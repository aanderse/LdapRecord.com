@extends('_layouts.docs')

@section('nav')
    @include('_nav.menu', ['items' => $page->laravelNavigation])

    <div class="block sm:hidden">
        <hr class="my-4"/>

        <ul class="list-none my-0">
            <li class="pl-4">
                <a href="/docs/"
                   class="nav-menu__item hover:text-blue-500"
                >
                    Core Documentation
                </a>
            </li>

            <li class="pl-4">
                <a href="https://github.com/DirectoryTree/LdapRecord-Laravel"
                   class="nav-menu__item hover:text-blue-500"
                >
                    Source Code
                </a>
            </li>
        </ul>
    </div>
@endsection

@section('footer')
    @include('_nav.laravel-footer-links', ['items' => $page->laravelNavigation])
@endsection
