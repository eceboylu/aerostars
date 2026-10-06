{{-- Admin paneli ana iskeleti: sol menü, üst bar ve içerik alanı.
     Tüm admin sayfaları bu dosyayı @extends ile kullanır. --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') | Admin Panel</title>

    @vite([
        'resources/css/admin.css',
        'resources/js/admin.js'
    ])
</head>

<body>

<div class="admin-wrapper">

    <aside class="admin-sidebar">

        <div class="admin-brand">
            <span class="admin-brand-logo">A</span>
            <span>AirStars Admin</span>
        </div>

        <nav class="admin-menu">

            <a href="#" class="admin-menu-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="admin-menu-link">
                <i class="bi bi-folder"></i>
                <span>Kategoriler</span>
            </a>

            <a
    href="{{ route('admin.pages.index') }}"
    class="admin-menu-link"
>
    <i class="bi bi-file-earmark-text"></i>
    <span>Sayfalar</span>
</a>

            <a href="#" class="admin-menu-link">
                <i class="bi bi-search"></i>
                <span>SEO</span>
            </a>

            <a href="#" class="admin-menu-link">
                <i class="bi bi-gear"></i>
                <span>Ayarlar</span>
            </a>

        </nav>

    </aside>

    <div class="admin-main">

        <header class="admin-topbar">

            <div class="admin-topbar-title">Yönetim Paneli</div>

            <div class="admin-topbar-user">
                <i class="bi bi-person-circle"></i>
                <span>Admin</span>
            </div>

        </header>

        <main class="admin-content">
            @yield('content')
        </main>

    </div>

</div>

</body>

</html>
