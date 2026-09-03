@php
    $scheme = $general_setting->mobile_theme_scheme ?? 'green';
    
    // Forced Light Mode Colors
    $colors = [
        'green' => [
            'primary' => '#32745e',
            'primary_light' => '#58907D',
            'bg_body' => '#f0fdf9',
        ],
        'blue' => [
            'primary' => '#0d47a1',
            'primary_light' => '#1976d2',
            'bg_body' => '#eff6ff',
        ],
        'red' => [
            'primary' => '#b71c1c',
            'primary_light' => '#d32f2f',
            'bg_body' => '#fef2f2',
        ],
        'purple' => [
            'primary' => '#4a148c',
            'primary_light' => '#7b1fa2',
            'bg_body' => '#faf5ff',
        ],
        'orange' => [
            'primary' => '#e65100',
            'primary_light' => '#f57c00',
            'bg_body' => '#fff8f1',
        ],
    ];

    // Default to green if scheme is dark or invalid
    $t = (isset($colors[$scheme]) && $scheme !== 'dark') ? $colors[$scheme] : $colors['green'];
    $isDark = false;

    // Share variables globally
    view()->share('isDark', $isDark);
    view()->share('t', $t);
    view()->share('scheme', $scheme);
@endphp
