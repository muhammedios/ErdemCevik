<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($item->slug)
@case('implant')
<path d="M14 19h20l-2-9c-1-5-6-5-8-3-2-2-7-2-8 3l-2 9Z M18 23h12l-2 17-4 3-4-3-2-17Z M17 27l14-3 M18 32l12-3 M19 37l10-3"/>
@break
@case('ortodonti')
<path d="M8 16c8-4 24-4 32 0v15c-8 4-24 4-32 0V16Z M17 14v20 M31 14v20 M5 24h38"/><rect x="11" y="21" width="4" height="6" rx="1"/><rect x="22" y="21" width="4" height="6" rx="1"/><rect x="33" y="21" width="4" height="6" rx="1"/>
@break
@case('dis-beyazlatma')
<path d="M25 11c-4-4-13-3-13 5 0 6 3 8 4 14 1 6 3 8 4 5l3-9c1-2 2-2 3 0l3 9c2 3 3 1 4-5l1-4 M33 5v12 M27 11h12 M38 19v7 M35 22.5h6"/>
@break
@case('restoratif-tedaviler')
<path d="M22 10c-5-4-14-3-14 6 0 6 4 9 5 15 1 6 3 8 5 5l4-10c1-2 2-2 3 0l4 10c2 3 4 1 5-5l2-9 M22 10l4 6-5 5 5 5 M31 9h12 M37 3v12"/>
@break
@case('dis-eti-sagligi')
<path d="M9 29c4-4 7 4 15 0s12-4 15 0v9H9v-9Z M15 28c-1-6-5-9-5-14 0-7 8-8 14-4 6-4 14-3 14 4 0 5-4 8-5 14 M19 9l8 5 M7 42h34"/>
@break
@default
<path d="M24 10c-5-5-16-4-16 6 0 7 4 9 5 16 1 7 3 9 5 6l4-11c1-3 3-3 4 0l4 11c2 3 4 1 5-6 1-7 5-9 5-16 0-10-11-11-16-6Z M18 9l8 6 M38 3v8 M34 7h8"/>
@endswitch
</svg>
