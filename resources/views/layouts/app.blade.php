<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Photo Gallery')</title>
    <style>
        :root {
            --bg: #0f1115;
            --surface: #171a21;
            --surface-2: #1f232d;
            --border: #2a2f3a;
            --text: #e8eaf0;
            --muted: #8b93a7;
            --accent: #f5b94a;
            --danger: #ff6b6b;
            --ok: #5fd38d;
            --radius: 14px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.25rem; }

        /* Top bar */
        .topbar {
            position: sticky; top: 0; z-index: 20;
            background: rgba(15, 17, 21, .85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }
        .topbar .container { display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .brand { font-weight: 700; letter-spacing: .06em; font-size: 1.1rem; }
        .brand span { color: var(--accent); }
        .nav { display: flex; gap: .25rem; }
        .nav a {
            padding: .45rem 1rem; border-radius: 999px;
            color: var(--muted); font-size: .95rem; transition: .15s;
        }
        .nav a:hover { color: var(--text); background: var(--surface-2); }
        .nav a.active { color: #111; background: var(--accent); font-weight: 600; }

        main { padding: 2.5rem 0 4rem; }

        /* Alerts */
        .alert { padding: .8rem 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: .95rem; }
        .alert-ok { background: rgba(95, 211, 141, .12); color: var(--ok); border: 1px solid rgba(95, 211, 141, .35); }
        .alert-error { background: rgba(255, 107, 107, .12); color: var(--danger); border: 1px solid rgba(255, 107, 107, .35); }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .6rem 1.2rem; border-radius: 999px; border: 0;
            font-size: .95rem; font-weight: 600; cursor: pointer; transition: .15s;
        }
        .btn-primary { background: var(--accent); color: #111; }
        .btn-primary:hover { filter: brightness(1.1); }
        .btn-danger { background: rgba(255, 107, 107, .15); color: var(--danger); border: 1px solid rgba(255, 107, 107, .4); padding: .35rem .8rem; font-size: .8rem; }
        .btn-danger:hover { background: var(--danger); color: #111; }

        /* Page header */
        .page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .page-head h1 { font-size: 2rem; line-height: 1.2; }
        .page-head p { color: var(--muted); }

        /* Masonry gallery */
        .masonry { columns: 4 240px; column-gap: 1rem; }
        .card {
            position: relative; break-inside: avoid; margin-bottom: 1rem;
            border-radius: var(--radius); overflow: hidden;
            background: var(--surface); border: 1px solid var(--border);
        }
        .card .open { display: block; width: 100%; border: 0; background: none; cursor: zoom-in; overflow: hidden; }
        .card img { display: block; width: 100%; height: auto; transition: transform .4s ease; }
        .card:hover img { transform: scale(1.05); }
        .overlay {
            position: absolute; left: 0; right: 0; bottom: 0;
            padding: 3rem .9rem .8rem;
            background: linear-gradient(transparent, rgba(0, 0, 0, .88));
            opacity: 0; transform: translateY(8px); transition: .25s;
            pointer-events: none;
        }
        .overlay form { pointer-events: auto; }
        .card:hover .overlay, .card:focus-within .overlay { opacity: 1; transform: none; }
        @media (hover: none) { .overlay { opacity: 1; transform: none; } }
        .overlay h3 { font-size: 1rem; margin-bottom: .15rem; word-break: break-word; }
        .meta { color: #b9bfd0; font-size: .78rem; margin-bottom: .6rem; word-break: break-all; }

        /* Empty state */
        .empty { text-align: center; padding: 5rem 1rem; border: 2px dashed var(--border); border-radius: var(--radius); color: var(--muted); }
        .empty h2 { color: var(--text); margin-bottom: .5rem; }
        .empty .btn { margin-top: 1.2rem; }

        /* Lightbox */
        .lightbox { width: 100vw; height: 100vh; max-width: none; max-height: none; background: transparent; border: 0; color: var(--text); }
        .lightbox[open] { display: flex; align-items: center; justify-content: center; }
        .lightbox::backdrop { background: rgba(0, 0, 0, .92); }
        .lightbox figure { max-width: 92vw; max-height: 92vh; text-align: center; }
        .lightbox img { max-width: 92vw; max-height: 82vh; border-radius: 10px; }
        .lightbox figcaption { margin-top: .8rem; font-size: 1.05rem; }
        .lightbox .close {
            position: absolute; top: 1rem; right: 1.5rem; font-size: 2.2rem; line-height: 1;
            background: none; border: 0; color: var(--text); cursor: pointer;
        }

        /* Upload form */
        .form-card {
            max-width: 560px; margin: 0 auto; padding: 2rem;
            background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
        }
        .form-card h1 { font-size: 1.6rem; margin-bottom: .3rem; }
        .form-card .sub { color: var(--muted); margin-bottom: 1.5rem; }
        .field { margin-bottom: 1.4rem; }
        .field label.lbl { display: block; font-size: .9rem; font-weight: 600; margin-bottom: .45rem; }
        .field input[type="text"] {
            width: 100%; padding: .75rem 1rem; border-radius: 10px;
            background: var(--surface-2); border: 1px solid var(--border);
            color: var(--text); font-size: 1rem;
        }
        .field input[type="text"]:focus { outline: 2px solid var(--accent); border-color: transparent; }
        .is-invalid { border-color: var(--danger) !important; }
        .error { color: var(--danger); font-size: .88rem; margin-top: .4rem; }

        .dropzone {
            position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: .4rem; min-height: 190px; padding: 1rem; text-align: center;
            border: 2px dashed var(--border); border-radius: 12px; background: var(--surface-2);
            color: var(--muted); cursor: pointer; transition: .15s; overflow: hidden;
        }
        .dropzone:hover { border-color: var(--accent); }
        .dropzone strong { color: var(--text); word-break: break-all; }
        .dropzone input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .dropzone img { max-width: 100%; max-height: 220px; border-radius: 8px; }

        .actions { display: flex; gap: .75rem; align-items: center; }
        .link-muted { color: var(--muted); font-size: .95rem; }
        .link-muted:hover { color: var(--text); }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container">
            <a href="{{ route('photos.index') }}" class="brand">PHOTO<span>.</span>GALLERY</a>
            <nav class="nav">
                <a href="{{ route('photos.index') }}" class="{{ request()->routeIs('photos.index') ? 'active' : '' }}">Gallery</a>
                <a href="{{ route('photos.create') }}" class="{{ request()->routeIs('photos.create') ? 'active' : '' }}">Upload</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            @if (session('status'))
                <div class="alert alert-ok">{{ session('status') }}</div>
            @endif
            @error('delete')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>