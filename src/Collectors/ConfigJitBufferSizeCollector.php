<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class ConfigJitBufferSizeCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status() || ! $this->status('jit')) {
            return;
        }

        $prometheus->addGauge('opcache_config_jit_buffer_size_bytes')
            ->help('The configured JIT buffer size in bytes.')
            ->value((float) $this->directive('opcache.jit_buffer_size'));
    }
}
