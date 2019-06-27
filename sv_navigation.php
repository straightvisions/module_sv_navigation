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
	protected static $menus						= array();
	
	// Properties
	protected $nav								= array();
	protected $menu								= array();
	protected $old								= array();

	public function init() {
		// Module Info
		$this->set_module_title( 'SV Navigation' );
		$this->set_module_desc( __( 'This module gives the ability to manage and display navigations via the "[sv_navigation]" shortcode.', 'straightvisions_100' ) );

		// Action Hooks
		add_action( 'after_setup_theme', array( $this, 'register_navs' ) );
		add_action( 'after_switch_theme', array( $this, 'nav_location_rescue' ) );

		$this->add_theme_support()->load_modules();
	}
	
	protected function add_theme_support(): sv_navigation {
		add_image_size( 'sv_100_nav_thumbnail', 250, 130 );
		
		return $this;
	}

	protected function load_modules(): sv_navigation {
		require_once( $this->get_path( 'lib/modules/walker.php' ) );

		return $this;
	}
	public function has_items($location): bool{
		$location = $this->get_prefix( $location );

		return ((count($this->get_nav_menu_items_by_location($location)) > 0) ? true : false);
	}
	protected function get_nav_menu_items_by_location( $location, $args = [] ):array {
		
		// Get all locations
		$locations = get_nav_menu_locations();
		
		// Get object id by location
		if(!isset($locations[$location])){
			return array();
		}
		$object = wp_get_nav_menu_object( $locations[$location] );
		
		// Get menu items by menu name
		$menu_items = wp_get_nav_menu_items( $object->name, $args );
		
		// Return menu post objects
		return $menu_items ? $menu_items : array();
	}

	public function load( $settings = array() ): string {
		$settings								= shortcode_atts(
			array(
				'location'						=> false,
				'depth'                         => 3,
				'show_images'                   => false
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
	
	// Tries to find menus in old locations and inserts them in new locations
	public function nav_location_rescue() {
		$old_theme 		= get_option( 'theme_switched' );
		$old_theme_mods = get_option( 'theme_mods_' . $old_theme );
		$old_theme_navs = isset( $old_theme_mods['nav_menu_locations'] ) ? $old_theme_mods['nav_menu_locations'] : false;
		$new_theme_navs = ! empty( get_theme_mod( 'nav_menu_locations' ) ) ? get_theme_mod( 'nav_menu_locations' ) : false;
		
		if ( ! $new_theme_navs && ! $old_theme_navs ) {
			$this->create_menus();
		} else if ( ! $new_theme_navs && $old_theme_navs ) {
			$new_theme_locations = get_registered_nav_menus();
			
			foreach ( $new_theme_locations as $location => $description ) {
				$new_theme_navs[ $location ] = $old_theme_navs[ $location ];
			}
			
			set_theme_mod( 'nav_menu_locations', $new_theme_navs );
		}
	}

	// Nav Methods
	public function create( $parent ): sv_navigation {
		$new                                    = new static();

		$new->set_root( $parent->get_root() );
		$new->set_parent( $parent );

		$new->nav['location']                  	= $this->get_prefix( $parent->get_module_name() );

		return $new;
	}

	public function load_nav(): sv_navigation {
		static::$navs[ $this->get_location() ]  = $this->get_desc() ? $this->get_desc() : $this->get_parent()->get_module_name();

		return $this->get_module('sv_navigation');
	}
	
	// Registers all created navigations
	public function register_navs() {
		register_nav_menus( static::$navs );
	}

	// Nav - Setter & Getter
	public function set_location( string $location ): sv_navigation {
		$this->nav['location']                 = $this->get_location() . '_' . $location;

		return $this;
	}

	protected function get_location(): string {
		return $this->nav['location'];
	}

	public function set_desc( string $description ): sv_navigation {
		$this->nav['description']              = $description;

		return $this;
	}

	protected function get_desc() :string {
		return $this->nav['description'];
	}
	
	// Menu Methods
	public function create_menu( $parent ): sv_navigation {
		$new                                    = new static();
		
		$new->set_root( $parent->get_root() );
		$new->set_parent( $parent );
		
		$new->menu['name']                 		= $this->get_prefix( $parent->get_module_name() );
		$new->menu['location']					= $this->get_prefix( $parent->get_module_name() );
		
		return $new;
	}
	
	public function load_menu(): sv_navigation {
		if ( ! wp_get_nav_menu_object( $this->get_menu_name()  ) ) {
			static::$menus[ $this->get_menu_name() ] = $this->menu;
		}
		
		return $this->get_root()->sv_navigation;
	}
	
	protected function create_menus(): sv_navigation {
		foreach ( static::$menus as $name => $data ) {
			$data['id'] = wp_create_nav_menu( $name );

			if ( ! empty( $data['items'] ) ) {
				foreach ( $data['items'] as $item ) {
					wp_update_nav_menu_item( $data['id'], 0, $item );
				}
			}
			
			set_theme_mod( 'nav_menu_locations', array( $data['location'] => $data['id'] ) );
		}
		
		return $this;
	}
	
	// Menu - Setter & Getter
	public function set_menu_name( string $name ): sv_navigation {
		$this->menu['name'] = $name;
		
		return $this;
	}
	
	protected function get_menu_name(): string {
		return $this->menu['name'];
	}
	
	public function set_menu_item( array $data ): sv_navigation {
		$this->menu['items'][] = $data;
		
		return $this;
	}
	
	protected function get_items(): array {
		return $this->menu['items'];
	}
	
	public function set_menu_location( string $location ): sv_navigation {
		$this->menu['location'] = $this->menu['location'] . '_' . $location;
		
		return $this;
	}
	
	protected function get_menu_location(): string {
		return $this->menu['location'];
	}
}