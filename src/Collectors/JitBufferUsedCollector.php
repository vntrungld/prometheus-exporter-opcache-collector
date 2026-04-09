<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class JitBufferUsedCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status() || ! $this->status('jit')) {
            return;
        }

        $prometheus->addGauge('opcache_jit_buffer_used_bytes')
            ->help('The amount of used JIT buffer.')
            ->value($this->status('jit.buffer_used'));
    }
}
