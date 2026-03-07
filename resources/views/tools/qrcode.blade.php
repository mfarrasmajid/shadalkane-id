@extends('layouts.app')

@section('title', 'QR Code Generator - ShadAlkane Tools')

@section('styles')
<style>
    .qr-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .qr-form-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 32px;
        margin-bottom: 24px;
    }

    .settings-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .qr-result {
        display: none;
        text-align: center;
    }

    .qr-result.show {
        display: block;
    }

    .qr-result-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 40px;
    }

    .qr-image-wrapper {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        display: inline-block;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .qr-image-wrapper img {
        display: block;
        max-width: 100%;
    }

    .qr-content-text {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.85rem;
        margin-bottom: 20px;
        word-break: break-all;
    }

    .download-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .color-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .color-input-wrapper input[type="color"] {
        width: 48px;
        height: 48px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        cursor: pointer;
        background: transparent;
        padding: 4px;
    }

    .color-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }

    .size-options {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .size-option {
        padding: 8px 16px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Inter', sans-serif;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .size-option:hover, .size-option.active {
        background: rgba(124, 58, 237, 0.4);
        border-color: rgba(124, 58, 237, 0.6);
        color: #fff;
    }

    .loading-overlay {
        display: none;
        text-align: center;
        padding: 40px;
    }

    .loading-overlay.show {
        display: block;
    }

    @media (max-width: 768px) {
        .settings-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="qr-container">
    <h1 class="page-title">
        <i class="fas fa-qrcode"></i> QR Code Generator
    </h1>
    <p class="page-subtitle">Generate QR code dari URL atau teks, dan download dalam format PNG.</p>

    <div class="qr-form-card">
        <div class="form-group">
            <label class="form-label">URL atau Teks</label>
            <input type="text" id="qrContent" class="form-input" placeholder="https://example.com atau teks apapun..." autocomplete="off">
        </div>

        <div class="settings-row">
            <div class="form-group">
                <label class="form-label">Ukuran QR Code</label>
                <div class="size-options">
                    <button class="size-option" data-size="200" onclick="selectSize(this)">200px</button>
                    <button class="size-option active" data-size="300" onclick="selectSize(this)">300px</button>
                    <button class="size-option" data-size="400" onclick="selectSize(this)">400px</button>
                    <button class="size-option" data-size="500" onclick="selectSize(this)">500px</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Warna QR Code</label>
                <div class="color-input-wrapper">
                    <input type="color" id="qrColor" value="#000000">
                    <span class="color-label" id="colorLabel">#000000</span>
                </div>
            </div>
        </div>

        <button onclick="generateQR()" class="btn-primary" id="generateBtn" style="width: 100%;">
            <i class="fas fa-magic"></i> Generate QR Code
        </button>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner" style="width: 40px; height: 40px; border-width: 3px;"></div>
        <p style="margin-top: 16px; color: rgba(255, 255, 255, 0.6);">Generating QR Code...</p>
    </div>

    <div class="qr-result" id="qrResult">
        <div class="qr-result-card">
            <div class="qr-image-wrapper">
                <img id="qrImage" src="" alt="QR Code">
            </div>
            <p class="qr-content-text">
                <strong>Konten:</strong> <span id="qrContentText"></span>
            </p>
            <div class="download-actions">
                <button onclick="downloadQR()" class="btn-primary">
                    <i class="fas fa-download"></i> Download PNG
                </button>
                <button onclick="resetQR()" class="btn-secondary">
                    <i class="fas fa-redo"></i> Buat Baru
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedSize = 300;
let currentQRImage = null;

function selectSize(btn) {
    document.querySelectorAll('.size-option').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedSize = parseInt(btn.dataset.size);
}

document.getElementById('qrColor').addEventListener('input', (e) => {
    document.getElementById('colorLabel').textContent = e.target.value;
});

async function generateQR() {
    const content = document.getElementById('qrContent').value.trim();
    if (!content) {
        alert('Silakan masukkan URL atau teks.');
        return;
    }

    const color = document.getElementById('qrColor').value;

    document.getElementById('qrResult').classList.remove('show');
    document.getElementById('loadingOverlay').classList.add('show');
    document.getElementById('generateBtn').disabled = true;

    try {
        const response = await fetchWithCsrf('{{ route("qrcode.generate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                content: content,
                size: selectedSize,
                color: color,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            alert(data.error || 'Gagal generate QR code.');
            return;
        }

        currentQRImage = data.qr_image;
        document.getElementById('qrImage').src = data.qr_image;
        document.getElementById('qrContentText').textContent = data.content;
        document.getElementById('qrResult').classList.add('show');

    } catch (err) {
        alert('Terjadi kesalahan. Periksa koneksi internet.');
    } finally {
        document.getElementById('loadingOverlay').classList.remove('show');
        document.getElementById('generateBtn').disabled = false;
    }
}

function downloadQR() {
    if (!currentQRImage) return;

    // Convert SVG to PNG using canvas
    const img = new Image();
    img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = img.naturalWidth || 300;
        canvas.height = img.naturalHeight || 300;
        const ctx = canvas.getContext('2d');
        // White background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0);

        const link = document.createElement('a');
        link.download = 'qrcode.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    };
    img.src = currentQRImage;
}

function resetQR() {
    document.getElementById('qrContent').value = '';
    document.getElementById('qrResult').classList.remove('show');
    currentQRImage = null;
}

// Allow enter key
document.getElementById('qrContent').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') generateQR();
});
</script>
@endsection
