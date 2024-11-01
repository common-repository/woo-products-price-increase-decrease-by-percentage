<?php
/*
Plugin Name: WooCommerce Products Price Increase / Decrease by Percentage
Plugin URI: 
Description: Automatically update your store prices for all products at once by percentage.
Version: 1.0.0
Author: Alfio Salanitri
Author URI: https://www.alfiosalanitri.it
Text Domain: woocommerce-ppidbp
Domain Path: /languages/
*/
defined('ABSPATH') or exit;
require(dirname(__FILE__) . '/vendor/autoload.php');
define('WC_PPIDBP', plugin_basename(__FILE__));
define('WC_PPIDBP_V', '1.0.0');
define('WC_PPIDBP_URL', plugin_dir_url(__FILE__));
define('WC_PPIDBP_DIR', plugin_dir_path(__FILE__));
/**
 * Avvio il plugin
 */
add_action('plugins_loaded', array('WooCommercePPIDBP\WcppidbpSetup', 'getInstance'));