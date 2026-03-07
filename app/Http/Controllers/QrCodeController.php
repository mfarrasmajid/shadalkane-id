<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function index()
    {
        return view('tools.qrcode');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2048',
            'size' => 'nullable|integer|min:100|max:1000',
            'color' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $content = $request->input('content');
        $size = $request->input('size', 300);
        $color = $request->input('color', '#000000');

        // Parse hex color to RGB
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));

        // Generate SVG QR code (no imagick dependency)
        $qrCode = QrCode::format('svg')
            ->size($size)
            ->color($r, $g, $b)
            ->errorCorrection('H')
            ->generate($content);

        $base64 = base64_encode($qrCode);

        return response()->json([
            'qr_image' => 'data:image/svg+xml;base64,' . $base64,
            'content' => $content,
        ]);
    }
}
