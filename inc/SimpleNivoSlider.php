<?php
/**
 * Simple NivoSlider
 * 
 * @package    Simple NivoSlider
 * @subpackage SimpleNivoSlider Main Functions
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

class SimpleNivoSlider {

	public $simplenivoslider_count;
	public $simplenivoslider_atts;

	/* ==================================================
	* @param	string	$content
	* @return	string	$content
	* @since	1.0
	*/
	function add_img_tag($content, $simplenivoslider_id, $atts) {

		remove_shortcode('gallery', 'gallery_shortcode');
		add_shortcode('gallery', array($this, 'simplenivoslider_gallery_shortcode'));

		$gallery_code = NULL;
		$pattern_gallery = '/\[' . preg_quote('gallery ') . '[^\]]*\]/im';
		if ( !empty($content) && preg_match($pattern_gallery, $content) ) {
			preg_match_all($pattern_gallery, $content, $retgallery);
			foreach ( $retgallery as $ret=>$gals ) {
				foreach ( $gals as $gal ) {
					$gallery_code = do_shortcode($gal);
					$content = str_replace( $gal, $gallery_code, $content );
				}
			}
		}
		remove_shortcode('gallery', array($this, 'simplenivoslider_gallery_shortcode'));
		add_shortcode('gallery', 'gallery_shortcode');

		$allowed_html = array(
			'img' => array()
		);
		wp_kses($content, $allowed_html);

		if(preg_match_all("/<img(.+?)>/mis", $content, $result) !== false){

			global $wpdb;
			$mimepattern_count = 0;
			$postmimetype = NULL;
			$mimes = get_allowed_mime_types();
			foreach ( $mimes as $type => $mime ) {
				if (substr($mime, 0, 5) === 'image') {
					if ( $mimepattern_count == 0 ) {
						$postmimetype .= 'post_mime_type IN("'.$mime.'"';
					} else {
						$postmimetype .= ',"'.$mime.'"';
					}
					++ $mimepattern_count;
				}
			}
			$postmimetype .= ')';
			$attachments = $wpdb->get_results("
							SELECT	ID, guid, post_title
							FROM	$wpdb->posts
							WHERE	$postmimetype
							");

			if ( count($result[0]) > 0 ) {
				$content = implode( "\n", $result[0] );
		    	foreach ($result[1] as $value){
					preg_match('/src=\"(.[^\"]*)\"/',$value,$src);
					$explode = explode("/" , $src[1]);
					$file_name = $explode[count($explode) - 1];
					$title_name = preg_replace("/(.+)(\.[^.]+$)/", "$1", $file_name);
					$title_name = preg_replace('(-[0-9]*x[0-9]*)', '', $title_name);
					$image_thumb = NULL;
					foreach ( $attachments as $attachment ) {
						if( strpos($attachment->guid, $title_name) ){
							$title_name = $attachment->post_title;
							$image_thumb = wp_get_attachment_image_src( $attachment->ID, 'thumbnail', false );
							if( !strpos($value, 'title=') ) {
								$title_name = ' title="'.$title_name.'" ';
								$content = str_replace($value, $title_name.$value, $content);
							}
							if( !strpos($value, 'data-thumb=') && $atts['controlnavthumbs'] ) {
								$thumb_data = ' data-thumb="'.$image_thumb[0].'" ';
								$content = str_replace($value, $thumb_data.$value, $content);
							}
						}
					}
				}
			}
		}

		$content = '<div class="slider-wrapper theme-'.$atts['theme'].'"><div id="simplenivoslider-'.$simplenivoslider_id.'" class="nivoSlider">'."\n".$content."\n".'</div></div>'."\n";

		return $content;

	}

	/* ==================================================
	* @param	array	$attr
	* @param	array	$attachment
	* @return	array	$attr
	* @since	1.0
	*/
	function add_title_to_attachment_image( $attr, $attachment ) {

		$attr['title'] = esc_attr( $attachment->post_title );

		return $attr;

	}

	/**
	 * The Gallery shortcode.
	 *
	 * This implements the functionality of the Gallery Shortcode for displaying
	 * WordPress images on a post.
	 *
	 * @since 2.5.0
	 *
	 * @param array $attr {
	 *     Attributes of the gallery shortcode.
	 *
	 *     @type string $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
	 *     @type string $orderby    The field to use when ordering the images. Default 'menu_order ID'.
	 *                              Accepts any valid SQL ORDERBY statement.
	 *     @type int    $id         Post ID.
	 *     @type string $itemtag    HTML tag to use for each image in the gallery.
	 *                              Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
	 *     @type string $icontag    HTML tag to use for each image's icon.
	 *                              Default 'dt', or 'div' when the theme registers HTML5 gallery support.
	 *     @type string $captiontag HTML tag to use for each image's caption.
	 *                              Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
	 *     @type int    $columns    Number of columns of images to display. Default 3.
	 *     @type string $size       Size of the images to display. Default 'thumbnail'.
	 *     @type string $ids        A comma-separated list of IDs of attachments to display. Default empty.
	 *     @type string $include    A comma-separated list of IDs of attachments to include. Default empty.
	 *     @type string $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
	 *     @type string $link       What to link each image to. Default empty (links to the attachment page).
	 *                              Accepts 'file', 'none'.
	 * }
	 * @return string HTML content to display gallery.
	 */
	function simplenivoslider_gallery_shortcode( $attr ) {

		$post = get_post();

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) )
				$attr['orderby'] = 'post__in';
			$attr['include'] = $attr['ids'];
		}

		/**
		 * Filter the default gallery shortcode output.
		 *
		 * If the filtered output isn't empty, it will be used instead of generating
		 * the default gallery template.
		 *
		 * @since 2.5.0
		 *
		 * @see gallery_shortcode()
		 *
		 * @param string $output The gallery output. Default empty.
		 * @param array  $attr   Attributes of the gallery shortcode.
		 */
		$output = apply_filters( 'post_gallery', '', $attr );

		if ( $output != '' )
			return $output;

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}

		$html5 = current_theme_supports( 'html5', 'gallery' );

		extract(shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => $html5 ? 'figure'     : 'dl',
			'icontag'    => $html5 ? 'div'        : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns'    => 3,
			'size'       => 'full',
			'include'    => '',
			'exclude'    => '',
			'link'       => 'none'
		), $attr, 'gallery'));

		$id = intval($id);
		if ( 'RAND' == $order )
			$orderby = 'none';

		if ( !empty($include) ) {
			$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($exclude) ) {
			$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		} else {
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		}

		if ( empty($attachments) )
			return '';

		$i = 0;
		foreach ( $attachments as $id => $attachment ) {
			if ( ! empty( $link ) && 'file' === $link )
				$image_output = wp_get_attachment_link( $id, $size, false, false );
			elseif ( ! empty( $link ) && 'none' === $link )
				$image_output = wp_get_attachment_image( $id, $size, false );
			else
				$image_output = wp_get_attachment_link( $id, $size, true, false );

			$image_meta  = wp_get_attachment_metadata( $id );

			$output .= $image_output."\n";
		}

		return $output;

	}
	/* ==================================================
	* Load Script
	* @param	none
	* @since	4.02
	*/
	function load_frontend_scripts(){

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'nivo-slider', SIMPLENIVOSLIDER_PLUGIN_URL.'/nivo-slider/jquery.nivo.slider.pack.js', null, '3.2' );
	}

	/* ==================================================
	* Load Localize Script and Style
	* @param	none
	* @since	5.00
	*/
	function load_localize_scripts_styles() {

		$localize_nivo_settings = array();
		wp_enqueue_script( 'nivo-slider-jquery', SIMPLENIVOSLIDER_PLUGIN_URL.'/js/jquery.simplenivoslider.js',array('jquery') );
		foreach($this->simplenivoslider_atts as $key => $value) {
			// Script
			$localize_nivo_settings = array_merge($localize_nivo_settings, $value);
			// Style
			$theme = $value['theme'.$key];
			$thumbswidth = $value['thumbswidth'.$key];
			$simplenivoslider_id = $value['id'.$key];
			wp_enqueue_style( 'nivo-slider-themes'.$simplenivoslider_id,  SIMPLENIVOSLIDER_PLUGIN_URL.'/nivo-slider/themes/'.$theme.'/'.$theme.'.css' );
			wp_enqueue_style( 'nivo-slider'.$simplenivoslider_id,  SIMPLENIVOSLIDER_PLUGIN_URL.'/nivo-slider/nivo-slider.css' );

			$css = '.theme-'.$theme.' .nivo-controlNav.nivo-thumbs-enabled img{ display: block; width: '.$thumbswidth.'px; height: auto; }';
			wp_add_inline_style( 'nivo-slider'.$simplenivoslider_id, $css );

		}
		// Script
		$maxcount = array( 'maxcount' => $this->simplenivoslider_count );
		$localize_nivo_settings = array_merge($localize_nivo_settings, $maxcount);
		wp_localize_script( 'nivo-slider-jquery', 'nivo_settings', $localize_nivo_settings );

	}

	/* ==================================================
	 * Short code
	 * @param	Array	$atts
	 * @param	String	$content
	 * @return	String	$content
	 * @since	4.00
	 */
	function simplenivoslider_func( $atts, $content = NULL ) {

		extract(shortcode_atts(array(
			'theme' => '',
			'effect' => '',
			'slices' => '',
			'boxcols' => '',
			'boxrows' => '',
			'animspeed' => '',
			'pausetime' => '',
			'startslide' => '',
			'directionnav' => '',
			'controlnav' => '',
			'controlnavthumbs' => '',
			'thumbswidth' => '',
			'pauseonhover' => '',
			'manualadvance' => '',
			'prevtext' => '',
			'nexttext' => '',
			'randomstart' => ''
		), $atts));

		$settings_tbl = get_option('simplenivoslider_settings');

		foreach ($settings_tbl as $key => $value) {
			$shortcodekey = strtolower($key);
			if ( empty($atts[$shortcodekey]) ) {
				$atts[$shortcodekey] = $value;
			} else if ( strtolower($atts[$shortcodekey]) === 'false' ) {
				$atts[$shortcodekey] = NULL;
			}
		}

		++$this->simplenivoslider_count;
		$simplenivoslider_id = get_the_ID().'-'.$this->simplenivoslider_count;

		$content = $this->add_img_tag($content, $simplenivoslider_id, $atts);

		$new_atts = array();
		foreach ( $atts as $key => $value ) {
			$new_atts[$key.$this->simplenivoslider_count] = $value;
		}
		$id_count_tbl = array( 'id'.$this->simplenivoslider_count => $simplenivoslider_id );
		$this->simplenivoslider_atts[$this->simplenivoslider_count] = array_merge($new_atts, $id_count_tbl);

		return do_shortcode($content);

	}

}

?>