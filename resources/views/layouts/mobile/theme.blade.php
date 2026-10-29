@php
    $scheme = $general_setting->mobile_theme_scheme ?? 'green';
    
    $colors = [
        'green' => [
            'primary' => '#32745e',
            'primary_light' => '#58907D',
            'primary_dark' => '#1e4b3d',
            'primary_rgb' => '50, 116, 94',
            'bg_body' => '#f0fdf9',
        ],
        'blue' => [
            'primary' => '#0d47a1',
            'primary_light' => '#1976d2',
            'primary_dark' => '#072a60',
            'primary_rgb' => '13, 71, 161',
            'bg_body' => '#eff6ff',
        ],
        'red' => [
            'primary' => '#b71c1c',
            'primary_light' => '#d32f2f',
            'primary_dark' => '#7f1212',
            'primary_rgb' => '183, 28, 28',
            'bg_body' => '#fef2f2',
        ],
        'purple' => [
            'primary' => '#4a148c',
            'primary_light' => '#7b1fa2',
            'primary_dark' => '#2c0b54',
            'primary_rgb' => '74, 20, 140',
            'bg_body' => '#faf5ff',
        ],
        'orange' => [
            'primary' => '#e65100',
            'primary_light' => '#f57c00',
            'primary_dark' => '#993600',
            'primary_rgb' => '230, 81, 0',
            'bg_body' => '#fff8f1',
        ],
        'rose' => [
            'primary' => '#be185d',
            'primary_light' => '#ec4899',
            'primary_dark' => '#831843',
            'primary_rgb' => '190, 24, 93',
            'bg_body' => '#fff5f7',
        ],
        'dark' => [
            'primary' => '#0f172a',
            'primary_light' => '#334155',
            'primary_dark' => '#020617',
            'primary_rgb' => '15, 23, 42',
            'bg_body' => '#070b14',
        ],
    ];

    $t = $colors[$scheme] ?? $colors['green'];
    $isDark = ($scheme === 'dark');

    // Share variables globally
    view()->share('isDark', $isDark);
    view()->share('t', $t);
    view()->share('scheme', $scheme);
@endphp
