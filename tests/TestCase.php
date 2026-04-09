<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vntrungld\PrometheusExporterOpcacheCollector\PrometheusExporterOpcacheCollectorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PrometheusExporterOpcacheCollectorServiceProvider::class,
        ];
    }
}
