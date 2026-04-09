<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class InternedStringsBufferCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $prometheus->addGauge('opcache_interned_strings_buffer_bytes')
            ->help('The total buffer size for interned strings.')
            ->value((float) $this->status('interned_strings_usage.buffer_size'));
    }
}
