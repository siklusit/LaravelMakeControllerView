<?php

namespace Siklusit\LaravelMakeControllerView;

use Illuminate\Support\ServiceProvider;
use Siklusit\LaravelMakeControllerView\Console\MakeControllerAndView;

class MakeControllerViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeControllerAndView::class,
        ]);
    }

    public function boot()
    {
        // Bootstrapping code if needed
    }
}