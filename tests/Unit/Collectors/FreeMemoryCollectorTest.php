<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Gauge;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\FreeMemoryCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class FreeMemoryCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterCreatesGaugeWithCorrectValues(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['memory_usage' => ['free_memory' => 67108864]]);
        $statusGetter->shouldReceive('getStatus')
            ->with('memory_usage.free_memory', null)
            ->andReturn(67108864);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(67108864)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_free_memory_bytes')
            ->once()
            ->andReturn($gauge);

        $collector = new FreeMemoryCollector($statusGetter);
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

        $collector = new FreeMemoryCollector($statusGetter);
        $collector->register($prometheus);
    }
}
