@php
echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($albums as $album)
        <url>
            <loc>{{ route('search', ['album' => $album->slug]) }}</loc>
            <lastmod>{{ $album->updated_at ? $album->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>

