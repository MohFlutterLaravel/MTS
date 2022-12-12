<?php

use Illuminate\Support\Facades\Route;

Route::get('/',function () {
    return redirect('/login');
});

Route::get('/{vue_capture?}',function () {
    return view('start');
})->where('vue_capture', '[\/\w\.-]*');

