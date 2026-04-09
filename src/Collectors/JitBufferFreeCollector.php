<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class JitBufferFreeCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status() || ! $this->status('jit')) {
            return;
        }

        $prometheus->addGauge('opcache_jit_buffer_free_bytes')
            ->help('The amount of free JIT buffer.')
            ->value($this->status('jit.buffer_free'));
    }
}
