<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class JitBufferSizeCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status() || ! $this->status('jit')) {
            return;
        }

        $prometheus->addGauge('opcache_jit_buffer_size_bytes')
            ->help('The total JIT buffer size.')
            ->value($this->status('jit.buffer_size'));
    }
}
