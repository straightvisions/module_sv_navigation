<?php
if ( has_nav_menu( $settings['location'] ) ) {
	$container_class        = $settings['class'] ? $this->get_prefix() . ' ' . $settings['class'] : $this->get_prefix();

	wp_nav_menu( array(
		'theme_location'	=> $settings['location'],
		'depth'				=> $settings['depth'],
		'container_class'	=> $container_class,
		'walker'            => new sv_100\sv_navigation_walker( $settings['show_images'] ),
	));

	echo '<button type="button" class="' . $this->get_prefix( 'mobile_menu_toggle' ) . '"></button>';
}