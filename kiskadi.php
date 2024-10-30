<?php
/**
 * Plugin Name:     Kiskadi
 * Plugin URI:      https://kiskadi.com/
 * Description:     Kiskadi CRM WordPress integration.
 * Author:          Kiskadi CRM
 * Author URI:      https://kiskadi.com/
 * Text Domain:     kiskadi
 * Domain Path:     /languages
 * Version:         1.3.0
 *
 * @package         Kiskadi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'autoload.php';

add_action( 'plugins_loaded', array( \Kiskadi\Kiskadi::class, 'instance' ) );
