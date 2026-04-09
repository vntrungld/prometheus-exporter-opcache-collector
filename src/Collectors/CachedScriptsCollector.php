<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class CachedScriptsCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_cached_scripts')
            ->help('The number of scripts cached by OPcache.')
            ->value((float) $this->status('opcache_statistics.num_cached_scripts'));
    }
}
