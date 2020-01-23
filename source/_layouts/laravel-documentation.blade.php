@extends('_layouts.docs')

@section('nav')
    @include('_nav.menu', ['items' => $page->laravelNavigation])
@endsection

@section('footer')
    @include('_nav.laravel-footer-links', ['items' => $page->laravelNavigation])
@endsection
