<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit;

use Vntrungld\PrometheusExporterOpcacheCollector\PrometheusExporterOpcacheCollectorServiceProvider;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;
use Vntrungld\PrometheusExporterOpcacheCollector\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function testServiceProviderIsRegistered(): void
    {
        $providers = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(
            PrometheusExporterOpcacheCollectorServiceProvider::class,
            $providers
        );
    }

    public function testStatusGetterIsBoundAsSingleton(): void
    {
        if (! function_exists('opcache_get_status')) {
            $this->markTestSkipped('opcache_get_status() is not available');
        }

        $instance1 = $this->app->make(StatusGetter::class);
        $instance2 = $this->app->make(StatusGetter::class);

        $this->assertSame($instance1, $instance2);
    }

    public function testProvidesReturnsExpectedServices(): void
    {
        $provider = new PrometheusExporterOpcacheCollectorServiceProvider($this->app);

        $provides = $provider->provides();

        $this->assertContains(StatusGetter::class, $provides);
    }
}
