<?php

namespace Midnite81\ReportEmailer;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Midnite81\LaravelBase\BaseServiceProvider;

class ReportEmailerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->register(BaseServiceProvider::class);
        $this->app->register(ExcelServiceProvider::class);
    }
}