<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class ManualRestartsCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_manual_restarts')
            ->help('The number of OPcache manual restarts.')
            ->value($this->status('opcache_statistics.manual_restarts'));
    }
}
