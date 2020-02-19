<?php
/**
 * Plugin Name: RedPic Related
 * Description: Show related posts
 * Plugin URI:
 * Author URI:
 * Author:      Nahaba Vadilslav
 * Version:     1.0
 * Text Domain: redpic-related
 */

/*  Copyright ГОД  Nahaba Vladislav  (email: nahabavladislav@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'REDPIC_RELATED_VERSION', '1.0' );
define( 'REDPIC_RELATED_SLUG', 'redpic-related' );
define( 'RELATED_DIR', plugin_dir_path(__FILE__) );
define( 'RELATED_URL', plugin_dir_url(__FILE__) );

register_activation_hook( __FILE__, function() {
	require_once __DIR__ . '/lib/class-redpic-related-init.php';
	$initialize = new Redpic_Related_Init();
	$initialize->activation();
});

register_deactivation_hook( __FILE__, function() {
	require_once __DIR__ . '/lib/class-redpic-related-init.php';
	$initialize = new Redpic_Related_Init();
	$initialize->deactivation();
});

add_action( 'wp_enqueue_scripts', 'add_related_style' );
function add_related_style() {
	wp_enqueue_style( REDPIC_RELATED_SLUG . '-style', plugin_dir_url(__FILE__) . 'css/style.css' );
}


require_once __DIR__ . '/lib/class-redpic-related-frontend.php';
new Redpic_Related_Frontend();

if ( is_admin() ) {
	/**
	 * Admin menu initialize
	 */
	function redpic_related_admin() {
		require_once __DIR__ . '/lib/class-redpic-related-admin-panel.php';
		Redpic_Related_Admin::init();
	}
	redpic_related_admin();
}
