<?php
if ( has_nav_menu( $settings['location'] ) ) {
	wp_nav_menu( array(
		'theme_location'	=> $settings['location'],
		'depth'				=> $settings['depth'],
		'container_class'	=> $this->get_prefix(),
	));
}