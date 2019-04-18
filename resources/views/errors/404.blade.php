@extends('layouts.base')

@section('body')
    <section class="container">
        <div class="row align-items-center vh-100">
            <div class="col text-center">
                <h1>404 - Not Found</h1>

                @include('partials.doc-missing')

                <a href="{{ url('/docs/'.DEFAULT_VERSION) }}" class="btn btn-outline-secondary mt-2">
                    <i class="fa fa-book-open"></i>
                    Go to LdapRecord Documentation
                </a>
            </div>
        </div>
    </section>
@endsection
