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
	protected static $custom_styles             = array();

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

		$this->register_scripts();
	}

	public function shortcode( $settings, $content = '' ) {
		$settings								= shortcode_atts(
			array(
				'inline'						=> true,
				'location'						=> false,
				'depth'                         => 3,
			),
			$settings,
			$this->get_module_name()
		);
		$settings['location']                   = $this->get_prefix( $settings['location'] );

		$this->load_scripts( $settings );

		ob_start();
		include( $this->get_path( 'lib/tpl/frontend.php' ) );
		$output									= ob_get_contents();
		ob_end_clean();

		return $output;
	}

	// Registers standard scripts
	protected function register_scripts() :sv_navigation {
		// Styles
		$this->scripts_queue['frontend']	    = static::$scripts
			->create( $this )
			->set_ID( 'frontend' )
			->set_path( 'lib/css/frontend.css' )
			->set_inline( true );

		return $this;
	}

	// Loads the scripts
	protected function load_scripts( array $settings ) :sv_navigation {
		if ( isset( static::$custom_styles[ $settings['location'] ] ) ) {
			static::$scripts->create( $this )
			                ->set_ID( $settings['location'] )
			                ->set_path( '../' . static::$custom_styles[ $settings['location'] ] )
			                ->set_inline( $settings['inline'] )
			                ->set_is_enqueued();
		} else {
			$this->scripts_queue['frontend']
				->set_inline( $settings['inline'] )
				->set_is_enqueued();
		}

		return $this;
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

	public function set_css( string $css_path, string $location = '' ) :sv_navigation {
		if ( !empty( $location ) ) {
			static::$custom_styles[ $this->get_prefix( $location ) ]    = $css_path;
		} else {
			static::$custom_styles[ $this->get_location() ]             = $css_path;
		}

		return $this;
	}

	public function get_css() :string {
		return static::$custom_styles[ $this->get_location() ];
	}
}