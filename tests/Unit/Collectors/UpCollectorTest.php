<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Gauge;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\UpCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class UpCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterReportsUpWhenOpcacheIsAvailable(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['opcache_enabled' => true]);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(true)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_up')
            ->once()
            ->andReturn($gauge);

        $collector = new UpCollector($statusGetter);
        $collector->register($prometheus);
    }

    public function testRegisterReportsDownWhenOpcacheIsUnavailable(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(false);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(false)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_up')
            ->once()
            ->andReturn($gauge);

        $collector = new UpCollector($statusGetter);
        $collector->register($prometheus);
    }
}
