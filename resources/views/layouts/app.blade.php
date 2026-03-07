<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShadAlkane Tools')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon-shadalkane.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-shadalkane.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --red: #DC2626;
            --red-dark: #991B1B;
            --red-light: #FCA5A5;
            --purple: #7C3AED;
            --purple-dark: #5B21B6;
            --purple-light: #C4B5FD;
            --orange: #EA580C;
            --orange-dark: #C2410C;
            --orange-light: #FDBA74;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 25%, #7C3AED 50%, #5B21B6 70%, #EA580C 85%, #C2410C 100%);
            background-attachment: fixed;
            color: #fff;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 50%, rgba(220, 38, 38, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(124, 58, 237, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 60% 80%, rgba(234, 88, 12, 0.3) 0%, transparent 50%);
            z-index: 0;
            animation: bgPulse 8s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            0% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
        }

        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            text-decoration: none;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .navbar-brand span {
            background: linear-gradient(135deg, var(--red-light), var(--orange-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .nav-link.active {
            background: rgba(220, 38, 38, 0.4);
        }

        .btn-logout {
            background: rgba(220, 38, 38, 0.5);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #fff;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-logout:hover {
            background: rgba(220, 38, 38, 0.8);
        }

        /* Main content */
        .main-content {
            position: relative;
            z-index: 1;
            padding-top: 80px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--red), var(--purple));
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        /* Form inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--red-light);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(220, 38, 38, 0.3);
            border: 1px solid rgba(220, 38, 38, 0.5);
            color: var(--red-light);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.3);
            border: 1px solid rgba(34, 197, 94, 0.5);
            color: #86efac;
        }

        /* Page title */
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #fff, var(--red-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin-bottom: 32px;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-wrap: wrap;
                gap: 0;
            }

            .navbar-top {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .navbar-collapse {
                display: none;
                width: 100%;
                flex-direction: column;
                gap: 4px;
                padding-top: 12px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 12px;
            }

            .navbar-collapse.open {
                display: flex;
            }

            .navbar-nav {
                flex-direction: column;
                width: 100%;
                gap: 4px;
            }

            .nav-link {
                width: 100%;
                padding: 10px 16px;
            }

            .btn-logout {
                width: 100%;
                text-align: center;
                margin-top: 4px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .container {
                padding: 16px;
            }
        }

        @media (min-width: 769px) {
            .navbar-collapse {
                display: contents;
            }

            .navbar-top {
                display: contents;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Mobile menu */
        .burger-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.4rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .burger-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        @media (max-width: 768px) {
            .burger-toggle {
                display: block;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar">
        <div class="navbar-top">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <img src="{{ asset('images/icon-shadalkane-white.png') }}" alt="ShadAlkane">
                <span>ShadAlkane</span>
            </a>
            <button class="burger-toggle" onclick="document.getElementById('navCollapse').classList.toggle('open')" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="navbar-collapse" id="navCollapse">
            <ul class="navbar-nav">
                <li>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('youtube') }}" class="nav-link {{ request()->routeIs('youtube*') ? 'active' : '' }}">
                        <i class="fab fa-youtube"></i> YouTube
                    </a>
                </li>
                <li>
                    <a href="{{ route('qrcode') }}" class="nav-link {{ request()->routeIs('qrcode*') ? 'active' : '' }}">
                        <i class="fas fa-qrcode"></i> QR Code
                    </a>
                </li>
                <li>
                    <a href="{{ route('image-editor') }}" class="nav-link {{ request()->routeIs('image-editor*') ? 'active' : '' }}">
                        <i class="fas fa-image"></i> Image Editor
                    </a>
                </li>
            </ul>

            <form action="{{ route('logout') }}" method="POST" style="display:inline; width:100%;">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script>
        // CSRF token for all fetch requests
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function fetchWithCsrf(url, options = {}) {
            return fetch(url, {
                ...options,
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json',
                    ...options.headers,
                },
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
