<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class ConfigMemoryLimitCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_config_memory_limit_bytes')
            ->help('The configured OPcache memory limit in bytes.')
            ->value((float) $this->directive('opcache.memory_consumption'));
    }
}
