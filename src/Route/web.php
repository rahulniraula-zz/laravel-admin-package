<?php

use Geeklearners\Util\Util;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('admin.prefix'),
    'namespace' => 'Geeklearners\Http\Controllers',
    'middleware' => array_merge(['web'], config('admin.middlewares'))
], function () {
    Route::get('{model}', 'CrudController@index')
        ->where('model', Util::prepareRoutingRegex())
        ->name(config('admin.prefix') . '.index');
    Route::get('{model}/create', 'CrudController@create')
        ->where('model', Util::prepareRoutingRegex())
        ->name(config('admin.prefix') . '.create');
    Route::post('{model}', 'CrudController@store')
        ->where('model', Util::prepareRoutingRegex())
        ->name(config('admin.prefix') . '.store');
    Route::get('{model}/{id}/edit', 'CrudController@edit')
        ->where('model', Util::prepareRoutingRegex())
        ->name(config('admin.prefix') . '.edit');
    Route::put('{model}/{id}', 'CrudController@update')
        ->where('model', Util::prepareRoutingRegex())
        ->name(config('admin.prefix') . '.update');
    Route::delete('{model}/{id}', 'CrudController@destroy')
        ->where('model', Util::prepareRoutingRegex())
        ->name(config('admin.prefix') . '.destroy');
});
