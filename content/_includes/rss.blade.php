<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>{{ @$siteName }}</title>
        <description>{{ @$siteDescription }}</description>
        <link>@url('/')</link>
        <atom:link href="@url('/feed.xml')" rel="self" type="application/rss+xml" />
        @foreach($blogPosts as $blogPost)
        <item>
            <title>{{ htmlspecialchars($blogPost->title, ENT_QUOTES) }}</title>
            <description>{{ str_replace('’', '&#8217;', $blogPost->brief) }}</description>
            <link>@url($blogPost->path)</link>
        </item>
        @endforeach
    </channel>
</rss>