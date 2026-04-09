<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\CachedKeysCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\CachedScriptsCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\ConfigInternedStringsBufferCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\ConfigJitBufferSizeCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\ConfigMaxAcceleratedFilesCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\ConfigMemoryLimitCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\FreeMemoryCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\HashRestartsCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\HitsCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\InternedStringsBufferCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\InternedStringsCountCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\InternedStringsFreeCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\InternedStringsUsedCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\JitBufferFreeCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\JitBufferSizeCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\JitBufferUsedCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\JitEnabledCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\ManualRestartsCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\MaxCachedKeysCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\MissesCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\OomRestartsCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\UpCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\UsedMemoryCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\WastedMemoryCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\Collectors\WastedMemoryPercentageCollector;
use Vntrungld\PrometheusExporterOpcacheCollector\OpcacheCollectorSet;

class OpcacheCollectorSetTest extends TestCase
{
    public function testCollectorsReturnsAllExpectedCollectors(): void
    {
        $collectorSet = new OpcacheCollectorSet();

        $this->assertSame([
            UpCollector::class,
            UsedMemoryCollector::class,
            FreeMemoryCollector::class,
            WastedMemoryCollector::class,
            WastedMemoryPercentageCollector::class,
            CachedScriptsCollector::class,
            HitsCollector::class,
            MissesCollector::class,
            CachedKeysCollector::class,
            MaxCachedKeysCollector::class,
            OomRestartsCollector::class,
            HashRestartsCollector::class,
            ManualRestartsCollector::class,
            InternedStringsBufferCollector::class,
            InternedStringsUsedCollector::class,
            InternedStringsFreeCollector::class,
            InternedStringsCountCollector::class,
            JitEnabledCollector::class,
            JitBufferSizeCollector::class,
            JitBufferUsedCollector::class,
            JitBufferFreeCollector::class,
            ConfigMemoryLimitCollector::class,
            ConfigMaxAcceleratedFilesCollector::class,
            ConfigInternedStringsBufferCollector::class,
            ConfigJitBufferSizeCollector::class,
        ], $collectorSet->collectors());
    }

    public function testAllCollectorClassesExist(): void
    {
        $collectorSet = new OpcacheCollectorSet();

        foreach ($collectorSet->collectors() as $collector) {
            $this->assertTrue(class_exists($collector), "Class {$collector} does not exist");
        }
    }
}
