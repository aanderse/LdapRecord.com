@extends('base')

@section('body')
    @include('partials.nav')
    <div class="container mt-4">
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 pt-4 border-right">
                <div class="navbar-collapse side-nav" id="nav-main">
                    @foreach($versions as $version => $name)
                        @php($path = 'docs/'.$version)

                        <a
                                href="{{ url($path) }}"
                                class="btn btn-sm btn-outline-secondary mb-md-2 mb-lg-0 {{ request()->is($path.'*') ? 'active' : null }}">
                            {{ $name }}
                        </a>
                    @endforeach

                    {!! $index !!}
                </div>
            </div>

            <div class="col-md-8 col-lg-9">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
