<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector;

use Illuminate\Support\ServiceProvider;

class PrometheusExporterOpcacheCollectorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StatusGetter::class, function () {
            return new StatusGetter();
        });
    }

    public function provides(): array
    {
        return [StatusGetter::class];
    }
}
