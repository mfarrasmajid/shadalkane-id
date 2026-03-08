@extends('layouts.app')

@section('title', 'YouTube Downloader - ShadAlkane Tools')

@section('styles')
<style>
    .yt-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .url-input-group {
        display: flex;
        gap: 12px;
        margin-bottom: 32px;
    }

    .url-input-group .form-input {
        flex: 1;
    }

    .video-preview {
        display: none;
        margin-bottom: 32px;
    }

    .video-preview.show {
        display: block;
    }

    .video-card {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 16px;
        overflow: hidden;
    }

    .video-thumbnail {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
    }

    .video-info {
        padding: 24px;
    }

    .video-info h3 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 8px;
        line-height: 1.4;
        color: var(--dark);
    }

    .video-info .author {
        color: var(--gray-500);
        font-size: 0.9rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .formats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }

    .format-btn {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        color: var(--gray-700);
        padding: 14px 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .format-btn:hover {
        background: var(--dark);
        border-color: var(--dark);
        color: var(--white);
        transform: translateY(-1px);
    }

    .format-btn .quality {
        font-weight: 700;
    }

    .format-btn .dl-icon {
        opacity: 0;
        transition: all 0.2s ease;
    }

    .format-btn:hover .dl-icon {
        opacity: 1;
    }

    .loading-state {
        display: none;
        text-align: center;
        padding: 40px;
    }

    .loading-state.show {
        display: block;
    }

    .loading-state .spinner {
        width: 40px;
        height: 40px;
        border-width: 3px;
    }

    .loading-state p {
        margin-top: 16px;
        color: var(--gray-500);
    }

    .error-message {
        display: none;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        text-align: center;
    }

    .error-message.show {
        display: block;
    }

    .instructions {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 24px;
        margin-top: 24px;
    }

    .instructions h4 {
        font-size: 1rem;
        margin-bottom: 12px;
        color: var(--dark);
    }

    .instructions ol {
        color: var(--gray-500);
        font-size: 0.9rem;
        line-height: 1.8;
        padding-left: 20px;
    }

    @media (max-width: 768px) {
        .url-input-group {
            flex-direction: column;
        }

        .video-thumbnail {
            height: 200px;
        }

        .formats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="yt-container">
    <h1 class="page-title">
        <i class="fab fa-youtube"></i> YouTube Downloader
    </h1>
    <p class="page-subtitle">Paste link video YouTube untuk mendapatkan opsi download.</p>

    <div class="url-input-group">
        <input type="url" id="youtubeUrl" class="form-input" placeholder="https://www.youtube.com/watch?v=..." autocomplete="off">
        <button onclick="getVideoInfo()" class="btn-primary" id="fetchBtn">
            <i class="fas fa-search"></i> Cari
        </button>
    </div>

    <div class="error-message" id="errorMsg"></div>

    <div class="loading-state" id="loadingState">
        <div class="spinner"></div>
        <p>Mengambil info video...</p>
    </div>

    <div class="video-preview" id="videoPreview">
        <div class="video-card">
            <img id="videoThumbnail" class="video-thumbnail" src="" alt="Thumbnail">
            <div class="video-info">
                <h3 id="videoTitle"></h3>
                <div class="author">
                    <i class="fas fa-user"></i>
                    <span id="videoAuthor"></span>
                </div>
                <div class="author" style="display:none; margin-top:-12px;">
                    <i class="fas fa-clock"></i>
                    <span id="videoDuration"></span>
                </div>
                <h4 style="margin-bottom: 12px; font-size: 0.95rem;">Pilih Resolusi Download:</h4>
                <div class="formats-grid" id="formatsGrid"></div>
            </div>
        </div>
    </div>

    <div class="instructions">
        <h4><i class="fas fa-info-circle"></i> Cara Penggunaan</h4>
        <ol>
            <li>Copy link video dari YouTube</li>
            <li>Paste link pada kolom input di atas</li>
            <li>Klik tombol "Cari" untuk mendapatkan info video</li>
            <li>Pilih resolusi yang diinginkan</li>
            <li>Klik tombol download untuk mengunduh video</li>
        </ol>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentVideoId = null;

async function getVideoInfo() {
    const url = document.getElementById('youtubeUrl').value.trim();
    if (!url) {
        showError('Silakan masukkan URL YouTube.');
        return;
    }

    // Reset state
    hideError();
    document.getElementById('videoPreview').classList.remove('show');
    document.getElementById('loadingState').classList.add('show');
    document.getElementById('fetchBtn').disabled = true;

    try {
        const response = await fetchWithCsrf('{{ route("youtube.info") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: url }),
        });

        const data = await response.json();

        if (!response.ok) {
            showError(data.error || 'Terjadi kesalahan.');
            return;
        }

        currentVideoId = data.video_id;

        // Display video info
        document.getElementById('videoThumbnail').src = data.thumbnail;
        document.getElementById('videoTitle').textContent = data.title;
        document.getElementById('videoAuthor').textContent = data.author;

        // Show duration if available
        const durationEl = document.getElementById('videoDuration');
        if (data.duration) {
            durationEl.textContent = data.duration;
            durationEl.parentElement.style.display = 'flex';
        } else {
            durationEl.parentElement.style.display = 'none';
        }

        // Render formats
        const grid = document.getElementById('formatsGrid');
        grid.innerHTML = '';

        data.formats.forEach(format => {
            const btn = document.createElement('button');
            btn.className = 'format-btn';
            const sizeText = format.filesize ? ` (${formatBytes(format.filesize)})` : '';
            const icon = format.type === 'audio' ? 'fa-music' : 'fa-video';
            const mergeTag = format.type === 'merge' ? ' <span style="font-size:0.7rem;opacity:0.6;margin-left:4px">HD</span>' : '';
            btn.innerHTML = `
                <span class="quality"><i class="fas ${icon}" style="margin-right:6px;opacity:0.6"></i>${format.label}${sizeText}${mergeTag}</span>
                <i class="fas fa-download dl-icon"></i>
            `;
            btn.onclick = () => downloadVideo(format.format_id, format.label, btn, format.type === 'merge');
            grid.appendChild(btn);
        });

        document.getElementById('videoPreview').classList.add('show');
    } catch (err) {
        showError('Gagal mengambil info video. Periksa koneksi internet.');
    } finally {
        document.getElementById('loadingState').classList.remove('show');
        document.getElementById('fetchBtn').disabled = false;
    }
}

async function downloadVideo(formatId, label, btn, isMerge = false) {
    const originalHtml = btn.innerHTML;
    const loadingText = isMerge ? 'Mendownload & merge...' : 'Memproses...';
    btn.innerHTML = `<span class="quality"><i class="fas fa-spinner fa-spin"></i> ${loadingText}</span>`;
    btn.disabled = true;

    try {
        const response = await fetchWithCsrf('{{ route("youtube.download") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ video_id: currentVideoId, format_id: formatId }),
        });

        const data = await response.json();

        if (!response.ok) {
            showError(data.error || 'Gagal mendapatkan link download.');
            return;
        }

        if (data.download_url) {
            window.open(data.download_url, '_blank', 'noopener,noreferrer');
        }
    } catch (err) {
        showError('Gagal mendapatkan link download.');
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function showError(msg) {
    const el = document.getElementById('errorMsg');
    el.textContent = msg;
    el.classList.add('show');
}

function hideError() {
    document.getElementById('errorMsg').classList.remove('show');
}

// Allow enter key
document.getElementById('youtubeUrl').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') getVideoInfo();
});
</script>
@endsection
