<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class HitsCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addCounter('opcache_hits_total')
            ->help('The total number of OPcache cache hits.')
            ->value($this->status('opcache_statistics.hits'));
    }
}
