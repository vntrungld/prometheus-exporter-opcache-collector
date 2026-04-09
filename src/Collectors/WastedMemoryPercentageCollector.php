<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class WastedMemoryPercentageCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_wasted_memory_percentage')
            ->help('The percentage of wasted memory by OPcache.')
            ->value($this->status('memory_usage.current_wasted_percentage'));
    }
}
