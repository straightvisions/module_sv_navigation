<?php
namespace sv_100;

/**
 * @version         3.000
 * @author			straightvisions
 * @package			sv_100
 * @copyright		2019 straightvisions GmbH
 * @link			https://straightvisions.com
 * @since			3.000
 * @license			See license.txt or https://straightvisions.com
 */

class sv_navigation extends init {
	protected static $navs                      = array();
	protected static $custom_scripts            = array();

	// Properties
	protected $location                         = false;
	protected $description                      = false;

	public function __construct() {

	}

	public function init(){
		// Translates the module
		load_theme_textdomain( $this->get_module_name(), $this->get_path( 'languages' ) );

		// Module Info
		$this->set_module_title( 'SV Navigation' );
		$this->set_module_desc( __( 'This module gives the ability to manage and display navigations via the "[sv_navigation]" shortcode.', $this->get_module_name() ) );

		// Action Hooks
		add_action( 'after_setup_theme', array( $this, 'register_navs' ) );

		// Shortcodes
		add_shortcode( $this->get_module_name(), array( $this, 'shortcode' ) );

		$this->load_modules();
	}

	protected function load_modules() :sv_navigation {
		require_once( $this->get_path( 'lib/modules/walker.php' ) );

		return $this;
	}

	public function shortcode( $settings, $content = '' ) {
		$settings								= shortcode_atts(
			array(
				'location'						=> false,
				'depth'                         => 3,
				'show_images'                   => false,
			),
			$settings,
			$this->get_module_name()
		);

		$settings['location']                   = $this->get_prefix( $settings['location'] );

		ob_start();
		include( $this->get_path( 'lib/frontend/tpl/default.php' ) );
		$output									= ob_get_contents();
		ob_end_clean();

		return $output;
	}

	// Registers all created navigations
	public function register_navs() {
		register_nav_menus( static::$navs );
	}

	// Object Methods
	public function create( $parent ) :sv_navigation {
		$new                                    = new static();

		$new->set_root( $parent->get_root() );
		$new->set_parent( $parent );

		$new->location                          = $this->get_prefix( $parent->get_module_name() );

		return $new;
	}

	public function load_nav() :sv_navigation {
		static::$navs[ $this->get_location() ]  = $this->get_desc() ? $this->get_desc() : $this->get_parent()->get_module_name();

		return $this->get_root()->sv_navigation;
	}

	// Setter & Getter
	public function set_location( string $location ) :sv_navigation {
		$this->location                         = $this->get_location() . '_' . $location;

		return $this;
	}

	public function get_location() :string {
		return $this->location;
	}

	public function set_desc( string $description ) :sv_navigation {
		$this->description                      = $description;

		return $this;
	}

	public function get_desc() :string {
		return $this->description;
	}
}