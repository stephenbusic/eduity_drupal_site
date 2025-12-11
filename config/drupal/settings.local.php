<?php

/**
 * Local development configuration.
 * This file is included by settings.php
 */

/**
 * Trusted host patterns for eduity.net
 */
$settings['trusted_host_patterns'] = [
  '^eduity\.net$',
  '^www\.eduity\.net$',
];

/**
 * Reverse proxy configuration for Traefik
 */
$settings['reverse_proxy'] = TRUE;
$settings['reverse_proxy_addresses'] = [
  '172.16.0.0/12',
  '10.0.0.0/8',
];

/**
 * Skip file system permissions check (Docker volumes)
 */
$settings['skip_permissions_hardening'] = TRUE;
