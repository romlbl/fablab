@props(['titre' => 'Connexion — Fablab'])

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titre }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-slate-800 min-h-screen min-h-dvh py-6 px-4 md:py-16 selection:bg-brand-cyan selection:text-white flex flex-col justify-center items-center">
    {{ $slot }}
    @livewireScripts
</body>
</html>
