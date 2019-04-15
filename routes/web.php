<?php

/**
 * Set the default documentation version...
 */
if (! defined('DEFAULT_VERSION')) {
    define('DEFAULT_VERSION', '10.0');
}

Route::get('/', function () {
    return view('home');
});

Route::get('docs', 'DocsController@showRootPage');
Route::get('docs/{version}/{page?}', 'DocsController@show')->where('page', '.*');
