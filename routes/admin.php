<?php

use Illuminate\Support\Facades\Route;

Route::middleware('admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::view('/', 'pages.admin.accueil')->name('accueil');
        Route::view('/jobs', 'pages.admin.jobs')->name('jobs');

        Route::prefix('projets')->name('projets.')->group(function () {
            Route::view('/', 'pages.admin.projets.index')->name('index');
            Route::view('/{projet}', 'pages.admin.projets.show')->name('show');
        });

        Route::view('/machines', 'pages.admin.machines')->name('machines');
        Route::view('/consos', 'pages.admin.consos')->name('consos');
        Route::view('/collabs', 'pages.admin.collabs')->name('collabs');
        Route::view('/clients', 'pages.admin.clients')->name('clients');
    });