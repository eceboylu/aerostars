@if (! empty($page->icon))
    <img class="family-icon" src="{{ $page->icon }}" alt="" loading="lazy" decoding="async">
@else
    @switch(basename($page->slug ?? ''))
        @case('airport-ground-services')
            <svg class="family-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><path d="M5 50h54"/><path d="M9 44v-7a3 3 0 0 1 3-3l4.4-7.6A3 3 0 0 1 19 25h14a3 3 0 0 1 2.6 1.4L40 34a3 3 0 0 1 3 3v7"/><path d="M9 44h3.5M19.5 44h14M40.5 44H43M17 34h22M27.5 26v8"/><circle cx="16" cy="44" r="3.5"/><circle cx="37" cy="44" r="3.5"/><path d="M53 50V27"/><rect x="47" y="13" width="12" height="14" rx="2"/><path d="M51.5 23.5v-7h2.8a2.3 2.3 0 0 1 0 4.6h-2.8"/></svg>
            @break
        @case('inclusive-travel-resources')
            <svg class="family-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><circle cx="17" cy="12" r="3"/><path d="M16 18v13h10l3.5 9.5M16 24h8"/><path d="M12.4 27.6A9.5 9.5 0 1 0 25 40"/><circle cx="44" cy="14" r="3"/><path d="M44 19.5v15M44 34.5l-4 14.5M44 34.5l4 14.5M44 24l-5.5 7M44 24l6 6.5"/><circle cx="54.5" cy="29" r="2.4"/><path d="M54.5 33v8.5M54.5 41.5l-2.6 7.5M54.5 41.5l2.6 7.5M54.5 35l-4.5-4.5"/></svg>
            @break
        @case('travel-guides-content')
            <svg class="family-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><path d="M32 53c-6.5-3.6-14.5-4.3-24-3V25.5c9.5-1.3 17.5-.6 24 3 6.5-3.6 14.5-4.3 24-3V50c-9.5-1.3-17.5-.6-24 3Z"/><path d="M32 28.5V53M14 33h11M14 39h11M14 45h7M39 33h11M39 39h11M39 45h7"/><path d="M32 22s-6-6.2-6-10.5a6 6 0 0 1 12 0C38 15.8 32 22 32 22Z"/><circle cx="32" cy="11.5" r="2"/></svg>
            @break
        @case('travel-technology-platforms')
            <svg class="family-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><rect x="20" y="7" width="24" height="50" rx="4"/><path d="M29 12h6M30 51h4"/><path d="M32 21v19M32 28.5l-8 5M32 28.5l8 5M32 38l-3.5 2.5M32 38l3.5 2.5"/><path d="M49.5 25a10 10 0 0 1 0 13M53.5 21a16 16 0 0 1 0 21M14.5 25a10 10 0 0 0 0 13M10.5 21a16 16 0 0 0 0 21" opacity=".55"/></svg>
            @break
        @default
            <svg class="family-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><circle cx="32" cy="25" r="14"/><path d="m32 17.5 2.3 4.7 5.2.8-3.8 3.6.9 5.2-4.6-2.4-4.6 2.4.9-5.2-3.8-3.6 5.2-.8Z"/><path d="m24 37-4 17 7-4 5 6M40 37l4 17-7-4-5 6"/></svg>
    @endswitch
@endif
