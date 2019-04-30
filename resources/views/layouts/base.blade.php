<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ isset($title) ? $title . ' - ' : null }}LdapRecord</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="author" content="Steve Bauman">
    <meta name="description" content="LdapRecord - A PHP LDAP Package for Humans">
    <meta name="keywords" content="php, ldap, adldap, adldap2, ldaprecord">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if (isset($canonical))
        <link rel="canonical" href="{{ url($canonical) }}" />
    @endif
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
</head>
<body class="@yield('body-class', 'docs') language-php">
    @yield('body')
    <script src="{{ mix('assets/js/app.js') }}"></script>
</body>
</html>
