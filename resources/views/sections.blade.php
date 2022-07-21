<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($sections as $section)
        <url>
            <loc>https://project.ru/catalog/{{ $section->code }}</loc>
            <lastmod>{{ $section->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>{{ $section->sectionId ? '0.7' : '0.8' }}</priority>
        </url>
    @endforeach
</urlset>
