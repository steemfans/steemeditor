<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ elixir('/static/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ elixir('/static/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ elixir('/static/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ elixir('/static/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ elixir('/static/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ elixir('/static/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ elixir('/static/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ elixir('/static/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ elixir('/static/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ elixir('/static/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ elixir('/static/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ elixir('/static/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ elixir('/static/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ elixir('/static/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ elixir('/static/favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="{{ elixir('/static/font/iconfont.css') }}">
    <link rel="stylesheet" href="{{ elixir('/plugins/editor/css/editormd.min.css') }}">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
  </head>
  <body>
    <script>
      Laravel = {
        sc2: {
          baseURL: 'https://v2.steemconnect.com',
          app: '{{ $scApp }}',
          callbackURL: '{{ $scCallback }}',
          scope: [{!! $scScope !!}],
        },
        username: '{{ $username }}',
        accessToken: '{{ $accessToken }}',
        userInfo: {!! $userInfo !!},
      }
    </script>
    <div id="app"></div>
    <script src="{{ mix('/js/main.js') }}"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-116713187-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'UA-116713187-1');
    </script>
  </body>
</html>
