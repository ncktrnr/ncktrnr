<?php

/**
 * @file
 * Includes the autoloader created by Composer.
 *
 * Dual-path: works on the local layout (vendor/ beside web/) and on
 * production, where the shared vendor lives at ../../ncktrnr/vendor.
 * This means an accidentally deployed copy of this file cannot take the
 * live site down. See docs/deployment.md.
 *
 * @see composer.json
 * @see index.php
 * @see core/install.php
 * @see core/rebuild.php
 */

foreach ([
  __DIR__ . '/../vendor/autoload.php',
  __DIR__ . '/../../ncktrnr/vendor/autoload.php',
] as $autoloader) {
  if (is_file($autoloader)) {
    return require $autoloader;
  }
}

throw new RuntimeException('Composer autoloader not found. Run "composer install".');
