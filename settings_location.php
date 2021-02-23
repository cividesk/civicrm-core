<?php
/**
 * Cividesk hosting environment
 */
$here = dirname( __FILE__ );
$directories = array(
  '/home' . DIRECTORY_SEPARATOR . $_SERVER['DOMAIN'] . DIRECTORY_SEPARATOR . 'www',
  '/home' . DIRECTORY_SEPARATOR . $_SERVER['DOMAIN'] . DIRECTORY_SEPARATOR . 'www/wordpress/wp-content/plugins/civicrm',
  dirname($here) . DIRECTORY_SEPARATOR . 'wordpress/wp-content/plugins/civicrm',
  $here . DIRECTORY_SEPARATOR . 'standalone',
);
foreach ($directories as $directory) {
  if (file_exists($directory . DIRECTORY_SEPARATOR . 'civicrm.settings.php' ) && !defined( 'CIVICRM_CONFDIR' )) {
    define( 'CIVICRM_CONFDIR', $directory );
  }
}
