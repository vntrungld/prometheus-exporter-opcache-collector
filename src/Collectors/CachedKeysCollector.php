<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class CachedKeysCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_cached_keys')
            ->help('The number of keys in the OPcache hash table.')
            ->value((float) $this->status('opcache_statistics.num_cached_keys'));
    }
}
