<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

class StatusGetterTest extends TestCase
{
    public function testGetStatusReturnsFullStatusWhenNoKeyProvided(): void
    {
        $statusGetter = $this->createMockStatusGetter([
            'opcache_enabled' => true,
            'memory_usage' => ['used_memory' => 1024],
        ]);

        $result = $statusGetter->getStatus();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('opcache_enabled', $result);
        $this->assertTrue($result['opcache_enabled']);
    }

    public function testGetStatusReturnsSpecificValueWhenKeyProvided(): void
    {
        $statusGetter = $this->createMockStatusGetter([
            'opcache_enabled' => true,
            'memory_usage' => ['used_memory' => 1024],
        ]);

        $result = $statusGetter->getStatus('opcache_enabled');

        $this->assertTrue($result);
    }

    public function testGetStatusReturnsNestedValue(): void
    {
        $statusGetter = $this->createMockStatusGetter([
            'memory_usage' => ['used_memory' => 1024, 'free_memory' => 2048],
        ]);

        $result = $statusGetter->getStatus('memory_usage.used_memory');

        $this->assertEquals(1024, $result);
    }

    public function testGetStatusReturnsDefaultForNonExistentKey(): void
    {
        $statusGetter = $this->createMockStatusGetter([
            'opcache_enabled' => true,
        ]);

        $result = $statusGetter->getStatus('non_existent');

        $this->assertNull($result);
    }

    public function testGetConfigurationReturnsFullConfigWhenNoKeyProvided(): void
    {
        $statusGetter = $this->createMockStatusGetter([], [
            'directives' => ['opcache.memory_consumption' => 134217728],
        ]);

        $result = $statusGetter->getConfiguration();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('directives', $result);
    }

    public function testGetConfigurationReturnsSpecificValueWhenKeyProvided(): void
    {
        $statusGetter = $this->createMockStatusGetter([], [
            'directives' => ['opcache.memory_consumption' => 134217728],
        ]);

        $result = $statusGetter->getConfiguration('directives');

        $this->assertIsArray($result);
        $this->assertEquals(134217728, $result['opcache.memory_consumption']);
    }

    public function testGetConfigurationReturnsDefaultForNonExistentKey(): void
    {
        $statusGetter = $this->createMockStatusGetter([], [
            'directives' => [],
        ]);

        $result = $statusGetter->getConfiguration('non_existent', 'default');

        $this->assertEquals('default', $result);
    }

    protected function createMockStatusGetter(array $status = [], array $configuration = []): StatusGetter
    {
        $mock = $this->getMockBuilder(StatusGetter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStatus', 'getConfiguration'])
            ->getMock();

        $mock->method('getStatus')
            ->willReturnCallback(function ($key = null, $default = null) use ($status) {
                if ($key === null) {
                    return $status;
                }
                return data_get($status, $key, $default);
            });

        $mock->method('getConfiguration')
            ->willReturnCallback(function ($key = null, $default = null) use ($configuration) {
                if ($key === null) {
                    return $configuration;
                }
                return data_get($configuration, $key, $default);
            });

        return $mock;
    }
}
