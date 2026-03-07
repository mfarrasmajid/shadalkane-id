@extends('layouts.app')

@section('title', 'Dashboard - ShadAlkane Tools')

@section('styles')
<style>
    .welcome-section {
        text-align: center;
        padding: 40px 0;
    }

    .welcome-section h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 12px;
        background: linear-gradient(135deg, #fff, #FCA5A5, #FDBA74);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-section p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.1rem;
        max-width: 500px;
        margin: 0 auto;
    }

    .tools-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 48px;
    }

    .tool-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 32px;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        text-decoration: none;
        color: #fff;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .tool-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: 20px 20px 0 0;
    }

    .tool-card.youtube::before { background: linear-gradient(90deg, #DC2626, #EF4444); }
    .tool-card.qrcode::before { background: linear-gradient(90deg, #7C3AED, #8B5CF6); }
    .tool-card.image::before { background: linear-gradient(90deg, #EA580C, #F97316); }

    .tool-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.25);
    }

    .tool-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    .tool-card.youtube .tool-icon {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.3), rgba(239, 68, 68, 0.1));
        color: #FCA5A5;
    }

    .tool-card.qrcode .tool-icon {
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.3), rgba(139, 92, 246, 0.1));
        color: #C4B5FD;
    }

    .tool-card.image .tool-icon {
        background: linear-gradient(135deg, rgba(234, 88, 12, 0.3), rgba(249, 115, 22, 0.1));
        color: #FDBA74;
    }

    .tool-card h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .tool-card p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .tool-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .tool-card:hover .tool-link {
        color: #fff;
        gap: 10px;
    }

    .user-greeting {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 4px;
    }

    @media (max-width: 768px) {
        .welcome-section h1 {
            font-size: 1.8rem;
        }

        .tools-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <p class="user-greeting">Selamat datang, {{ Auth::user()->name }}!</p>
    <h1>ShadAlkane Tools</h1>
    <p>Portal tools yang memudahkan pekerjaan kamu sehari-hari.</p>
</div>

<div class="tools-grid">
    <a href="{{ route('youtube') }}" class="tool-card youtube">
        <div class="tool-icon">
            <i class="fab fa-youtube"></i>
        </div>
        <h3>YouTube Downloader</h3>
        <p>Download video YouTube dengan berbagai resolusi. Paste link dan pilih kualitas yang diinginkan.</p>
        <span class="tool-link">
            Buka Tool <i class="fas fa-arrow-right"></i>
        </span>
    </a>

    <a href="{{ route('qrcode') }}" class="tool-card qrcode">
        <div class="tool-icon">
            <i class="fas fa-qrcode"></i>
        </div>
        <h3>QR Code Generator</h3>
        <p>Generate QR code dari URL atau teks apapun. Download hasilnya dalam format PNG.</p>
        <span class="tool-link">
            Buka Tool <i class="fas fa-arrow-right"></i>
        </span>
    </a>

    <a href="{{ route('image-editor') }}" class="tool-card image">
        <div class="tool-icon">
            <i class="fas fa-image"></i>
        </div>
        <h3>Image Editor</h3>
        <p>Edit gambar dengan fitur crop, resize, filter, dan lainnya. Simple dan mudah digunakan.</p>
        <span class="tool-link">
            Buka Tool <i class="fas fa-arrow-right"></i>
        </span>
    </a>
</div>
@endsection
