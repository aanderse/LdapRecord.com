@extends('_layouts.docs')

@section('nav')
    @include('_nav.menu', ['items' => $page->navigation])

    <div class="block md:hidden">
        <hr class="my-4"/>

        <ul class="list-none my-0">
            <li class="pl-4">
                <a href="/docs/laravel/"
                   class="nav-menu__item hover:text-blue-500"
                >
                    Laravel Documentation
                </a>
            </li>

            <li class="pl-4">
                <a href="https://github.com/DirectoryTree/LdapRecord"
                   class="nav-menu__item hover:text-blue-500"
                >
                    Source Code
                </a>
            </li>
        </ul>
    </div>
@endsection

@section('footer')
    @include('_nav.primary-footer-links', ['items' => $page->navigation])
@endsection
