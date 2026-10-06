<?php

function icon(string $name): string
{
    $paths = [
        'home' => '<path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1z"/>',
        'chat' => '<path d="M5 6.5h14a1 1 0 0 1 1 1V16a1 1 0 0 1-1 1H9l-4 3v-3H5a1 1 0 0 1-1-1V7.5a1 1 0 0 1 1-1z"/>',
        'bell' => '<path d="M6 16h12l-1.1-2A6 6 0 0 1 16 10V9a4 4 0 0 0-8 0v1a6 6 0 0 1-.9 4L6 16z"/><path d="M10 16a2 2 0 0 0 4 0"/>',
        'user' => '<circle cx="12" cy="8.5" r="3.2"/><path d="M5.5 19.5a6.5 6.5 0 0 1 13 0"/>',
        'search' => '<circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/>',
        'close' => '<path d="M7 7l10 10M17 7 7 17"/>',
        'heart' => '<path d="M12 19s-6.5-4.1-6.5-8.2A3.6 3.6 0 0 1 12 8a3.6 3.6 0 0 1 6.5 2.8C18.5 14.9 12 19 12 19z"/>',
        'comment' => '<path d="M5 6.5h14v8.5H8.5L5 18z"/>',
        'image' => '<rect x="4" y="5" width="16" height="14" rx="2"/><path d="m8 15 2.5-2.5L15 17"/><circle cx="15" cy="9" r="1"/>',
        'plus' => '<path d="M12 6v12M6 12h12"/>',
        'send' => '<path d="M4.5 12 19 5.5 14 19l-2.2-6.2z"/>',
        'back' => '<path d="M15 6 9 12l6 6"/>',
        'down' => '<path d="m6 9 6 6 6-6"/>',
        'more' => '<circle cx="6" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="18" cy="12" r="1"/>',
        'gear' => '<circle cx="12" cy="12" r="3"/><path d="M12 3.5v2.2M12 18.3v2.2M4.8 6.8l1.6 1.6M17.6 15.6l1.6 1.6M3.5 12h2.2M18.3 12h2.2M4.8 17.2l1.6-1.6M17.6 8.4l1.6-1.6"/>',
        'edit' => '<path d="M4 16.5V20h3.5L18 9.5 14.5 6z"/><path d="m13 7.5 3.5 3.5"/>',
        'check' => '<path d="m5 12 5 5L20 7"/>',
        'refresh' => '<path d="M20 12a8 8 0 1 1-2.2-5.5"/><path d="M20 4v5h-5"/>',
    ];
    $inner = $paths[$name] ?? $paths['plus'];
    return '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}
