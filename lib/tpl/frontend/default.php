<?php
	$nav_menu = wp_nav_menu( array(
		'theme_location'	=> $settings['location'],
		'depth'				=> $settings['depth'],
		'container_class'	=> $settings['location'],
		'echo'				=> false,
		'walker'			=> new sv100\sv_navigation_walker( $settings['show_images'] ),
		'fallback_cb'		=> '__return_false'
	));

	if ( $nav_menu ) {
		echo '<div class="'.$this->get_prefix('container').' '.$this->get_prefix('container').'_'.$settings['location'].'">';
		echo $nav_menu;
		echo '<button type="button" class="' . $this->get_prefix( 'mobile_menu_toggle' )
			. ' ' . $settings['location']
			. '_mobile_menu_toggle"></button>';
		echo '</div>';
	}