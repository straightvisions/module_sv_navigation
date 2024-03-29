<?php
	namespace sv100;
	
	class sv_navigation_walker extends \Walker_Nav_Menu {
		protected $init;
		protected $show_images;
		protected $child_images;
	
	
		public function __construct( $show_images ) {
			$this->init		 = new init();
			$this->show_images  = $show_images;
		}
	
		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent		 = str_repeat( "\t", $depth );
			$output		 .= "\n$indent<ul class=\"sub-menu depth_$depth\">\n";
		}
	
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent		 = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			$li_attributes  = '';
			$classes		= empty( $item->classes ) ? array() : (array) $item->classes;
	
			$classes[]	  = ( $args->walker->has_children ) ? 'dropdown' : '';
			$classes[]	  = ( $item->current || $item->current_item_ancestor ) ? 'active' : '';
			$classes[]	  = 'menu-item-' . $item->ID;
	
			$class_names	=  join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names	= ' class="' . esc_attr( $class_names ) . '"';
	
			$id			 = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
			$id			 = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
	
			$output		 .= $indent . '<li' . $id . $class_names . $li_attributes . '>';
	
			$attributes	 = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes	 .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '" rel="noopener"' : '';
			$attributes	 .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes	 .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
	
			$attributes	 .= ( $args->walker->has_children ) ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';
	
			$item_output	= $args->before;
			$item_output	.= '<a' . $attributes . '>';
	
			// Checks if this menu shows thumbnails and tells the children to show their thumbnails
			// @todo: make this an opt in feature
			if ( $depth < 1 && $this->show_images && $args->walker->has_children  ) {
				$this->child_images = true;
			} else if ( $depth < 1 && $this->show_images && $args->walker->has_children  ) {
				$this->child_images = false;
			}
			
			if ( $this->show_images && $this->child_images && !$args->walker->has_children && $depth > 0 ) {
				$item_output	.= '<div class="item-thumbnail">';
				$item_output	.= '<img src="'.esc_url(wp_get_attachment_image_src(get_post_thumbnail_id($item->object_id), 'sv100_nav_thumbnail', false)[0]).'" alt="'.esc_attr( $item->title ).'" />';
				$item_output	.= '</div>';
			}
	
			$item_output	.= '<div class="item-title">';
			$item_output	.= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output	.= '</div></a>';
			$item_output	.= $args->after;
	
			$output .= apply_filters ( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	
		}
	
		/*
			function end_el(){ // closing li a span
	
			}
	
			function end_lvl(){ // closing ul
	
			}
		*/
	}