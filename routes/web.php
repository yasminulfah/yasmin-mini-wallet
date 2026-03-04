<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'project' => 'Yasmin Mini Wallet',
        'developer' => 'Syarifatul Yasmin Ulfah',
        'status' => 'Live',
        'version' => '1.0.0',
        'message' => 'Backend is running smoothly! ✨'
    ]);
});
