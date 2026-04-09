<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class MaxCachedKeysCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_max_cached_keys')
            ->help('The maximum number of keys in the OPcache hash table.')
            ->value($this->status('opcache_statistics.max_cached_keys'));
    }
}
