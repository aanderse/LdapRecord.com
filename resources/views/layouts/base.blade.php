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
    <link rel="apple-touch-icon" href="/favicon.png">
</head>
<body class="@yield('body-class', 'docs') language-php">
    @yield('body')
    <script src="{{ mix('assets/js/app.js') }}"></script>
</body>
</html>
