<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class UsedMemoryCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_used_memory_bytes')
            ->help('The amount of used memory by OPcache.')
            ->value((float) $this->status('memory_usage.used_memory'));
    }
}
