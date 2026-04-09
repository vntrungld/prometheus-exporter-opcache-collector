# Prometheus Exporter OPcache Collector

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vntrungld/prometheus-exporter-opcache-collector.svg?style=flat-square)](https://packagist.org/packages/vntrungld/prometheus-exporter-opcache-collector)
[![Total Downloads](https://img.shields.io/packagist/dt/vntrungld/prometheus-exporter-opcache-collector.svg?style=flat-square)](https://packagist.org/packages/vntrungld/prometheus-exporter-opcache-collector)
[![Tests](https://github.com/vntrungld/prometheus-exporter-opcache-collector/actions/workflows/tests.yml/badge.svg)](https://github.com/vntrungld/prometheus-exporter-opcache-collector/actions/workflows/tests.yml)
[![License](https://img.shields.io/packagist/l/vntrungld/prometheus-exporter-opcache-collector.svg?style=flat-square)](https://packagist.org/packages/vntrungld/prometheus-exporter-opcache-collector)

An OPcache metrics collector for [Prometheus Exporter](https://github.com/vntrungld/prometheus-exporter) in Laravel applications. This package collects OPcache memory usage, cache statistics, interned strings, JIT status, and configuration limits, exposing them in Prometheus format.

## Requirements

| Laravel | PHP       | Package |
|---------|-----------|---------|
| 6.x     | 7.2 - 8.0 | 1.x     |
| 7.x     | 7.2 - 8.0 | 1.x     |
| 8.x     | 7.3 - 8.1 | 1.x     |
| 9.x     | 8.0 - 8.2 | 1.x     |
| 10.x    | 8.1 - 8.3 | 1.x     |
| 11.x    | 8.2 - 8.4 | 1.x     |
| 12.x    | 8.2 - 8.4 | 1.x     |

## Installation

Install via Composer:

```bash
composer require vntrungld/prometheus-exporter-opcache-collector
```

The package uses Laravel's auto-discovery, so the service provider will be automatically registered.

## Configuration

Add the collector set to your `prometheus-exporter` configuration file (`config/prometheus-exporter.php`):

```php
return [
    // ... other config options

    'collector_sets' => [
        \Vntrungld\PrometheusExporterOpcacheCollector\OpcacheCollectorSet::class,
        // ... other collector sets
    ],
];
```

## Available Metrics

### Up Status

| Metric Name    | Type  | Description                                |
|---------------|-------|--------------------------------------------|
| `opcache_up`   | Gauge | Whether OPcache is available (1=up, 0=down) |

### Memory Usage

| Metric Name                        | Type  | Description                          |
|-----------------------------------|-------|--------------------------------------|
| `opcache_used_memory_bytes`        | Gauge | Used memory in bytes                 |
| `opcache_free_memory_bytes`        | Gauge | Free memory in bytes                 |
| `opcache_wasted_memory_bytes`      | Gauge | Wasted memory in bytes               |
| `opcache_wasted_memory_percentage` | Gauge | Wasted memory as percentage          |

### Cache Statistics

| Metric Name                | Type    | Description                              |
|---------------------------|---------|------------------------------------------|
| `opcache_cached_scripts`   | Gauge   | Number of cached scripts                 |
| `opcache_hits_total`       | Counter | Total cache hits                         |
| `opcache_misses_total`     | Counter | Total cache misses                       |
| `opcache_cached_keys`      | Gauge   | Number of keys in the hash table         |
| `opcache_max_cached_keys`  | Gauge   | Maximum slots in the hash table          |
| `opcache_oom_restarts`     | Gauge   | Out-of-memory restart count              |
| `opcache_hash_restarts`    | Gauge   | Hash table overflow restart count        |
| `opcache_manual_restarts`  | Gauge   | Manual restart count                     |

### Interned Strings

| Metric Name                              | Type  | Description                    |
|-----------------------------------------|-------|--------------------------------|
| `opcache_interned_strings_buffer_bytes`  | Gauge | Total buffer size              |
| `opcache_interned_strings_used_bytes`    | Gauge | Used buffer memory             |
| `opcache_interned_strings_free_bytes`    | Gauge | Free buffer memory             |
| `opcache_interned_strings_count`         | Gauge | Number of interned strings     |

### JIT (PHP 8.0+)

These metrics are only available when JIT is present. They are silently skipped on PHP < 8.0.

| Metric Name                    | Type  | Description              |
|-------------------------------|-------|--------------------------|
| `opcache_jit_enabled`          | Gauge | Whether JIT is enabled   |
| `opcache_jit_buffer_size_bytes`| Gauge | Total JIT buffer size    |
| `opcache_jit_buffer_used_bytes`| Gauge | Used JIT buffer          |
| `opcache_jit_buffer_free_bytes`| Gauge | Free JIT buffer          |

### Configuration Limits

These gauges expose OPcache configuration values, useful for computing utilization ratios in PromQL.

| Metric Name                                    | Type  | Source                             |
|-----------------------------------------------|-------|------------------------------------|
| `opcache_config_memory_limit_bytes`            | Gauge | `opcache.memory_consumption`       |
| `opcache_config_max_accelerated_files`         | Gauge | `opcache.max_accelerated_files`    |
| `opcache_config_interned_strings_buffer_bytes` | Gauge | `opcache.interned_strings_buffer`  |
| `opcache_config_jit_buffer_size_bytes`         | Gauge | `opcache.jit_buffer_size` (8.0+)  |

## Example PromQL Queries

```promql
# Memory utilization %
opcache_used_memory_bytes / opcache_config_memory_limit_bytes * 100

# Script slots utilization %
opcache_cached_scripts / opcache_config_max_accelerated_files * 100

# Interned strings utilization %
opcache_interned_strings_used_bytes / opcache_config_interned_strings_buffer_bytes * 100

# Cache hit rate %
opcache_hits_total / (opcache_hits_total + opcache_misses_total) * 100
```

## Example Output

```
# HELP opcache_up Whether OPcache is available.
# TYPE opcache_up gauge
opcache_up 1

# HELP opcache_used_memory_bytes The amount of used memory by OPcache.
# TYPE opcache_used_memory_bytes gauge
opcache_used_memory_bytes 67108864

# HELP opcache_free_memory_bytes The amount of free memory for OPcache.
# TYPE opcache_free_memory_bytes gauge
opcache_free_memory_bytes 67108864

# HELP opcache_cached_scripts The number of scripts cached by OPcache.
# TYPE opcache_cached_scripts gauge
opcache_cached_scripts 150

# HELP opcache_hits_total The total number of OPcache cache hits.
# TYPE opcache_hits_total counter
opcache_hits_total 50000

# HELP opcache_config_memory_limit_bytes The configured OPcache memory limit in bytes.
# TYPE opcache_config_memory_limit_bytes gauge
opcache_config_memory_limit_bytes 134217728
```

## OPcache Configuration

For this collector to work, the OPcache extension must be enabled. Ensure your `php.ini` has:

```ini
opcache.enable=1
```

The collector uses PHP's built-in `opcache_get_status()` and `opcache_get_configuration()` functions. When OPcache is not available, the `opcache_up` metric reports `0` and all other metrics are silently skipped.

## Testing

Run the test suite:

```bash
composer test
```

Or with PHPUnit directly:

```bash
vendor/bin/phpunit
```

## Changelog

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email vn.trungld@gmail.com instead of using the issue tracker.

## Credits

- [vntrungld](https://github.com/vntrungld)
- [All Contributors](../../contributors)

## License

MIT. Please see the [license file](license.md) for more information.
