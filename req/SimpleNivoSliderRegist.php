<?php
/**
 * Simple NivoSlider
 * 
 * @package    Simple NivoSlider
 * @subpackage SimpleNivoSliderRegist registered in the database
    Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

class SimpleNivoSliderRegist {

	/* ==================================================
	 * Settings register
	 * @since	1.0
	 */
	function register_settings(){

		// version 3.43 -> 4.00
		if ( get_option('simplenivoslider_mgsettings') ) {
			delete_option( 'simplenivoslider_mgsettings' );
		}
		// version 4.05 -> 5.00
		global $wpdb;
		$apply_meta = 'simplenivoslider_apply';
		$delete_meta = $wpdb->get_results("
						SELECT	post_id, meta_value
						FROM	$wpdb->postmeta
						WHERE	meta_key
						LIKE	'%%$apply_meta%%'
						");
		if ( $delete_meta ) {
			foreach ($delete_meta as $value) {
				delete_post_meta( $value->post_id, $apply_meta );
			}
		}

		if ( !get_option('simplenivoslider_settings') ) {
			$settings_tbl = array(
								'theme' => 'default',
								'effect' => 'random',
								'slices' => 15,
								'boxCols' => 8,
								'boxRows' => 4,
								'animSpeed' => 500,
								'pauseTime' => 3000,
								'startSlide' => 0,
								'directionNav' => 'true',
								'controlNav' => 'true',
								'controlNavThumbs' => NULL,
								'thumbswidth' => 40,
								'pauseOnHover' => 'true',
								'manualAdvance' => NULL,
								'prevText' => 'Prev',
								'nextText' => 'Next',
								'randomStart' => NULL
							);
			update_option( 'simplenivoslider_settings', $settings_tbl );
		} else {
			$settings_tbl = get_option('simplenivoslider_settings');
			foreach ($settings_tbl as $key => $value) {
				if ( $value === 'false' ) {
					$settings_tbl[$key] = NULL;
				}
			}
			update_option( 'simplenivoslider_settings', $settings_tbl );
		}

	}

}

?>