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
        color: var(--dark);
    }

    .welcome-section p {
        color: var(--gray-500);
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
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 16px;
        padding: 32px;
        transition: all 0.2s ease;
        text-decoration: none;
        color: var(--gray-700);
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
        background: var(--dark);
    }

    .tool-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        border-color: var(--gray-300);
    }

    .tool-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
        background: var(--gray-100);
        color: var(--dark);
    }

    .tool-card h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--dark);
    }

    .tool-card p {
        color: var(--gray-500);
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .tool-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--dark);
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .tool-card:hover .tool-link {
        gap: 10px;
    }

    .user-greeting {
        font-size: 1rem;
        color: var(--gray-400);
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
