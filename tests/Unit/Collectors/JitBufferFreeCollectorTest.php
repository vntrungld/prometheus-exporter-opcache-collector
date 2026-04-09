<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Gauge;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\JitBufferFreeCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class JitBufferFreeCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterCreatesGaugeWithCorrectValues(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['jit' => ['buffer_free' => 67108864]]);
        $statusGetter->shouldReceive('getStatus')
            ->with('jit', null)
            ->andReturn(['buffer_free' => 67108864]);
        $statusGetter->shouldReceive('getStatus')
            ->with('jit.buffer_free', null)
            ->andReturn(67108864);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(67108864)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_jit_buffer_free_bytes')
            ->once()
            ->andReturn($gauge);

        $collector = new JitBufferFreeCollector($statusGetter);
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

        $collector = new JitBufferFreeCollector($statusGetter);
        $collector->register($prometheus);
    }
}
