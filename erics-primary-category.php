<?php
/**
 * Plugin Name: Eric's Primary Category
 * Plugin URI:
 * Description: A plugin to designate a primary category for filtering posts.
 * Author: Eric Montzka
 * Version: 1.0
 * Author URI: http://ericmontzka.com
 * Text Domain: primary-category
 **/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/category-setup.php';

Primary_Category_Setup::run();
