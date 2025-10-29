@php
echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($blogs as $blog)
        <url>
            <loc>{{ route('blog.item', $blog->slug) }}</loc>
            <lastmod>{{ $blog->updated_at ? $blog->updated_at->toAtomString() : now()->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>

