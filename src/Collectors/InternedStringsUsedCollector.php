<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class InternedStringsUsedCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_interned_strings_used_bytes')
            ->help('The amount of used memory for interned strings.')
            ->value((float) $this->status('interned_strings_usage.used_memory'));
    }
}
