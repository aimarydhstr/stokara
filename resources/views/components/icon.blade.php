@props(['name' => 'box', 'size' => 20])
@php
$paths = [
'box' => '<path d="m21 8-9 5-9-5M12 13v9"/>
<path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5z"/>
<path d="m7.5 5.3 9 4.5"/>',
'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/>
<rect x="14" y="3" width="7" height="7" rx="1.5"/>
<rect x="3" y="14" width="7" height="7" rx="1.5"/>
<rect x="14" y="14" width="7" height="7" rx="1.5"/>',
'layers' => '<path d="m12 3 10 5-10 5L2 8zM2 12l10 5 10-5M2 16l10 5 10-5"/>',
'folder' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v10H3z"/>',
'in' => '<path d="M12 3v12m-5-5 5 5 5-5M4 15v5h16v-5"/>',
'out' => '<path d="M12 15V3m-5 5 5-5 5 5M4 15v5h16v-5"/>',
'history' => '<path d="M3 11a9 9 0 1 1 2.5 7M3 4v7h7M12 7v5l3 2"/>',
'search' => '<circle cx="10.5" cy="10.5" r="6.5"/>
<path d="m16 16 4.5 4.5"/>',
'plus' => '<path d="M12 5v14M5 12h14"/>',
'chevron' => '<path d="m9 5 7 7-7 7"/>',
'down' => '<path d="m6 9 6 6 6-6"/>',
'arrow' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
'arrow-up' => '<path d="M7 17 17 7M7 7h10v10"/>',
'alert' => '<path d="m12 3 10 18H2zM12 9v5m0 3v.1"/>',
'check' => '<path d="m5 12 4 4L19 6"/>',
'check-circle' => '<circle cx="12" cy="12" r="9"/>
<path d="m8 12 3 3 5-6"/>',
'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/>
<path d="M16 3v4M8 3v4M3 11h18"/>',
'logout' => '<path d="M9 21H4V3h5m6 4 5 5-5 5M9 12h11"/>',
'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
'help' => '<circle cx="12" cy="12" r="9"/>
<path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 2-2.5 2-2.5 4m0 3v.1"/>',
'edit' => '<path d="m15 5 4 4M4 20l4-1L21 6l-4-4L4 15z"/>',
'eye' => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7"/>
<circle cx="12" cy="12" r="3"/>',
'trash' => '<path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7"/>',
'filter' => '<path d="M4 7h16M7 12h10M10 17h4"/>
<circle cx="8" cy="7" r="2" fill="currentColor" stroke="none"/>
<circle cx="15" cy="12" r="2" fill="currentColor" stroke="none"/>',
'file' => '<path d="M14 2H5v20h14V7zM14 2v5h5M8 12h8M8 16h6"/>',
'back' => '<path d="M19 12H5m5-5-5 5 5 5"/>',
'lock' => '<rect x="5" y="10" width="14" height="11" rx="2"/>
<path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/>',
'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
'spark' => '<path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5z"/>',
'wallet' => '<rect x="3" y="5" width="18" height="15" rx="2"/>
<path d="M3 8h18m-5 5h5v4h-5zM6 5V3h12v2"/>',
];
@endphp
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {{ $attributes }}>{!! $paths[$name] ?? $paths['box'] !!}</svg>