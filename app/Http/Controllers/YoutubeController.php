<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    private function ytdlpPath(): string
    {
        return config('services.ytdlp.path', 'C:\\Users\\mfarr\\AppData\\Local\\Programs\\Python\\Python313\\Scripts\\yt-dlp.exe');
    }

    private function ffmpegPath(): string
    {
        return config('services.ffmpeg.path', 'C:\\Users\\mfarr\\AppData\\Local\\Microsoft\\WinGet\\Packages\\Gyan.FFmpeg_Microsoft.Winget.Source_8wekyb3d8bbwe\\ffmpeg-8.0.1-full_build\\bin\\ffmpeg.exe');
    }

    private function runYtdlp(string $args): array
    {
        $ffmpeg = $this->ffmpegPath();
        $ffmpegDir = dirname($ffmpeg);
        $cmd = escapeshellarg($this->ytdlpPath()) . ' --ffmpeg-location ' . escapeshellarg($ffmpegDir) . ' ' . $args;

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        // On Windows, we must pass a custom env with System32 in PATH to avoid
        // WinError 10106 (Python asyncio needs Winsock). On Linux, inherit the
        // system environment as-is to avoid stripping essential vars.
        $env = null;
        if (PHP_OS_FAMILY === 'Windows') {
            $systemRoot = getenv('SYSTEMROOT') ?: 'C:\\Windows';
            $system32 = $systemRoot . '\\System32';
            $ytdlpDir = dirname($this->ytdlpPath());

            $env = [
                'SYSTEMROOT' => $systemRoot,
                'SystemDrive' => getenv('SystemDrive') ?: 'C:',
                'ComSpec' => $system32 . '\\cmd.exe',
                'PATH' => implode(';', array_filter([
                    $ffmpegDir,
                    $ytdlpDir,
                    $system32,
                    $systemRoot,
                    $system32 . '\\Wbem',
                    getenv('PATH') ?: '',
                ])),
                'TEMP' => getenv('TEMP') ?: sys_get_temp_dir(),
                'TMP' => getenv('TMP') ?: sys_get_temp_dir(),
                'APPDATA' => getenv('APPDATA') ?: '',
                'LOCALAPPDATA' => getenv('LOCALAPPDATA') ?: '',
                'USERPROFILE' => getenv('USERPROFILE') ?: '',
                'HOMEDRIVE' => getenv('HOMEDRIVE') ?: 'C:',
                'HOMEPATH' => getenv('HOMEPATH') ?: '\\',
            ];
        }

        $process = proc_open($cmd, $descriptors, $pipes, null, $env);
        if (!is_resource($process)) {
            return ['success' => false, 'output' => '', 'error' => 'Gagal menjalankan yt-dlp.'];
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            \Illuminate\Support\Facades\Log::error('yt-dlp failed', [
                'exit_code' => $exitCode,
                'error' => $error,
                'output' => $output,
            ]);
        }

        return [
            'success' => $exitCode === 0,
            'output' => $output,
            'error' => $error,
        ];
    }

    public function index()
    {
        return view('tools.youtube');
    }

    public function getInfo(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/'],
        ]);

        $url = $request->input('url');

        $videoId = $this->extractVideoId($url);
        if (!$videoId) {
            return response()->json(['error' => 'URL YouTube tidak valid.'], 422);
        }

        $ytUrl = "https://www.youtube.com/watch?v={$videoId}";

        $result = $this->runYtdlp('--dump-json --no-download --no-warnings ' . escapeshellarg($ytUrl));

        if (!$result['success']) {
            $msg = 'Gagal mengambil info video.';
            if (config('app.debug') && $result['error']) {
                $msg .= ' ' . mb_substr($result['error'], 0, 300);
            }
            return response()->json(['error' => $msg], 500);
        }

        $data = json_decode($result['output'], true);
        if (!$data) {
            return response()->json(['error' => 'Gagal memproses info video.'], 500);
        }

        // Build format list from available formats
        $formats = $this->parseFormats($data['formats'] ?? []);

        return response()->json([
            'title' => $data['title'] ?? 'Unknown',
            'author' => $data['uploader'] ?? $data['channel'] ?? 'Unknown',
            'thumbnail' => $data['thumbnail'] ?? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
            'duration' => $data['duration_string'] ?? '',
            'video_id' => $videoId,
            'formats' => $formats,
        ]);
    }

    public function download(Request $request)
    {
        $request->validate([
            'video_id' => 'required|string|regex:/^[a-zA-Z0-9_-]{11}$/',
            'format_id' => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9_\-\+\*\/]+$/'],
        ]);

        $videoId = $request->input('video_id');
        $formatId = $request->input('format_id');
        $ytUrl = "https://www.youtube.com/watch?v={$videoId}";
        $needsMerge = str_contains($formatId, '+');

        if ($needsMerge) {
            // Download and merge on server, then serve the file
            return $this->downloadMerged($videoId, $formatId, $ytUrl);
        }

        // For single-stream formats, get direct URL
        $result = $this->runYtdlp('-f ' . escapeshellarg($formatId) . ' -g --no-warnings ' . escapeshellarg($ytUrl));

        if (!$result['success']) {
            return response()->json(['error' => 'Gagal mendapatkan link download.'], 500);
        }

        $urls = array_filter(explode("\n", trim($result['output'])));

        if (empty($urls)) {
            return response()->json(['error' => 'Link download tidak ditemukan.'], 500);
        }

        return response()->json([
            'download_url' => $urls[0],
        ]);
    }

    private function downloadMerged(string $videoId, string $formatId, string $ytUrl)
    {
        $tmpDir = storage_path('app/temp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        // Clean old temp files (older than 1 hour)
        foreach (glob($tmpDir . '/*') as $file) {
            if (filemtime($file) < time() - 3600) {
                @unlink($file);
            }
        }

        $outputFile = $tmpDir . DIRECTORY_SEPARATOR . $videoId . '.mp4';

        // Remove existing file if any
        if (file_exists($outputFile)) {
            @unlink($outputFile);
        }

        $result = $this->runYtdlp(
            '-f ' . escapeshellarg($formatId) .
            ' --merge-output-format mp4' .
            ' --postprocessor-args "ffmpeg:-c:v copy -c:a aac -b:a 192k"' .
            ' --no-warnings' .
            ' -o "' . addcslashes($outputFile, '"') . '"' .
            ' ' . escapeshellarg($ytUrl)
        );

        if (!$result['success']) {
            return response()->json(['error' => 'Gagal mendownload video.'], 500);
        }

        if (!file_exists($outputFile)) {
            return response()->json(['error' => 'File download tidak ditemukan.'], 500);
        }

        return response()->json([
            'download_url' => route('youtube.serve', ['file' => basename($outputFile)]),
        ]);
    }

    public function serveFile(Request $request)
    {
        $fileName = $request->query('file', '');

        // Validate filename to prevent path traversal
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $fileName)) {
            abort(404);
        }

        $filePath = storage_path('app/temp/' . $fileName);

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    private function parseFormats(array $rawFormats): array
    {
        $combined = [];
        $videoOnly = [];

        foreach ($rawFormats as $f) {
            $hasVideo = ($f['vcodec'] ?? 'none') !== 'none';
            $hasAudio = ($f['acodec'] ?? 'none') !== 'none';
            $height = $f['height'] ?? 0;
            $formatId = $f['format_id'] ?? '';
            $ext = $f['ext'] ?? '';
            $filesize = $f['filesize'] ?? $f['filesize_approx'] ?? 0;

            if (!$hasVideo || $height <= 0) continue;

            if ($hasAudio) {
                // Combined format (video + audio in one stream)
                if (!isset($combined[$height]) || $filesize > ($combined[$height]['filesize'] ?? 0)) {
                    $combined[$height] = [
                        'format_id' => $formatId,
                        'quality' => "{$height}p",
                        'label' => "{$height}p ({$ext})",
                        'type' => 'combined',
                        'filesize' => $filesize,
                        'height' => $height,
                    ];
                }
            } else {
                // Video-only — pick best codec per height (prefer mp4/avc1)
                $isMp4 = $ext === 'mp4';
                $existing = $videoOnly[$height] ?? null;
                if (!$existing || ($isMp4 && ($existing['ext'] ?? '') !== 'mp4') || $filesize > ($existing['filesize'] ?? 0)) {
                    $videoOnly[$height] = [
                        'format_id' => $formatId,
                        'height' => $height,
                        'ext' => $ext,
                        'filesize' => $filesize,
                    ];
                }
            }
        }

        $formats = [];

        // Build merged format options for resolutions not available as combined
        $allHeights = array_unique(array_merge(array_keys($combined), array_keys($videoOnly)));
        rsort($allHeights);

        foreach ($allHeights as $height) {
            if (isset($combined[$height])) {
                $formats[] = $combined[$height];
            } elseif (isset($videoOnly[$height])) {
                $vo = $videoOnly[$height];
                $formats[] = [
                    'format_id' => $vo['format_id'] . '+ba',
                    'quality' => "{$height}p",
                    'label' => "{$height}p (mp4, merge)",
                    'type' => 'merge',
                    'filesize' => $vo['filesize'],
                    'height' => $height,
                ];
            }
        }

        // Add audio-only option
        $formats[] = [
            'format_id' => 'ba',
            'quality' => 'Audio',
            'label' => 'Audio Only (M4A)',
            'type' => 'audio',
            'filesize' => 0,
            'height' => 0,
        ];

        return $formats;
    }

    private function extractVideoId(string $url): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
