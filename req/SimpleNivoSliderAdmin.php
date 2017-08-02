<?php
/**
 * Simple NivoSlider
 * 
 * @package    Simple NivoSlider
 * @subpackage SimpleNivoSliderAdmin Management screen
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

class SimpleNivoSliderAdmin {

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0
	 */
	function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = SIMPLENIVOSLIDER_PLUGIN_BASE_FILE;
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('options-general.php?page=simplenivoslider').'">'.__( 'Settings').'</a>';
		}
			return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function plugin_menu() {
		add_options_page( 'Simple NivoSlider Options', 'Simple NivoSlider', 'manage_options', 'simplenivoslider', array($this, 'plugin_options') );
	}

	/* ==================================================
	 * Add Css and Script
	 * @since	2.0
	 */
	function load_custom_wp_admin_style() {
		if ($this->is_my_plugin_screen()) {
			wp_enqueue_style( 'jquery-responsiveTabs', SIMPLENIVOSLIDER_PLUGIN_URL.'/css/responsive-tabs.css' );
			wp_enqueue_style( 'jquery-responsiveTabs-style', SIMPLENIVOSLIDER_PLUGIN_URL.'/css/style.css' );
			wp_enqueue_style( 'simple-nivoslider', SIMPLENIVOSLIDER_PLUGIN_URL.'/css/simple-nivoslider.css' );
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'jquery-responsiveTabs', SIMPLENIVOSLIDER_PLUGIN_URL.'/js/jquery.responsiveTabs.min.js' );
			wp_enqueue_script( 'simplenivoslider-admin-js', SIMPLENIVOSLIDER_PLUGIN_URL.'/js/jquery.simplenivoslider.admin.js', array('jquery') );
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	3.3
	 */
	function is_my_plugin_screen() {
		$screen = get_current_screen();
		if (is_object($screen) && $screen->id == 'settings_page_simplenivoslider') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function plugin_options() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if( !empty($_POST) ) { 
			$post_nonce_field = 'simplenivoslider_set';
			if ( isset($_POST[$post_nonce_field]) && $_POST[$post_nonce_field] ) {
				if ( check_admin_referer( 'snsl_set', $post_nonce_field ) ) {
					$this->options_updated();
				}
			}
		}
		$scriptname = admin_url('options-general.php?page=simplenivoslider');
		$simplenivoslider_settings = get_option('simplenivoslider_settings');

		?>

		<div class="wrap">
		<h2>Simple NivoSlider</h2>

	<div id="simplenivoslider-admin-tabs">
	  <ul>
		<li><a href="#simplenivoslider-admin-tabs-1">NivoSlider&nbsp<?php _e('Settings'); ?></a></li>
	    <li><a href="#simplenivoslider-admin-tabs-2"><?php _e('Settings'); ?></a></li>
		<li><a href="#simplenivoslider-admin-tabs-3"><?php _e('Donate to this plugin &#187;'); ?></a></li>
	<!--
		<li><a href="#simplenivoslider-admin-tabs-4">FAQ</a></li>
	 -->
	  </ul>

	  <div id="simplenivoslider-admin-tabs-1">
		<div class="wrap">
			<h2>NivoSlider <?php _e('Settings'); ?>(<a href="https://github.com/Codeinwp/Nivo-Slider-jQuery" target="_blank" style="text-decoration: none; word-break: break-all;"><?php _e('Website'); ?></a>)</h2>	
			<h3><?php _e('Shortcode attributes take precedence. If the attribute is omitted, the following settings are applied.', 'simple-nivoslider'); ?></h3>

			<form method="post" action="<?php echo $scriptname; ?>">
			<?php wp_nonce_field('snsl_set', 'simplenivoslider_set'); ?>

			<p class="submit">
			<?php submit_button( __('Save Changes'), 'large', 'Submit', FALSE ); ?>
			<?php submit_button( __('Default'), 'large', 'Default', FALSE ); ?>
			</p>

			<div id="container-simplenivoslider-settings">

				<?php $shortocode_attr_html = '<a href="'.__('https://codex.wordpress.org/Shortcode_API#Handling_Attributes', 'simple-nivoslider').'" target="_blank" style="text-decoration: none; word-break: break-all;">'.__('specification', 'simple-nivoslider').'</a>'; ?>

				<h4><span style="color: green;"><?php echo sprintf(__('Shortcode attribute is green. It will be lowercase. It is the %1$s of WordPress.', 'simple-nivoslider'), $shortocode_attr_html); ?></span></h4>

				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>theme&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">theme</span></div>
					<div><?php _e('Default') ?>&nbsp(default)</div>
					<div>
					<?php $target_settings_theme = $simplenivoslider_settings['theme']; ?>
					<select id="simplenivoslider_settings_theme" name="simplenivoslider_settings_theme">
						<option <?php if ('default' == $target_settings_theme)echo 'selected="selected"'; ?>>default</option>
						<option <?php if ('dark' == $target_settings_theme)echo 'selected="selected"'; ?>>dark</option>
						<option <?php if ('light' == $target_settings_theme)echo 'selected="selected"'; ?>>light</option>
						<option <?php if ('bar' == $target_settings_theme)echo 'selected="selected"'; ?>>bar</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Using themes', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>effect&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">effect</span></div>
					<div><?php _e('Default') ?>&nbsp(random)</div>
					<div>
					<?php $target_settings_effect = $simplenivoslider_settings['effect']; ?>
					<select id="simplenivoslider_settings_effect" name="simplenivoslider_settings_effect">
						<option <?php if ('sliceDown' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceDown</option>
						<option <?php if ('sliceDownLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceDownLeft</option>
						<option <?php if ('sliceUp' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUp</option>
						<option <?php if ('sliceUpLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUpLeft</option>
						<option <?php if ('sliceUpDown' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUpDown</option>
						<option <?php if ('sliceUpDownLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUpDownLeft</option>
						<option <?php if ('fold' == $target_settings_effect)echo 'selected="selected"'; ?>>fold</option>
						<option <?php if ('fade' == $target_settings_effect)echo 'selected="selected"'; ?>>fade</option>
						<option <?php if ('random' == $target_settings_effect)echo 'selected="selected"'; ?>>random</option>
						<option <?php if ('slideInRight' == $target_settings_effect)echo 'selected="selected"'; ?>>slideInRight</option>
						<option <?php if ('slideInLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>slideInLeft</option>
						<option <?php if ('boxRandom' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRandom</option>
						<option <?php if ('boxRain' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRain</option>
						<option <?php if ('boxRainReverse' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRainReverse</option>
						<option <?php if ('boxRainGrow' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRainGrow</option>
						<option <?php if ('boxRainGrowReverse' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRainGrowReverse</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Specify sets like', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>slices&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">slices</span></div>
					<div><?php _e('Default') ?>&nbsp(15)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_slices" name="simplenivoslider_settings_slices" value="<?php echo $simplenivoslider_settings['slices'] ?>" style="width: 80px" />
					</div>
					<div style="padding: 0px 10px"><li><?php _e('For slice animations', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>boxCols&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">boxcols</span></div>
					<div><?php _e('Default') ?>&nbsp(8)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_boxCols" name="simplenivoslider_settings_boxCols" value="<?php echo $simplenivoslider_settings['boxCols'] ?>" style="width: 80px" />
					</div>
					<div style="padding: 0px 10px"><li><?php _e('For box animations cols', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>boxRows&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">boxrows</span></div>
					<div><?php _e('Default') ?>&nbsp(4)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_boxRows" name="simplenivoslider_settings_boxRows" value="<?php echo $simplenivoslider_settings['boxRows'] ?>" style="width: 80px" />
					</div>
					<div style="padding: 0px 10px"><li><?php _e('For box animations rows', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>animSpeed&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">animspeed</span></div>
					<div><?php _e('Default') ?>&nbsp(500)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_animSpeed" name="simplenivoslider_settings_animSpeed" value="<?php echo $simplenivoslider_settings['animSpeed'] ?>" style="width: 80px" />msec
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Slide transition speed', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>pauseTime&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">pausetime</span></div>
					<div><?php _e('Default') ?>&nbsp(3000)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_pauseTime" name="simplenivoslider_settings_pauseTime" value="<?php echo $simplenivoslider_settings['pauseTime'] ?>" style="width: 80px" />msec
					</div>
					<div style="padding: 0px 10px"><li><?php _e('How long each slide will show', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>startSlide&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">startslide</span></div>
					<div><?php _e('Default') ?>&nbsp(0)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_startSlide" name="simplenivoslider_settings_startSlide" value="<?php echo $simplenivoslider_settings['startSlide'] ?>" style="width: 80px" />
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Set starting Slide (0 index)', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>directionNav&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">directionnav</span></div>
					<div><?php _e('Default') ?>&nbsp(true)</div>
					<div>
					<?php $target_settings_directionNav = $simplenivoslider_settings['directionNav']; ?>
					<select id="simplenivoslider_settings_directionNav" name="simplenivoslider_settings_directionNav">
						<option <?php if ('true' == $target_settings_directionNav)echo 'selected="selected"'; ?>>true</option>
						<option value="" <?php if (!$target_settings_directionNav)echo 'selected="selected"'; ?>>false</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Next & Prev navigation', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>controlNav&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">controlnav</span></div>
					<div><?php _e('Default') ?>&nbsp(true)</div>
					<div>
					<?php $target_settings_controlNav = $simplenivoslider_settings['controlNav']; ?>
					<select id="simplenivoslider_settings_controlNav" name="simplenivoslider_settings_controlNav">
						<option <?php if ('true' == $target_settings_controlNav)echo 'selected="selected"'; ?>>true</option>
						<option value="" <?php if (!$target_settings_controlNav)echo 'selected="selected"'; ?>>false</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('1,2,3... navigation', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>controlNavThumbs&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">controlnavthumbs</span></div>
					<div><?php _e('Default') ?>&nbsp(false)</div>
					<div>
					<?php $target_settings_controlNavThumbs = $simplenivoslider_settings['controlNavThumbs']; ?>
					<select id="simplenivoslider_settings_controlNavThumbs" name="simplenivoslider_settings_controlNavThumbs">
						<option <?php if ('true' == $target_settings_controlNavThumbs)echo 'selected="selected"'; ?>>true</option>
						<option value="" <?php if (!$target_settings_controlNavThumbs)echo 'selected="selected"'; ?>>false</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Use thumbnails for Control Nav', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>thumbswidth&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">thumbswidth</span></div>
					<div><?php _e('Default') ?>&nbsp(40)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_thumbswidth" name="simplenivoslider_settings_thumbswidth" value="<?php echo $simplenivoslider_settings['thumbswidth'] ?>" style="width: 80px" />px
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Width of thumbnails', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>pauseOnHover&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">pauseonhover</span></div>
					<div><?php _e('Default') ?>&nbsp(true)</div>
					<div>
					<?php $target_settings_pauseOnHover = $simplenivoslider_settings['pauseOnHover']; ?>
					<select id="simplenivoslider_settings_pauseOnHover" name="simplenivoslider_settings_pauseOnHover">
						<option <?php if ('true' == $target_settings_pauseOnHover)echo 'selected="selected"'; ?>>true</option>
						<option value="" <?php if (!$target_settings_pauseOnHover)echo 'selected="selected"'; ?>>false</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Stop animation while hovering', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>manualAdvance&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">manualadvance</span></div>
					<div><?php _e('Default') ?>&nbsp(false)</div>
					<div>
					<?php $target_settings_manualAdvance = $simplenivoslider_settings['manualAdvance']; ?>
					<select id="simplenivoslider_settings_manualAdvance" name="simplenivoslider_settings_manualAdvance">
						<option <?php if ('true' == $target_settings_manualAdvance)echo 'selected="selected"'; ?>>true</option>
						<option value="" <?php if (!$target_settings_manualAdvance)echo 'selected="selected"'; ?>>false</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Force manual transitions', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>prevText&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">prevtext</span></div>
					<div><?php _e('Default') ?>&nbsp(Prev)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_prevText" name="simplenivoslider_settings_prevText" value="<?php echo $simplenivoslider_settings['prevText'] ?>" />
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Prev directionNav text', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>nextText&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">nexttext</span></div>
					<div><?php _e('Default') ?>&nbsp(Next)</div>
					<div>
						<input type="text" id="simplenivoslider_settings_nextText" name="simplenivoslider_settings_nextText" value="<?php echo $simplenivoslider_settings['nextText'] ?>" />
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Next directionNav text', 'simple-nivoslider'); ?></li></div>
				</div>
				<div class="item-simplenivoslider-settings" style="border:#CCC 2px solid;">
					<div>randomStart&nbsp&nbsp&nbsp&nbsp&nbsp<span style="color: green;">randomstart</span></div>
					<div><?php _e('Default') ?>&nbsp(false)</div>
					<div>
					<?php $target_settings_randomStart = $simplenivoslider_settings['randomStart']; ?>
					<select id="simplenivoslider_settings_randomStart" name="simplenivoslider_settings_randomStart">
						<option <?php if ('true' == $target_settings_randomStart)echo 'selected="selected"'; ?>>true</option>
						<option value="" <?php if (!$target_settings_randomStart)echo 'selected="selected"'; ?>>false</option>
					</select>
					</div>
					<div style="padding: 0px 10px"><li><?php _e('Start on a random slide', 'simple-nivoslider'); ?></li></div>
				</div>

			</div>
			<div style="clear:both"></div>

			<?php submit_button( __('Save Changes'), 'large', 'Submit', TRUE ); ?>

			</form>

		</div>
	  </div>

	  <div id="simplenivoslider-admin-tabs-2">
		<div class="wrap">
			<?php
			$screenshot_html = '<a href="'.__('https://wordpress.org/plugins/simple-nivoslider/screenshots/', 'simple-nivoslider').'" target="_blank" style="text-decoration: none; word-break: break-all;">'.__('Screenshots', 'simple-nivoslider').'</a>';
			?>
			<h2><?php _e('Settings'); ?>(<?php echo $screenshot_html; ?>)</h2>

			<li style="margin: 0px 40px;">
			<h3><?php _e('Write a Shortcode. The following text field. Enclose image tags and gallery shortcode.', 'simple-nivoslider'); ?></h3>
			<h3><?php _e('example:'); ?></h3>
			<h3><code>&#91simplenivoslider&#93&lt;a href="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f8e6a6a7.jpg"&gt;&lt;img src="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f8e6a6a7.jpg" alt="" width="1000" height="626" class="alignnone size-full wp-image-275" /&gt;&lt;/a&gt;
&lt;a href="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f878ff71.jpg"&gt;&lt;img src="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f878ff71.jpg" alt="" width="1000" height="666" class="alignnone size-full wp-image-274" /&gt;&lt;/a&gt;&#91gallery size="full" ids="273,272,271,270"&#93&#91/simplenivoslider&#93</code></h3>
			</li>
			<li style="margin: 0px 40px;">
			<h3><?php _e('Write a Shortcode. The following template. Enclose image tags and gallery shortcode.', 'simple-nivoslider'); ?></h3>
			<h3><?php _e('example:'); ?></h3>
			<h3><code>&lt;?php echo do_shortcode('&#91simplenivoslider controlnav="false"&#93&#91gallery link="none" size="full" ids="271,270,269,268"&#93&#91/simplenivoslider&#93'); ?&gt;</code></h3>
			</h3>
			</li>
			<li style="margin: 0px 40px;">
			<h3><?php _e('"Simple NivoSlider" activation, you to include additional buttons for Shortcode in the Text (HTML) mode of the WordPress editor.', 'simple-nivoslider'); ?>
			</h3>
			</li>
			<li style="margin: 0px 40px;">
			<h3><?php _e('Within the Shortcode, it is possible to describe multiple galleries and multiple media.', 'simple-nivoslider'); ?>
			</h3>
			</li>

		</div>
	  </div>

	  <div id="simplenivoslider-admin-tabs-3">
		<div class="wrap">
			<?php
			$plugin_datas = get_file_data( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR.'/simplenivoslider.php', array('version' => 'Version') );
			$plugin_version = __('Version:').' '.$plugin_datas['version'];
			?>
			<h4 style="margin: 5px; padding: 5px;">
			<?php echo $plugin_version; ?> |
			<a style="text-decoration: none;" href="https://wordpress.org/support/plugin/simple-nivoslider" target="_blank"><?php _e('Support Forums') ?></a> |
			<a style="text-decoration: none;" href="https://wordpress.org/support/view/plugin-reviews/simple-nivoslider" target="_blank"><?php _e('Reviews', 'simple-nivoslider') ?></a>
			</h4>
			<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
			<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', 'simple-nivoslider'); ?></h3>
			<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
	<a style="margin: 5px; padding: 5px;" href='https://pledgie.com/campaigns/28307' target="_blank"><img alt='Click here to lend your support to: Various Plugins for WordPress and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/28307.png?skin_name=chrome' border='0' ></a>
			</div>
		</div>
	  </div>

	<!--
	  <div id="simplenivoslider-admin-tabs-4">
		<div class="wrap">
		<h2>FAQ</h2>

		</div>
	  </div>
	-->

	</div>

		</div>
		<?php
	}

	/* ==================================================
	 * Update wp_options table.
	 * @param	none
	 * @since	1.0
	 */
	function options_updated(){

		if ( !empty($_POST['Default']) ) {
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
		} else {
			$settings_tbl = array(
							'theme' => $_POST['simplenivoslider_settings_theme'],
							'effect' => $_POST['simplenivoslider_settings_effect'],
							'slices' => intval($_POST['simplenivoslider_settings_slices']),
							'boxCols' => intval($_POST['simplenivoslider_settings_boxCols']),
							'boxRows' => intval($_POST['simplenivoslider_settings_boxRows']),
							'animSpeed' => intval($_POST['simplenivoslider_settings_animSpeed']),
							'pauseTime' => intval($_POST['simplenivoslider_settings_pauseTime']),
							'startSlide' => intval($_POST['simplenivoslider_settings_startSlide']),
							'directionNav' => $_POST['simplenivoslider_settings_directionNav'],
							'controlNav' => $_POST['simplenivoslider_settings_controlNav'],
							'controlNavThumbs' => $_POST['simplenivoslider_settings_controlNavThumbs'],
							'thumbswidth' => $_POST['simplenivoslider_settings_thumbswidth'],
							'pauseOnHover' => $_POST['simplenivoslider_settings_pauseOnHover'],
							'manualAdvance' => $_POST['simplenivoslider_settings_manualAdvance'],
							'prevText' => $_POST['simplenivoslider_settings_prevText'],
							'nextText' => $_POST['simplenivoslider_settings_nextText'],
							'randomStart' => $_POST['simplenivoslider_settings_randomStart']
							);
		}
		update_option( 'simplenivoslider_settings', $settings_tbl );
		echo '<div class="notice notice-success is-dismissible"><ul><li>'.'NivoSlider '.__('Settings').' --> '.__('Settings saved.').'</li></ul></div>';

	}

	/* ==================================================
	 * Add Quick Tag
	 * @since	4.00
	 */
	function simplenivoslider_add_quicktags() {
		if (wp_script_is('quicktags')){
	?>
		<script type="text/javascript">
			QTags.addButton( 'simplenivoslider', 'simplenivoslider', '[simplenivoslider]', '[/simplenivoslider]' );
		</script>
	<?php
		}
	}

}

?>