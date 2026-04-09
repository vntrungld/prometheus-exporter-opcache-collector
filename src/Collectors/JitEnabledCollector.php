<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Prometheus;

class JitEnabledCollector extends BaseCollector
{
    public function register(Prometheus $prometheus): void
    {
        if (! $this->status() || ! $this->status('jit')) {
            return;
        }

        $prometheus->addGauge('opcache_jit_enabled')
            ->help('Whether JIT is enabled.')
            ->value($this->status('jit.enabled') ? 1 : 0);
    }
}
