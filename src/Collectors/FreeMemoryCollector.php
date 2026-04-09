<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class FreeMemoryCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_free_memory_bytes')
            ->help('The amount of free memory for OPcache.')
            ->value($this->status('memory_usage.free_memory'));
    }
}
