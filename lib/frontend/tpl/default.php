<?php
if ( has_nav_menu( $settings['location'] ) ) {
	wp_nav_menu( array(
		'theme_location'	=> $settings['location'],
		'depth'				=> $settings['depth'],
		'container_class'	=> $this->get_prefix(),
		'walker'            => new sv_100\sv_navigation_walker( $settings['show_images'] ),
	));

	echo '<button type="button" class="' . $this->get_prefix( 'mobile_menu_toggle' ) . '">';
	echo '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd">
			<path d="M24 18v1h-24v-1h24zm0-6v1h-24v-1h24zm0-6v1h-24v-1h24z" fill="#1040e2"/>
			<path d="M24 19h-24v-1h24v1zm0-6h-24v-1h24v1zm0-6h-24v-1h24v1z"/>
			</svg>';
	echo '</button>';
}