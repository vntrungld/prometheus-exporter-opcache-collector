<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class UpCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        $up = (bool) $this->status();

        $prometheus->addGauge('opcache_up')
            ->help('Whether OPcache is available.')
            ->value($up);
    }
}
