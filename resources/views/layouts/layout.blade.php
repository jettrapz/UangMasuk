<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ledger' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    @yield('content')
</body>

<script>
    document.addEventListener('click', (event) => {
        // 1. Buka modal saat elemen dengan [data-modal-open] diklik
        const openTrigger = event.target.closest('[data-modal-open]');
        if (openTrigger) {
            const targetId = openTrigger.dataset.modalOpen;
            const modalElement = document.getElementById(targetId);
            if (modalElement && typeof modalElement.showModal === 'function') {
                modalElement.showModal();
            }
        }

        // 2. Tutup modal saat elemen dengan [data-modal-close] diklik
        const closeTrigger = event.target.closest('[data-modal-close]');
        if (closeTrigger) {
            const modalElement = closeTrigger.closest('dialog');
            if (modalElement && typeof modalElement.close === 'function') {
                modalElement.close();
            }
        }

        // 3. Tutup modal jika area backdrop (luar modal) diklik
        if (event.target.tagName === 'DIALOG' && event.target.classList.contains('confirm-modal')) {
            event.target.close();
        }
    });
</script>

</html>