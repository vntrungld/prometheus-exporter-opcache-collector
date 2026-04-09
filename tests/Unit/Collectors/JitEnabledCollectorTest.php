<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Gauge;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\JitEnabledCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class JitEnabledCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterCreatesGaugeWhenJitIsEnabled(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['jit' => ['enabled' => true]]);
        $statusGetter->shouldReceive('getStatus')
            ->with('jit', null)
            ->andReturn(['enabled' => true]);
        $statusGetter->shouldReceive('getStatus')
            ->with('jit.enabled', null)
            ->andReturn(true);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(1)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_jit_enabled')
            ->once()
            ->andReturn($gauge);

        $collector = new JitEnabledCollector($statusGetter);
        $collector->register($prometheus);
    }

    public function testRegisterSkipsWhenJitIsNotAvailable(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['opcache_enabled' => true]);
        $statusGetter->shouldReceive('getStatus')
            ->with('jit', null)
            ->andReturn(null);

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldNotReceive('addGauge');

        $collector = new JitEnabledCollector($statusGetter);
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

        $collector = new JitEnabledCollector($statusGetter);
        $collector->register($prometheus);
    }
}
