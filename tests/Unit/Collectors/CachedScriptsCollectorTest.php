<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Gauge;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\CachedScriptsCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class CachedScriptsCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterCreatesGaugeWithCorrectValues(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['opcache_statistics' => ['num_cached_scripts' => 150]]);
        $statusGetter->shouldReceive('getStatus')
            ->with('opcache_statistics.num_cached_scripts', null)
            ->andReturn(150);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(150)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_cached_scripts')
            ->once()
            ->andReturn($gauge);

        $collector = new CachedScriptsCollector($statusGetter);
        $collector->register($prometheus);
    }

    public function testRegisterSkipsWhenOpcacheIsUnavailable(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(false);

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldNotReceive('addGauge');

        $collector = new CachedScriptsCollector($statusGetter);
        $collector->register($prometheus);
    }
}
