<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class ConfigInternedStringsBufferCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status()) {
            return;
        }

        $value = $this->directive('opcache.interned_strings_buffer');

        $prometheus->addGauge('opcache_config_interned_strings_buffer_bytes')
            ->help('The configured interned strings buffer size in bytes.')
            ->value((float) $value * 1024 * 1024);
    }
}
