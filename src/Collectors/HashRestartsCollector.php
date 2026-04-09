<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class HashRestartsCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_hash_restarts')
            ->help('The number of OPcache hash table overflow restarts.')
            ->value((float) $this->status('opcache_statistics.hash_restarts'));
    }
}
