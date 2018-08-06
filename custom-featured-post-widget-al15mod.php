<?php
/**
 * Custom Featured Post Widget
 *
 * @package           Custom_Featured_Post_Widget_AL15MOD
 * @author            Pontus Sundén/GU
 * @license           GPL-2.0+
 * @link              http://gamajo.com/
 * @copyright         2013 Gary Jones, Gamajo Tech, 2018 Pontus Sundén
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Featured Post Widget AL15MOD
 * Plugin URI:        http://gamajo.com/
 * Description:       Amends the order of output of the Genesis Featured Post Widget.
 * Version:           0.4
 * Author:            Pontus Sundén
 * Author URI:        http://gu.se
 * Text Domain:       custom-featured-post-widget
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'widgets_init', 'custom_featured_post_widget_al15mod', 15 );
/**
 * Register widgets for use in a Genesis child theme.
 *
 * @since 1.0.0
 */
function custom_featured_post_widget_al15mod() {
	require plugin_dir_path( __FILE__ ) . 'class-custom-featured-post-al15mod.php';
	unregister_widget( 'Genesis_Featured_Post' );
	register_widget( 'Custom_Featured_Post' );
}

/* Note: If the require is happening too late, then move into a new function, hooked to genesis_setup, 15 instead:

add_action( 'genesis_setup', 'custom_featured_post_load_class', 15 );
function custom_featured_post_load_class() {
	require plugin_dir_path( __FILE__ ) . 'class-custom-featured-post.php';
}

*/
