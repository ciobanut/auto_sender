<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover" />

<script>
    (function () {
        var mode = localStorage.getItem('theme') || localStorage.getItem('theme:mode');
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (mode === 'dark' || (!mode && prefersDark)) {
            document.documentElement.classList.add('dark');
        }
    })();
</script>

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
