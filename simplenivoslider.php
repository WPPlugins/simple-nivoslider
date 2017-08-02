<?php
/*
Plugin Name: Simple NivoSlider
Plugin URI: https://wordpress.org/plugins/simple-nivoslider/
Version: 5.01
Description: Integrates NivoSlider into WordPress.
Author: Katsushi Kawamori
Author URI: http://riverforest-wp.info/
Text Domain: simple-nivoslider
Domain Path: /languages
*/

/*  Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	load_plugin_textdomain('simple-nivoslider');
//	load_plugin_textdomain('simple-nivoslider', false, basename( dirname( __FILE__ ) ) . '/languages' );

	define("SIMPLENIVOSLIDER_PLUGIN_BASE_FILE", plugin_basename(__FILE__));
	define("SIMPLENIVOSLIDER_PLUGIN_BASE_DIR", dirname(__FILE__));
	define("SIMPLENIVOSLIDER_PLUGIN_URL", plugins_url($path='',$scheme=null).'/simple-nivoslider');

	require_once( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR . '/req/SimpleNivoSliderRegist.php' );
	$simplenivosliderregistandheader = new SimpleNivoSliderRegist();
	add_action('admin_init', array($simplenivosliderregistandheader, 'register_settings'));
	unset($simplenivosliderregistandheader);

	require_once( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR . '/req/SimpleNivoSliderAdmin.php' );
	$simplenivoslideradmin = new SimpleNivoSliderAdmin();
	add_action( 'admin_menu', array($simplenivoslideradmin, 'plugin_menu'));
	add_action( 'admin_enqueue_scripts', array($simplenivoslideradmin, 'load_custom_wp_admin_style') );
	add_filter( 'plugin_action_links', array($simplenivoslideradmin, 'settings_link'), 10, 2 );
	add_action( 'admin_print_footer_scripts', array($simplenivoslideradmin, 'simplenivoslider_add_quicktags'));
	unset($simplenivoslideradmin);

	include_once( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR.'/inc/SimpleNivoSlider.php' );
	$simplenivoslider = new SimpleNivoSlider();
	$simplenivoslider->simplenivoslider_count = 0;
	$simplenivoslider->simplenivoslider_atts = array();
	add_filter( 'wp_get_attachment_image_attributes', array($simplenivoslider, 'add_title_to_attachment_image'), 12, 2 );
	add_shortcode( 'simplenivoslider', array($simplenivoslider, 'simplenivoslider_func') );
	add_action( 'wp_enqueue_scripts', array($simplenivoslider, 'load_frontend_scripts' ) );
	add_action( 'wp_footer', array($simplenivoslider, 'load_localize_scripts_styles') );
	unset($simplenivoslider);

?>