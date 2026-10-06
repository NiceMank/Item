<?php
require_once __DIR__ . '/icons.php';

function render_tabbar(string $current): void
{
    $tabs = [
        'home' => ['../home/index.php', 'home', 'Accueil'],
        'chat' => ['../chats/discussion.php', 'chat', 'Messages'],
        'bell' => ['#', 'bell', 'Alertes'],
        'profile' => ['../profile/profile.php', 'user', 'Profil'],
    ];
    echo '<nav class="tabbar" aria-label="Navigation">';
    foreach ($tabs as $key => [$href, $icon, $label]) {
        $active = $key === $current ? ' active' : '';
        $extra = $key === 'bell' ? ' js-notifs' : '';
        echo '<a class="tab' . $active . $extra . '" href="' . h($href) . '">';
        echo icon($icon);
        echo '<span>' . h($label) . '</span>';
        echo '</a>';
    }
    echo '</nav>';
}
