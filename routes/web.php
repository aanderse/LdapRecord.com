<?php

/**
 * Setup our repository routes...
 */

if (! defined('DEFAULT_VERSION')) {
    define('DEFAULT_VERSION', 'master');
}

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('docs', 'DocsController@showRootPage')->name('docs');
Route::get('docs/{version}/{page?}', 'DocsController@show')->where('page', '.*')->name('page');