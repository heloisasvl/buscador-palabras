<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Buscador_Palabras
 * @subpackage Buscador_Palabras/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buscador_Palabras
 * @subpackage Buscador_Palabras/public
 * @author     Your Name <email@example.com>
 */
class Buscador_Palabras_Public {

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
	 * @param      string    $buscador_palabras       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $buscador_palabras, $version ) {

		$this->buscador_palabras = $buscador_palabras;
		$this->version = $version;

		$this->palabras = array();
		$this->dic = file(plugin_dir_url( __FILE__ ) . 'data/dicionario.txt', FILE_IGNORE_NEW_LINES);
		$this->dicionarioLength = count($this->dic);
		$this->palabrasLength = 0;
		$this->title = '';
		$this->buscador_palabras_params = array(
			'i' => !empty($_GET['i']) ? sanitize_text_field($_GET['i']) : '',
			'f' => !empty($_GET['f']) ? sanitize_text_field($_GET['f']) : '',
			'ms' => !empty($_GET['ms']) ? sanitize_textarea_field($_GET['ms']) : '',
			'mns' => !empty($_GET['mns']) ? sanitize_textarea_field($_GET['mns']) : '',
			'm' => !empty($_GET['m']) ? sanitize_textarea_field($_GET['m']) : '',
			'mn' => !empty($_GET['mn']) ? sanitize_textarea_field($_GET['mn']) : '',
			'fs' => !empty($_GET['fs']) ? sanitize_textarea_field($_GET['fs']) : 0,
			'fnl' => !empty($_GET['fnl']) ? sanitize_textarea_field($_GET['fnl']) : 0,
			'fa' => !empty($_GET['fa']) ? sanitize_textarea_field($_GET['fa']) : 0
		);
		
		$this->isValid = array_reduce($this->buscador_palabras_params, function($total, $param) {
			$total += ($param ? 1 : 0);
			return $total;
		}, 0);

		$this->buscador_palabras_resultados();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->buscador_palabras, plugin_dir_url( __FILE__ ) . 'css/buscador-palabras-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bs5', plugin_dir_url( __FILE__ ) . 'css/bootstrap-isolated.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->buscador_palabras, plugin_dir_url( __FILE__ ) . 'js/buscador-palabras-public.js', array( 'jquery' ), $this->version, false );
	}

	public function buscador_palabras_filter_wp_title() {
	  return $this->title;
	}

	/**
	 * Callback function for the public page.
	 *
	 * @since    1.0.0
	 */
	public function buscador_palabras_shortcode( $atts ) {

		ob_start();

		include_once( 'partials/buscador-palabras-public-display.php' );

		$output = ob_get_contents();

		ob_end_clean();

		return $output;
	}

	public function buscador_palabras_resultados() {
			if ($this->isValid) {
				$this->dicionarioLength = count($this->dic);

				// Palabras que empiezan con
				$this->palabras = $this->empiezan($this->dic, $this->buscador_palabras_params['i']);
				
				// Palabras que acaban con
				$this->palabras = $this->acaban($this->palabras, $this->buscador_palabras_params['f']);

				// Palabras que tengan las letras
				$this->palabras = $this->tengan($this->palabras, $this->buscador_palabras_params['ms']);

				// Palabras que no tengan las letras
				$this->palabras = $this->notengan($this->palabras, $this->buscador_palabras_params['mns']);

				// Palabras que tengan la cadena
				$this->palabras = $this->cadena($this->palabras, $this->buscador_palabras_params['m']);

				// Palabras que no tengan la cadena
				$this->palabras = $this->nocadena($this->palabras, $this->buscador_palabras_params['mn']);

				// Número de sílabas
				$this->palabras = $this->silabas($this->palabras, $this->buscador_palabras_params['fs']);

				// Número de letras
				$this->palabras = $this->letras($this->palabras, $this->buscador_palabras_params['fnl']);

				// Filtro anti acentos
				$this->palabras = $this->antiacentos($this->palabras, $this->buscador_palabras_params['fa']);

				$this->palabrasLength = count($this->palabras);

				// Group
				$this->palabras = array_reduce($this->palabras, function($group, $palabra) {
						$group[mb_strlen($palabra)] = $group[mb_strlen($palabra)] ?? [];
						array_push($group[mb_strlen($palabra)], $palabra);
		        return $group;
		    }, array());

				// Sort
				ksort($this->palabras);

				// Change Meta Title HTML
				$this->title = $this->palabrasLength . ' Palabras que empiezan con "' . $this->buscador_palabras_params['i'] .'", y terminen en "' . $this->buscador_palabras_params['f'] .'" y contengan las letras "' . $this->buscador_palabras_params['ms'] .'" y no contengan las letras "' . $this->buscador_palabras_params['mns'] .'"';	
			}
	}

	public function empiezan($arr, $query) {
		$palabras = array();

		for ($i = 0; $i < count($arr); $i++) {
			if(str_starts_with($this->removeAccents($arr[$i]), $this->removeAccents($query))) {
				array_push($palabras, $arr[$i]);
			}
		}

		return $palabras;
	}

	public function acaban($arr, $query) {
		$palabras = array();

		for ($i = 0; $i < count($arr); $i++) {
			if(str_ends_with($this->removeAccents($arr[$i]), $this->removeAccents($query))) {
				array_push($palabras, $arr[$i]);
			}
		}

		return $palabras;
	}

	public function tengan($arr, $query) {
		$palabras = array();

		for ($i = 0; $i < count($arr); $i++) {
			if(($query == '') || 
				 (strpos($this->removeAccents($arr[$i]), $this->removeAccents($query)) !== false)) {
				array_push($palabras, $arr[$i]);
			}
		}

		return $palabras;
	}

	public function notengan($arr, $query) {
		$palabras = array();
		
		for ($i = 0; $i < count($arr); $i++) {
			if(($query == '') || 
				 (strpos($this->removeAccents($arr[$i]), $this->removeAccents($query)) == false)) {
				array_push($palabras, $arr[$i]);
			}
		}

		return $palabras;
	}
	
	public function cadena($arr, $query) {
		$palabras = array();

		for ($i = 0; $i < count($arr); $i++) {
			if(($query == '') || 
				 (strpos($this->removeAccents($arr[$i]), $this->removeAccents($query)) !== false)) {
				array_push($palabras, $arr[$i]);
			}
		}

		return $palabras;
	}

	public function nocadena($arr, $query) {
		$palabras = array();
		
		for ($i = 0; $i < count($arr); $i++) {
			if(($query == '') || 
				 (strpos($this->removeAccents($arr[$i]), $this->removeAccents($query)) == false)) {
				array_push($palabras, $arr[$i]);
			}
		}

		return $palabras;
	}

	public function silabas($arr, $query) {
		$palabras = array();
		$BPS = new Buscador_Palabras_Silaba();

		for ($i = 0; $i < count($arr); $i++) {
			$silaba = $BPS->getSilabas($arr[$i]);

			if (($query == 0) || 
					($query == $silaba['numeroSilaba']) || 
					($query >= 10 && $silaba['numeroSilaba'] >= 10)) {
				array_push($palabras, $arr[$i]);
			}
		}

		return $palabras;
	}
	
	public function letras($arr, $query) {
		$palabras = array();

		for ($i = 0; $i < count($arr); $i++) {
			if (($query == 0) || 
					($query == mb_strlen($arr[$i])) || 
					($query == 100 && (mb_strlen($arr[$i]) >= 10 && mb_strlen($arr[$i]) <= 14)) || 
					($query == 150 && mb_strlen($arr[$i]) >= 15)) {
						array_push($palabras, $arr[$i]);
					}
		}

		return $palabras;
	}

	public function antiacentos($arr, $query) {
		$palabras = array();

		for ($i = 0; $i < count($arr); $i++) {
			if (($query == 0) || 
					($query == 1 && $this->hasAccents($arr[$i])) || 
					($query == 2 && !$this->hasAccents($arr[$i]))) {
						array_push($palabras, $arr[$i]);
					}
		}

		return $palabras;
	}

	public function removeAccents($string) {
    return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'))), ' '));
	}

	public function hasAccents($string) {
		$characterMap = array("Á", "á", "É", "é", "Í", "í", "Ó", "ó", "Ú", "ú", "Ñ", "ñ", "Ü", "ü");

		foreach($characterMap as $a) {
        if (stripos($string, $a) !== false) return true;
    }
    return false;
	}
}
