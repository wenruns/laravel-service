<?php

use WenRuns\Laravel\Http\Controllers\LaravelController;

Route::get('laravel-service', LaravelController::class.'@index');