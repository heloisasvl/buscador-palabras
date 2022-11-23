<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Buscador_Palabras
 * @subpackage Buscador_Palabras/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buscador_Palabras
 * @subpackage Buscador_Palabras/admin
 * @author     Your Name <email@example.com>
 */
class Buscador_Palabras_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $buscador_palabras    The ID of this plugin.
	 */
	private $buscador_palabras;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $buscador_palabras       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $buscador_palabras, $version ) {

		$this->buscador_palabras = $buscador_palabras;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buscador_Palabras_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buscador_Palabras_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->buscador_palabras, plugin_dir_url( __FILE__ ) . 'css/buscador-palabras-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buscador_Palabras_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buscador_Palabras_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->buscador_palabras, plugin_dir_url( __FILE__ ) . 'js/buscador-palabras-admin.js', array( 'jquery' ), $this->version, false );

	}

}
