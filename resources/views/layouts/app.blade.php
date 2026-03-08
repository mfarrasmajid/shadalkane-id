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
            --dark: #1A1A1A;
            --dark-secondary: #2D2D2D;
            --gray-700: #374151;
            --gray-500: #6B7280;
            --gray-400: #9CA3AF;
            --gray-300: #D1D5DB;
            --gray-200: #E5E7EB;
            --gray-100: #F3F4F6;
            --gray-50: #F9FAFB;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: var(--gray-50);
            color: var(--gray-700);
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
            background: var(--dark);
            border-bottom: 1px solid var(--dark-secondary);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            text-decoration: none;
            color: var(--white);
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
            color: var(--white);
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
        }

        .nav-link {
            color: var(--gray-400);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link:hover {
            background: var(--dark-secondary);
            color: var(--white);
        }

        .nav-link.active {
            background: var(--white);
            color: var(--dark);
        }

        .btn-logout {
            background: transparent;
            border: 1px solid var(--gray-500);
            color: var(--gray-400);
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-logout:hover {
            background: var(--white);
            color: var(--dark);
            border-color: var(--white);
        }

        .logout-form {
            display: inline;
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
            background: var(--dark);
            color: var(--white);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--dark-secondary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: var(--gray-100);
            transform: translateY(-1px);
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
            color: var(--gray-700);
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid var(--gray-300);
            background: var(--white);
            color: var(--dark);
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
        }

        .form-input::placeholder {
            color: var(--gray-400);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--dark);
            box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
            background: var(--white);
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }

        .alert-success {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #166534;
        }

        /* Page title */
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--dark);
        }

        .page-subtitle {
            color: var(--gray-500);
            font-size: 1rem;
            margin-bottom: 32px;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray-300);
            border-radius: 50%;
            border-top-color: var(--dark);
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
                border-top: 1px solid var(--dark-secondary);
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

            .logout-form {
                width: 100%;
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
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* Mobile menu */
        .burger-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        .burger-toggle:hover {
            background: var(--dark-secondary);
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
                <img src="{{ asset('images/icon-shadalkane.png') }}" alt="ShadAlkane">
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

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
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
