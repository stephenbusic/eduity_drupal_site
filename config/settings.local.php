<?php
/**
 * Local development configuration.
 *
 * This file is included from settings.php
 */

/**
 * Reverse proxy configuration for Traefik
 */
$settings['reverse_proxy'] = TRUE;
$settings['reverse_proxy_addresses'] = ['172.16.0.0/12', '10.0.0.0/8'];

/**
 * Trusted host patterns
 */
$settings['trusted_host_patterns'] = [
  '^chattanooga\.digital$',
];
