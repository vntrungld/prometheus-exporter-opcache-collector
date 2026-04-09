<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporter\MetricTypes\Counter;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\HitsCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class HitsCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegisterCreatesCounterWithCorrectValues(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with(null, null)
            ->andReturn(['opcache_statistics' => ['hits' => 50000]]);
        $statusGetter->shouldReceive('getStatus')
            ->with('opcache_statistics.hits', null)
            ->andReturn(50000);

        /** @var MockInterface|Counter $counter */
        $counter = Mockery::mock(Counter::class);
        $counter->shouldReceive('help')->andReturnSelf();
        $counter->shouldReceive('value')->with(50000)->andReturnSelf();

        /** @var MockInterface|Prometheus $prometheus */
        $prometheus = Mockery::mock(Prometheus::class);
        $prometheus->shouldReceive('addCounter')
            ->with('opcache_hits_total')
            ->once()
            ->andReturn($counter);

        $collector = new HitsCollector($statusGetter);
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
        $prometheus->shouldNotReceive('addCounter');

        $collector = new HitsCollector($statusGetter);
        $collector->register($prometheus);
    }
}
