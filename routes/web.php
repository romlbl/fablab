<?php

use Illuminate\Support\Facades\Route;

Route::view('/connexion', 'pages.auth.connexion')->name('connexion');

require __DIR__ . '/client.php';
require __DIR__ . '/admin.php';