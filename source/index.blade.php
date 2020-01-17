@extends('_layouts.master')

@section('body')
<section class="retro-hero">
    <div class="retro-hero-background" style="background-image:url('/assets/img/bg-retro.jpg');"></div>

    <div class="retro-hero-content md:mx-48">
        <h1 class="retro-hero-heading sm:text-3xl lg:text-5xl">
            <em>Integrating LDAP is hard work.</em>
        </h1>

        <h2 class="text-shadow font-light hidden sm:block mt-4 text-white">
            <strong class="font-extrabold italic">LdapRecord</strong> is a <strong class="font-extrabold italic">PHP</strong> package that helps you integrate LDAP into your applications. No hair-pulling necessary.
        </h2>

        <h4 class="text-shadow font-light block sm:hidden mt-4 text-white">
            <strong class="font-extrabold italic">LdapRecord</strong> is a <strong class="font-extrabold italic">PHP</strong> package that helps you integrate LDAP into your applications. No hair-pulling necessary.
        </h4>

        <div class="flex justify-center mb-8">
            <a href="/docs/installation" title="{{ $page->siteName }} getting started" class="bg-purple-500 hover:bg-purple-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">Get Started</a>

            <a href="https://github.com/DirectoryTree/LdapRecord" title="GitHub LdapRecord Source Code Link" class="bg-gray-400 hover:bg-gray-600 text-blue-900 font-normal hover:text-white rounded py-2 px-6">Source Code</a>
        </div>
    </div>
</section>

<div class="md:mb-32 lg:w-2/3 mx-auto relative z-10 -mt-64">
    <div class="feature shadow mx-4 md:mx-12">
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

<section class="container max-w-6xl mx-auto px-6 py-0 mt-16 md:mt-0">
    <!-- Fast Setup. -->
    <div class="flex flex-col-reverse md:flex-row md:flex md:items-center lg:mb-32">
        <div class="md:w-3/5">
            @component('components.feature')
                @include('docs.examples.fast-setup')
            @endcomponent
        </div>

        <div class="md:w-2/5 md:ml-8 flex flex-col items-center md:items-start">
            <div class="flex items-center">
                <img src="/assets/img/stopwatch.svg" class="h-12 w-12" alt="window icon">

                <h3 id="intro-laravel" class="text-2xl text-blue-900 mb-0 mt-0 ml-2 font-extrabold italic text-shadow">
                    Up and running fast
                </h3>
            </div>

            <p class="text-xl text-center md:text-left">
                Effortlessly connect to your LDAP servers and start running queries & operations in a matter of minutes.
            </p>
        </div>
    </div>

    <hr class="block my-8 border lg:hidden">

    <!-- Fluent Query Builder. -->
    <div class="flex flex-col md:flex-row md:flex md:items-center lg:mb-32">
        <div class="md:w-2/5 md:mr-8 flex flex-col items-center md:items-start">
            <div class="flex items-center">
                <img src="/assets/img/repeat.svg" class="h-12 w-12" alt="terminal icon">

                <h3 id="intro-markdown" class="text-2xl text-blue-900 mb-0 mt-0 ml-2 font-extrabold italic text-shadow">
                    Fluent Query Builder
                </h3>
            </div>

            <p class="text-xl text-center md:text-left">
                Building LDAP queries has never been so easy. Find the objects you're
                looking for in a couple lines or less with a fluent interface.
            </p>
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

        <div class="md:w-2/5 md:ml-8 flex flex-col items-center md:items-start">
            <div class="flex items-center">
                <img src="/assets/img/volume-control.svg" class="h-12 w-12" alt="stack icon">

                <h3 id="intro-mix" class="text-2xl text-blue-900 mb-0 mt-0 ml-2 font-extrabold italic text-shadow">
                    Supercharged ActiveRecord
                </h3>
            </div>

            <p class="text-xl text-center md:text-left">
                Create and modify LDAP objects with ease. All LDAP objects are individual models. Simply modify the
                attributes on the model and save it to persist the changes to your LDAP server.
            </p>
        </div>
    </div>
</section>
@endsection
