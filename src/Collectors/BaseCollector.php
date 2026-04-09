<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector\Collectors;

use Vntrungld\PrometheusExporter\Collectors\Collector;
use Vntrungld\PrometheusExporterOpcacheCollector\StatusGetter;

abstract class BaseCollector implements Collector
{
    /** @var StatusGetter */
    protected $getter;

    public function __construct(StatusGetter $getter)
    {
        $this->getter = $getter;
    }

    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    protected function status($key = null, $default = null)
    {
        return $this->getter->getStatus($key, $default);
    }

    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    protected function config($key = null, $default = null)
    {
        return $this->getter->getConfiguration($key, $default);
    }

    /**
     * Get a configuration directive value.
     *
     * OPcache directive keys contain literal dots (e.g. "opcache.memory_consumption")
     * which data_get interprets as nested access. This method accesses directives directly.
     *
     * @param string $directive
     * @param mixed $default
     * @return mixed
     */
    protected function directive($directive, $default = null)
    {
        $directives = $this->config('directives');

        if (! is_array($directives)) {
            return $default;
        }

        return $directives[$directive] ?? $default;
    }
}
