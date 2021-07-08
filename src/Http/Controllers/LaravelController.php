<?php

namespace WenRuns\Laravel\Http\Controllers;

use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

class LaravelController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Title')
            ->description('Description')
            ->body(view('laravel-service::index'));
    }
}