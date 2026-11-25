<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="">
        <meta name="description" content="ДомГид — каталог квартир, домов и коммерческой недвижимости. Найдите объект под ваш бюджет.">

        <script>
            (() => {
                let stored = null;

                try {
                    stored = localStorage.getItem('theme');
                } catch (error) {
                    stored = null;
                }

                const requested = new URLSearchParams(window.location.search).get('theme');
                const dark = requested === 'dark'
                    || (requested !== 'light' && (
                        stored === 'dark'
                        || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)
                    ));

                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            })();
        </script>

        <title inertia>{{ config('app.name', 'ДомГид') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="bg-surface-page font-sans text-content antialiased">
        @inertia
    </body>
</html>
