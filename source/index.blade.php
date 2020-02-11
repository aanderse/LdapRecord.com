@extends('_layouts.master')

@section('body')
<section class="retro-hero">
    <div class="retro-hero-background w-3/4 md:w-1/2 h-screen bg-purple-900" style="transform: skew(45deg);"></div>

    <div class="retro-hero-content md:mx-48 my-auto">
        <h1 class="retro-hero-heading sm:text-3xl lg:text-5xl">
            <em>Integrate LDAP into your PHP application</em>
        </h1>

        <h2 class="text-shadow font-light hidden sm:block mt-4 text-white max-w-5xl">
            No hair-pulling necessary.
        </h2>

        <h4 class="text-shadow font-light block sm:hidden mt-4 text-white">
            No hair-pulling necessary.
        </h4>

        <div class="flex justify-center mb-8">
            <a href="/docs/installation" title="{{ $page->siteName }} getting started" class="uppercase tracking-wide italic bg-purple-500 hover:bg-purple-600 font-extrabold text-white hover:text-white rounded mr-4 py-2 px-6">
                Get Started
            </a>

            <a href="https://github.com/DirectoryTree/LdapRecord" title="GitHub LdapRecord Source Code Link" class="uppercase tracking-wide italic bg-gray-400 hover:bg-gray-600 text-blue-900 font-extrabold hover:text-white rounded py-2 px-6">
                Source Code
            </a>
        </div>
    </div>
</section>

<div class="md:mb-32 lg:w-2/3 max-w-5xl mx-auto relative z-10 -mt-32">
    <div class="feature shadow-2xl mx-8">
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

<section class="mx-auto py-0 mt-16 md:mt-0">
    <!-- Fast Setup. -->
    <div class="flex flex-col-reverse md:flex-row md:flex md:items-center lg:mb-32 p-12">
        <div class="md:w-3/5">
            @component('components.feature')
                @include('docs.examples.fast-setup')
            @endcomponent
        </div>

        <div class="md:w-2/5 md:ml-8 flex flex-col items-center md:items-start">
            <h3 class="text-center md:text-left text-5xl text-purple-800 my-0 font-extrabold italic text-shadow">
                Up and running fast
            </h3>

            <p class="text-xl text-center md:text-left">
                Effortlessly connect to your LDAP servers and start running queries & operations faster than dial up.
            </p>
        </div>
    </div>

    <!-- Fluent Query Builder. -->
    <div class="flex flex-col md:flex-row md:flex md:items-center lg:mb-32 p-12 bg-purple-800">
        <div class="md:w-2/5 md:mr-8 flex flex-col items-center md:items-start">
            <h3 class="text-center md:text-left text-5xl text-white my-0 font-extrabold italic">
                Fluent Query Builder
            </h3>

            <p class="text-xl text-white text-center md:text-left">
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

    <!-- ActiveRecord. -->
    <div class="flex flex-col-reverse md:flex-row md:flex md:items-center mb-24 p-12">
        <div class="md:w-3/5">
            @component('components.feature')
                @include('docs.examples.active-record')
            @endcomponent
        </div>

        <div class="md:w-2/5 md:ml-8 flex flex-col items-center md:items-start">
            <h3 class="text-center md:text-left text-5xl text-purple-800 my-0 font-extrabold italic text-shadow">
                Supercharged Active Record
            </h3>

            <p class="text-xl text-center md:text-left">
                Create and modify LDAP objects with ease. All LDAP objects are individual models. Simply modify the
                attributes on the model and save it to persist the changes to your LDAP server.
            </p>
        </div>
    </div>
</section>
@endsection
