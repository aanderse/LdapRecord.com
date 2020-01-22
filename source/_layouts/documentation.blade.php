@extends('_layouts.docs')

@section('nav')
    @include('_nav.menu', ['items' => $page->navigation])
@endsection

@section('footer')
    @include('_nav.primary-footer-links', ['items' => $page->navigation])
@endsection
