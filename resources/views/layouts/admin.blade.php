<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') | Admin Panel</title>
    <meta name="robots" content="noindex,nofollow">

    @vite([
        'resources/css/admin.css',
        'resources/js/admin.js'
    ])
</head>

<body>

<div class="admin-wrapper">

    <aside class="admin-sidebar">

        <div class="admin-brand">
            <img src="/uploads/flyoneers-logo.png" alt="Flyoneers" class="admin-brand-logo">
        </div>

        <nav class="admin-menu">

            <a href="{{ route('admin.pages.dashboard') }}" class="admin-menu-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.home-sections.index') }}" class="admin-menu-link">
                <i class="bi bi-layout-text-window"></i>
                <span>Ana Sayfa</span>
            </a>

            <a
    href="{{ route('admin.pages.index') }}"
    class="admin-menu-link"
>
    <i class="bi bi-file-earmark-text"></i>
    <span>Sayfalar</span>
</a>

            <a href="{{ route('admin.header-menu.index') }}" class="admin-menu-link">
                <i class="bi bi-list"></i>
                <span>Header Menu</span>
            </a>

            <a href="{{ route('admin.footer-menu.index') }}" class="admin-menu-link">
                <i class="bi bi-layout-text-sidebar-reverse"></i>
                <span>Footer Menu</span>
            </a>

            <a href="{{ route('admin.site-assets.edit') }}" class="admin-menu-link">
                <i class="bi bi-gear"></i>
                <span>Ayarlar</span>
            </a>

            <a href="{{ route('admin.filemanager.index') }}" class="admin-menu-link">
                <i class="bi bi-images"></i>
                <span>Dosya Yöneticisi</span>
            </a>

            <a href="{{ route('admin.winners.index') }}" class="admin-menu-link">
                <i class="bi bi-trophy"></i>
                <span>Kazananlar</span>
            </a>

        </nav>

    </aside>

    <div class="admin-main">

        <header class="admin-topbar">

            <div class="admin-topbar-title">Yönetim Paneli</div>

            <div class="admin-topbar-user">
                <i class="bi bi-person-circle"></i>
                <span>{{ auth()->user()->username }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Çıkış Yap</button>
                </form>
            </div>

        </header>

        <main class="admin-content">
            @yield('content')
        </main>

    </div>

</div>

</body>

</html>
