<?php
if ( has_nav_menu( $settings['location'] ) ) {
	wp_nav_menu( array(
		'theme_location'	=> $settings['location'],
		'depth'				=> $settings['depth'],
		'container_class'	=> $this->get_prefix(),
		'walker'            => new sv_100\sv_navigation_walker( $settings['show_images'] ),
	));
}