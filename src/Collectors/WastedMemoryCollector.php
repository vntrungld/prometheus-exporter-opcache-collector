<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class WastedMemoryCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_wasted_memory_bytes')
            ->help('The amount of wasted memory by OPcache.')
            ->value((float) $this->status('memory_usage.wasted_memory'));
    }
}
