<?php

use think\facade\Route;

Route::get('/id/:id', '/Index/index');
Route::get('/order/search', '/Order/search');
Route::get('/order/:id', '/Order/index');
Route::get('/item/:id', '/Item/index');
Route::get('/category/:id', '/Category/index');
Route::get('/brand/:id', '/Brand/index');
