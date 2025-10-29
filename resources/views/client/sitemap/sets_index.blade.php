@php
echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @for ($page = 1; $page <= $totalPages; $page++)
        <sitemap>
            <loc>{{ route('sitemap.sets', ['page' => $page]) }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
        </sitemap>
    @endfor
</sitemapindex>

