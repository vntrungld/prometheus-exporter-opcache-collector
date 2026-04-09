<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Gauge;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\ConfigMemoryLimitCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class ConfigMemoryLimitCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterCreatesGaugeWithCorrectValues(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['opcache_enabled' => true]);
        $statusGetter->shouldReceive('getConfiguration')
            ->with('directives', null)
            ->andReturn(['opcache.memory_consumption' => 134217728]);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(134217728)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_config_memory_limit_bytes')
            ->once()
            ->andReturn($gauge);

        $collector = new ConfigMemoryLimitCollector($statusGetter);
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

        $collector = new ConfigMemoryLimitCollector($statusGetter);
        $collector->register($prometheus);
    }
}
