<?php

use App\Http\Controllers\DeconnexionController;
use Illuminate\Support\Facades\Route;

Route::view('/connexion', 'pages.auth.connexion')->name('connexion');
Route::post('/deconnexion', DeconnexionController::class)->name('deconnexion');

require __DIR__ . '/client.php';
require __DIR__ . '/admin.php';