<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Giriş | Flyoneers</title>
<meta name="robots" content="noindex,nofollow">
@vite(['resources/css/site.css'])
<style>
    body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .login-card { width: 100%; max-width: 380px; margin: 16px; padding: 32px; background: var(--panel); border: 1px solid var(--line); border-radius: 14px; }
    .login-card .brand { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; color: var(--ink); font-family: var(--f-ui); font-weight: 700; }
    .login-card .brand img { height: 44px; width: auto; display: block; filter: saturate(0.8) brightness(0.95) hue-rotate(-6deg); }
    .login-card h1 { font-size: 1.25rem; margin: 0 0 20px; color: var(--ink); font-family: var(--f-ui); }
    .login-field { margin-bottom: 16px; }
    .login-field label { display: block; margin-bottom: 6px; color: var(--ink-2); font-family: var(--f-ui); font-size: 0.85rem; }
    .login-field input { width: 100%; padding: 10px 12px; background: var(--paper); border: 1px solid var(--line); border-radius: 8px; color: var(--ink); font-family: var(--f-ui); }
    .login-field input:focus { outline: 2px solid var(--focus); outline-offset: 1px; }
    .login-error { margin-bottom: 16px; padding: 10px 12px; border-radius: 8px; background: rgba(201, 20, 20, 0.12); border: 1px solid rgba(201, 20, 20, 0.4); color: #ffb4b4; font-family: var(--f-ui); font-size: 0.85rem; }
    .login-submit { width: 100%; padding: 11px; border: none; border-radius: 8px; background: var(--signal); color: var(--on-signal); font-family: var(--f-ui); font-weight: 700; cursor: pointer; }
    .login-submit:hover { background: var(--signal-hover); }
</style>
</head>
<body>
<div class="login-card">
    <div class="brand">
        <img src="/uploads/flyoneers-logo.png" alt="Flyoneers">
    </div>

    <h1>Giriş Yap</h1>

    @error('username')
        <div class="login-error">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('admin.login.attempt') }}">
        @csrf

        <div class="login-field">
            <label for="username">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
        </div>

        <div class="login-field">
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="login-submit">Giriş Yap</button>
    </form>
</div>
</body>
</html>
