<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class InternedStringsFreeCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_interned_strings_free_bytes')
            ->help('The amount of free memory for interned strings.')
            ->value((float) $this->status('interned_strings_usage.free_memory'));
    }
}
