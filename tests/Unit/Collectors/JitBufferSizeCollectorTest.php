<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Gauge;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\JitBufferSizeCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class JitBufferSizeCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterCreatesGaugeWithCorrectValues(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['jit' => ['buffer_size' => 134217728]]);
        $statusGetter->shouldReceive('getStatus')
            ->with('jit', null)
            ->andReturn(['buffer_size' => 134217728]);
        $statusGetter->shouldReceive('getStatus')
            ->with('jit.buffer_size', null)
            ->andReturn(134217728);

        /** @var MockInterface|Gauge $gauge */
        $gauge = Mockery::mock(Gauge::class);
        $gauge->shouldReceive('help')->andReturnSelf();
        $gauge->shouldReceive('value')->with(134217728)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addGauge')
            ->with('opcache_jit_buffer_size_bytes')
            ->once()
            ->andReturn($gauge);

        $collector = new JitBufferSizeCollector($statusGetter);
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

        $collector = new JitBufferSizeCollector($statusGetter);
        $collector->register($prometheus);
    }
}
