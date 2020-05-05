<?php
if ( has_nav_menu( $settings['location'] ) ) {
	$nav_menu = wp_nav_menu( array(
		'theme_location'	=> $settings['location'],
		'depth'				=> $settings['depth'],
		'container_class'	=> $settings['location'],
		'echo'				=> false,
		'walker'            => new sv100\sv_navigation_walker( $settings['show_images'] ),
	));
	
	if ( $nav_menu ) {
	    echo '<div class="'.$this->get_prefix('container').'">';
            echo $nav_menu;
            echo '<button type="button" class="' . $this->get_prefix( 'mobile_menu_toggle' )
                 . ' ' . $settings['location']
                 . '_mobile_menu_toggle"></button>';
		echo '</div>';
	}
}