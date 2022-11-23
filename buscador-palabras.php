<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Buscador_Palabras
 *
 * @wordpress-plugin
 * Plugin Name:       Buscador de Palabras
 * Description:       A simple Buscador de Palabras. To use it, copy this shortcode: [buscador_palabras], and paste on the page that you want to display the plugin. You can use the component Shortcode of Elementor or the text editor of Wordpress.
 * Version:           1.0.0
 * Author:            Heloisa Araújo
 * Author URI:        https://www.upwork.com/freelancers/~01ecaeba25b16c5067
 * Text Domain:       buscador-palabras
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BUSCADOR_PALABRAS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buscador-palabras-activator.php
 */
function activate_buscador_palabras() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buscador-palabras-activator.php';
	Buscador_Palabras_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buscador-palabras-deactivator.php
 */
function deactivate_buscador_palabras() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buscador-palabras-deactivator.php';
	Buscador_Palabras_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buscador_palabras' );
register_deactivation_hook( __FILE__, 'deactivate_buscador_palabras' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buscador-palabras.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buscador_palabras() {

	$plugin = new Buscador_Palabras();
	$plugin->run();

}
run_buscador_palabras();
