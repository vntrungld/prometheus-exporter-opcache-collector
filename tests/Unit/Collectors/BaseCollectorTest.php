<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit\Collectors;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporter\Prometheus;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\BaseCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class BaseCollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testCollectorReceivesStatusGetter(): void
    {
        $statusGetter = Mockery::mock(StatusGetter::class);

        $collector = $this->createConcreteCollector($statusGetter);

        $this->assertInstanceOf(BaseCollector::class, $collector);
    }

    public function testStatusMethodDelegatesToStatusGetter(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getStatus')
            ->with('opcache_enabled', null)
            ->once()
            ->andReturn(true);

        $collector = $this->createConcreteCollector($statusGetter);

        $reflection = new \ReflectionClass($collector);
        $method = $reflection->getMethod('status');
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        $result = $method->invoke($collector, 'opcache_enabled');

        $this->assertTrue($result);
    }

    public function testConfigMethodDelegatesToStatusGetter(): void
    {
        /** @var MockInterface|StatusGetter $statusGetter */
        $statusGetter = Mockery::mock(StatusGetter::class);
        $statusGetter->shouldReceive('getConfiguration')
            ->with('directives', null)
            ->once()
            ->andReturn(['opcache.memory_consumption' => 134217728]);

        $collector = $this->createConcreteCollector($statusGetter);

        $reflection = new \ReflectionClass($collector);
        $method = $reflection->getMethod('config');
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        $result = $method->invoke($collector, 'directives');

        $this->assertIsArray($result);
        $this->assertEquals(134217728, $result['opcache.memory_consumption']);
    }

    protected function createConcreteCollector(StatusGetter $statusGetter): BaseCollector
    {
        return new class($statusGetter) extends BaseCollector {
            public function register(Prometheus $prometheus): void
            {
                // Empty implementation for testing
            }
        };
    }
}
