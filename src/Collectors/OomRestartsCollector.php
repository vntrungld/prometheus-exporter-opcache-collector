<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class OomRestartsCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_oom_restarts')
            ->help('The number of OPcache out-of-memory restarts.')
            ->value((float) $this->status('opcache_statistics.oom_restarts'));
    }
}
