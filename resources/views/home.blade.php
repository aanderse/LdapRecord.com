@extends('layouts.base')

@section('body')
    <div class="d-flex justify-content-center align-items-center min-vh-100 mt-n4">
        <div class="d-flex flex-column align-items-center">
            <div class="text-center">
                <img src="{{ asset('assets/img/ldap-record.png') }}" width="500" class="img-fluid">

                <p class="text-muted" style="font-size:1.3em;">
                    A PHP LDAP Package <strong>for humans</strong>.
                </p>

                <hr/>

                <div class="d-md-flex justify-content-center">
                    <a href="{{ url('docs/'.DEFAULT_VERSION) }}" class="btn btn-outline-secondary rounded-pill mx-1 mx-md-2 mb-2 mb-md-0">
                        <i class="fa fa-book-open"></i> Documentation
                    </a>

                    <a href="{{ url('docs/'.DEFAULT_VERSION.'#quick-start') }}" class="btn btn-outline-secondary rounded-pill mx-1 mx-md-2 mb-2 mb-md-0">
                        <i class="fa fa-rocket"></i> Quick Start
                    </a>

                    <a href="https://github.com/DirectoryTree/LdapRecord" class="btn btn-outline-secondary rounded-pill mx-1 mx-md-2 mb-2 mb-md-0">
                        <i class="fab fa-github"></i> Source on GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
