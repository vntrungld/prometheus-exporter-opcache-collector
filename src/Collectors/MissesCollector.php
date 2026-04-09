<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class MissesCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addCounter('opcache_misses_total')
            ->help('The total number of OPcache cache misses.')
            ->value($this->status('opcache_statistics.misses'));
    }
}
