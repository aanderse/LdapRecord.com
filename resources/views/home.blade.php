@extends('base')

@section('body')
    <section class="container">
        <div class="row align-items-center vh-100">
            <div class="col">
                <div class="text-center">
                    <h1>Adldap2</h1>

                    <p class="text-muted">
                        A PHP LDAP Package <strong>for humans</strong>.
                    </p>

                    <a href="{{ url('docs/'.DEFAULT_VERSION) }}" class="btn btn-outline-secondary">
                        <i class="fa fa-book-open"></i> Documentation
                    </a>

                    <a href="{{ url('docs/'.DEFAULT_VERSION.'#quick-start') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-rocket"></i> Quick Start
                    </a>

                    <a href="https://github.com/Adldap2/Adldap2" class="btn btn-outline-secondary">
                        <i class="fab fa-github"></i> Source on GitHub
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
