@php
echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($sets as $set)
        <url>
            <loc>{{ route('search.set.details', $set->slug ?? $set->id) }}</loc>
            <lastmod>{{ $set->updated_at ? $set->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
</urlset>

