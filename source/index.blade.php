@extends('_layouts.master')

@section('body')
<section class="container max-w-6xl mx-auto px-6 py-0">
    <div class="mb-10 text-center">
        <h1 id="intro-docs-template">Integrating LDAP is hard.</h1>

        <h2 class="font-light hidden sm:block mt-4">
            <strong>LdapRecord</strong> is a <strong>PHP</strong> package that helps you integrate LDAP into your applications. No hair-pulling necessary.
        </h2>

        <h4 class="font-light block sm:hidden mt-4">
            <strong>LdapRecord</strong> is a <strong>PHP</strong> package that helps you integrate LDAP into your applications. No hair-pulling necessary.
        </h4>
    </div>

    <div class="flex justify-center mb-8">
        <a href="/docs/installation" title="{{ $page->siteName }} getting started" class="bg-purple-500 hover:bg-purple-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">Get Started</a>

        <a href="https://github.com/DirectoryTree/LdapRecord" title="GitHub LdapRecord Source Code Link" class="bg-gray-400 hover:bg-gray-600 text-blue-900 font-normal hover:text-white rounded py-2 px-6">Source Code</a>
    </div>

    <div class="lg:mb-32 lg:w-2/3 mx-auto">
        <div class="feature">
            <div class="top-bar">
                <div class="circles">
                    <div class="circle circle-red"></div>
                    <div class="circle circle-yellow"></div>
                    <div class="circle circle-green"></div>
                </div>
            </div>

            @include('docs.examples.landing')
        </div>
    </div>

    <hr class="block my-8 border">

    <!-- Fast Setup. -->
    <div class="flex flex-col-reverse md:flex-row md:flex md:items-center lg:mb-32">
        <div class="md:w-3/5">
            @component('components.feature')
                @include('docs.examples.fast-setup')
            @endcomponent
        </div>

        <div class="md:w-2/5 md:ml-8">
            <div class="flex items-center">
                <img src="/assets/img/stopwatch.svg" class="h-12 w-12" alt="window icon">

                <h3 id="intro-laravel" class="text-2xl text-blue-900 mb-0 mt-0 ml-2">Up and running within minutes</h3>
            </div>

            <p>Effortlessly connect to your LDAP servers and start running queries & operations in a matter of minutes.</p>
        </div>
    </div>

    <hr class="block my-8 border lg:hidden">

    <!-- Fluent Query Builder. -->
    <div class="flex flex-col md:flex-row md:flex md:items-center lg:mb-32">
        <div class="md:w-2/5 md:mr-8">
            <div class="flex items-center">
                <img src="/assets/img/repeat.svg" class="h-12 w-12" alt="terminal icon">

                <h3 id="intro-markdown" class="text-2xl text-blue-900 mb-0 mt-0 ml-2">Fluent Query Builder</h3>
            </div>

            <p>Building LDAP queries has never been so easy. Find the objects you're looking for in a couple lines or less with a fluent interface.</p>
        </div>

        <div class="md:w-3/5">
            @component('components.feature')
                @include('docs.examples.fluent')
            @endcomponent
        </div>
    </div>

    <hr class="block my-8 border lg:hidden">

    <!-- ActiveRecord. -->
    <div class="flex flex-col-reverse md:flex-row md:flex md:items-center mb-24">
        <div class="md:w-3/5">
            @component('components.feature')
                @include('docs.examples.active-record')
            @endcomponent
        </div>

        <div class="md:w-2/5 md:ml-8">
            <div class="flex items-center">
                <img src="/assets/img/volume-control.svg" class="h-12 w-12" alt="stack icon">

                <h3 id="intro-mix" class="text-2xl text-blue-900 mb-0 mt-0 ml-2">Supercharged ActiveRecord</h3>
            </div>

            <p>
                Create and modify LDAP objects with ease. All LDAP objects are individual models. Simply modify the
                attributes on the model and save it to persist the changes to your LDAP server.
            </p>
        </div>
    </div>
</section>
@endsection
