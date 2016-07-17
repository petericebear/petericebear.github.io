<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{$siteDescription}}">

    <title>@yield('pageTitle') {{$siteName}}</title>

    <link href='https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/libs.css">
    <link rel="stylesheet" href="/prism.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body>

<div class="container">
    <div class="row main-row">
        <div class="col-md-8 main-content">
            @yield('body')
        </div>

        <div class="col-md-3 col-md-offset-1 sidebar">
            <div class="avatar">
                <a href="/"><img src="https://avatars1.githubusercontent.com/u/339796?v=3&s=460"></a>
            </div>

            <h1>Peter Steenbergen</h1>

            <p>WebDeveloper [PHP, Laravel, Ionic, SOLR] &amp; learning Swift.</p>

            <a href="https://github.com/petericebear">@github</a> ·
            <a href="https://twitter.com/petericebear">@twitter</a> ·
            <a href="mailto:psteenbergen@gmail.com">@email</a>
        </div>
    </div>

    <p class="footer">
        &copy; Peter Steenbergen · Design and built with <a href="http://themsaid.github.io/katana/">Katana</a>
    </p>
</div>

<script src="/prism.js"></script>

<script>!function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + '://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'twitter-wjs');</script>
<script>(function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
        a = s.createElement(o), m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
    ga('create', 'UA-77757149-1', 'auto');
    ga('send', 'pageview');
</script>
</body>
</html>