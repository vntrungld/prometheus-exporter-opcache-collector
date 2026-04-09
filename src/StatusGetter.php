<?php

namespace Vntrungld\PrometheusExporterOpcacheCollector;

class StatusGetter
{
    /** @var array|false */
    protected $status;

    /** @var array|false */
    protected $configuration;

    public function __construct()
    {
        $this->status = function_exists('opcache_get_status') ? opcache_get_status(false) : false;
        $this->configuration = function_exists('opcache_get_configuration') ? opcache_get_configuration() : false;
    }

    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getStatus($key = null, $default = null)
    {
        if ($key === null) {
            return $this->status;
        }

        return data_get($this->status, $key, $default);
    }

    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfiguration($key = null, $default = null)
    {
        if ($key === null) {
            return $this->configuration;
        }

        return data_get($this->configuration, $key, $default);
    }
}
