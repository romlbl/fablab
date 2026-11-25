<?php

use Illuminate\Support\Facades\Route;

Route::middleware('client')
    ->name('client.')
    ->group(function () {
        Route::view('/', 'pages.client.accueil')->name('accueil');

        Route::prefix('projets')->name('projets.')->group(function () {
            Route::view('/', 'pages.client.projets.index')->name('index');
            Route::view('/{projet}', 'pages.client.projets.show')->name('show');
        });

        Route::view('/machines', 'pages.client.machines')->name('machines');
        Route::view('/consos', 'pages.client.consos')->name('consos');
    });