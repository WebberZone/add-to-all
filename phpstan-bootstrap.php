<?php
/**
 * PHPStan bootstrap file
 *
 * @package WebberZone\Snippetz
 */

if ( ! defined( 'WZ_SNIPPETZ_VERSION' ) ) {
	define( 'WZ_SNIPPETZ_VERSION', '2.3.0' );
}

if ( ! defined( 'WZ_SNIPPETZ_FILE' ) ) {
	define( 'WZ_SNIPPETZ_FILE', __FILE__ );
}

if ( ! defined( 'WZ_SNIPPETZ_DIR' ) ) {
	define( 'WZ_SNIPPETZ_DIR', plugin_dir_path( WZ_SNIPPETZ_FILE ) );
}

if ( ! defined( 'WZ_SNIPPETZ_URL' ) ) {
	define( 'WZ_SNIPPETZ_URL', plugin_dir_url( WZ_SNIPPETZ_FILE ) );
}
