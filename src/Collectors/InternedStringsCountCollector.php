<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class InternedStringsCountCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_interned_strings_count')
            ->help('The number of interned strings.')
            ->value($this->status('interned_strings_usage.number_of_strings'));
    }
}
