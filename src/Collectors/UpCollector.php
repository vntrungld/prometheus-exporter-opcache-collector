<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class UpCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        $prometheus->addGauge('opcache_up')
            ->help('Whether OPcache is available.')
            ->value($this->status() ? 1.0 : 0.0);
    }
}
