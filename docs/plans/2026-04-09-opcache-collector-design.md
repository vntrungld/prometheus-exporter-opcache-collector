# OPcache Collector Package Design

## Overview

A new Laravel package `vntrungld/prometheus-exporter-opcache-collector` that collects PHP OPcache metrics and exposes them as Prometheus metrics via `vntrungld/prometheus-exporter`.

Follows the same architecture as `vntrungld/prometheus-exporter-php-fpm-collector`.

## Architecture

- **`StatusGetter`** — wraps `opcache_get_status(false)` and `opcache_get_configuration()` in a single call during construction. Returns `false` when OPcache is unavailable. Bound as a singleton in the service provider.
- **`OpcacheCollectorSet`** — implements `CollectorSet`, registers all collectors.
- **`BaseCollector`** — abstract class that receives the `StatusGetter` instance.
- **`PrometheusExporterOpcacheCollectorServiceProvider`** — auto-discovered by Laravel, binds `StatusGetter` as singleton.

## Requirements

- `php: ^7.2|^8.0`
- `illuminate/support: ^6.0|^7.0|^8.0|^9.0|^10.0|^11.0|^12.0`
- `vntrungld/prometheus-exporter: ^1.1`

## Metrics

All metrics use the `opcache_` prefix. All are gauges unless noted.

### Up

| Metric | Description |
|--------|-------------|
| `opcache_up` | `1` if OPcache is available, `0` if not. All other collectors skip when `0`. |

### Memory Usage (from `memory_usage`)

| Metric | Description |
|--------|-------------|
| `opcache_used_memory_bytes` | Used memory in bytes |
| `opcache_free_memory_bytes` | Free memory in bytes |
| `opcache_wasted_memory_bytes` | Wasted memory in bytes |
| `opcache_wasted_memory_percentage` | Wasted memory as percentage |

### Cache Statistics (from `opcache_statistics`)

| Metric | Type | Description |
|--------|------|-------------|
| `opcache_cached_scripts` | gauge | Number of cached scripts |
| `opcache_hits_total` | counter | Cache hits |
| `opcache_misses_total` | counter | Cache misses |
| `opcache_cached_keys` | gauge | Number of keys in hash table |
| `opcache_max_cached_keys` | gauge | Max slots in hash table |
| `opcache_oom_restarts` | gauge | Out-of-memory restart count |
| `opcache_hash_restarts` | gauge | Hash table overflow restart count |
| `opcache_manual_restarts` | gauge | Manual restart count |

### Interned Strings (from `interned_strings_usage`)

| Metric | Description |
|--------|-------------|
| `opcache_interned_strings_buffer_bytes` | Total buffer size |
| `opcache_interned_strings_used_bytes` | Used buffer |
| `opcache_interned_strings_free_bytes` | Free buffer |
| `opcache_interned_strings_count` | Number of interned strings |

### JIT (from `jit`, PHP 8.0+ only — skip if not present)

| Metric | Description |
|--------|-------------|
| `opcache_jit_enabled` | `1` or `0` |
| `opcache_jit_buffer_size_bytes` | Total JIT buffer size |
| `opcache_jit_buffer_used_bytes` | Used JIT buffer |
| `opcache_jit_buffer_free_bytes` | Free JIT buffer |

### Configuration Limits (from `opcache_get_configuration()`)

| Metric | Source | Description |
|--------|--------|-------------|
| `opcache_config_memory_limit_bytes` | `opcache.memory_consumption` | Memory limit |
| `opcache_config_max_accelerated_files` | `opcache.max_accelerated_files` | Max cached files |
| `opcache_config_interned_strings_buffer_bytes` | `opcache.interned_strings_buffer` (MB -> bytes) | Interned strings buffer limit |
| `opcache_config_jit_buffer_size_bytes` | `opcache.jit_buffer_size` (PHP 8.0+) | JIT buffer limit |

### Example PromQL Queries

```promql
# Memory utilization %
opcache_used_memory_bytes / opcache_config_memory_limit_bytes * 100

# Script slots utilization %
opcache_cached_scripts / opcache_config_max_accelerated_files * 100

# Interned strings utilization %
opcache_interned_strings_used_bytes / opcache_config_interned_strings_buffer_bytes * 100
```

## Error Handling

- `StatusGetter` calls `opcache_get_status(false)` — passing `false` skips the per-script file list.
- When OPcache is unavailable, `StatusGetter` stores `false`. `UpCollector` reports `0`, all other collectors return early with no metrics.
- JIT collectors check for the `jit` key in the status array and skip silently if absent (PHP < 8.0).
- Config collectors check for JIT config keys and skip if absent (PHP < 8.0).

## File Structure

```
src/
  StatusGetter.php
  OpcacheCollectorSet.php
  PrometheusExporterOpcacheCollectorServiceProvider.php
  Collectors/
    BaseCollector.php
    UpCollector.php
    UsedMemoryCollector.php
    FreeMemoryCollector.php
    WastedMemoryCollector.php
    WastedMemoryPercentageCollector.php
    CachedScriptsCollector.php
    HitsCollector.php
    MissesCollector.php
    CachedKeysCollector.php
    MaxCachedKeysCollector.php
    OomRestartsCollector.php
    HashRestartsCollector.php
    ManualRestartsCollector.php
    InternedStringsBufferCollector.php
    InternedStringsUsedCollector.php
    InternedStringsFreeCollector.php
    InternedStringsCountCollector.php
    JitEnabledCollector.php
    JitBufferSizeCollector.php
    JitBufferUsedCollector.php
    JitBufferFreeCollector.php
    ConfigMemoryLimitCollector.php
    ConfigMaxAcceleratedFilesCollector.php
    ConfigInternedStringsBufferCollector.php
    ConfigJitBufferSizeCollector.php
tests/
  Unit/
    PackageTest.php
    Collectors/
      (one test per collector)
```

## Testing

- Unit tests mock `StatusGetter` to return sample status/config arrays.
- Test cases cover: OPcache available, OPcache unavailable, JIT present, JIT absent.
- GitHub Actions CI matrix: PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4.
