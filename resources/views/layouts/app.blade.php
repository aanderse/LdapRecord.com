@extends('layouts.base')

@section('body')
    @include('partials.nav')

    <div class="container">
        <div class="row">
            <div class="navbar-collapse side-nav col-sm-12 col-md-4 col-lg-3 pt-4 border-md-right border-bottom border-sm-bottom-none collapse" id="nav-main">
                @foreach($versions as $version => $name)
                    @php($path = 'docs/'.$version)

                    <a
                            href="{{ url($path) }}"
                            class="btn btn-sm btn-outline-secondary mb-md-2 mb-lg-0 {{ request()->is($path.'*') ? 'active' : null }}">
                        {{ $name }}
                    </a>
                @endforeach

                {!! $index !!}

                <ul class="d-sm-block d-md-none">
                    <li>
                        <a href="https://github.com/DirectoryTree/LdapRecord">
                            <i class="fab fa-github"></i> Source on GitHub
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-8 col-lg-9">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
