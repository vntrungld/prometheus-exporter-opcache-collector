<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class ConfigMaxAcceleratedFilesCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_config_max_accelerated_files')
            ->help('The configured maximum number of accelerated files.')
            ->value($this->directive('opcache.max_accelerated_files'));
    }
}
