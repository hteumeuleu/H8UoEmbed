<?php
/*
Plugin Name: H8UoEmbed
Plugin URI: http://github.com/HTeuMeuLeu/H8UoEmbed
Version: 0.3
Description: Overrides WordPress default oEmbed settings to optimize performance.
Author: HTeuMeuLeu
Author URI: http://www.hteumeuleu.fr
License: WTFPL
License URI: http://www.wtfpl.net/
*/

class H8UoEmbed {
	
	function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks for actions and filters through WordPress.
	 */
	private function init_hooks() {
		add_filter('oembed_dataparse', array(&$this, 'override_oembed'), 1, 3);
		add_filter('embed_defaults', array(&$this, 'set_default_size_to_large'));

		add_action('wp_enqueue_scripts', array(&$this, 'add_css'));
		add_action('wp_enqueue_scripts', array(&$this, 'add_script'));
	}

	/**
	 * Overrides WordPress' default oEmbed rules for videos.
	 * Instead of directly embedding a video player, this function
	 * will generate HTML for a static link with a static thumbnail image
	 * from the oEmbed $data response.
	 *
	 * This function is called on the oembed_dataparse filter.
	 *
	 * @see WP_oEmbed::data2html()
	 */
	function override_oembed($html, $data, $url)
	{
		if($data->type == 'video' && !empty($data->thumbnail_url) && is_string($data->thumbnail_url))
		{
			$oembed_style = '';
			$oembed_html = '';
			$oembed_class = '';
			$title = '';
			$img_size = '';
			$img_class = '';

			if(!empty($data->thumbnail_width) && !empty($data->thumbnail_height) && is_numeric($data->thumbnail_width) && is_numeric($data->thumbnail_height))
				$img_size = ' width="'.esc_attr($data->thumbnail_width).'" height="'.esc_attr($data->thumbnail_height).'"';
			if(!empty($data->width) && is_numeric($data->width))
				$oembed_style = ' style="max-width:'.esc_attr($data->width).'px;"';
			if(!empty($data->title) && is_string($data->title))
				$title = $data->title;
			if(!empty($data->html) && is_string($data->html))
				$oembed_html = ' data-H8UoEmbed-html="'.esc_attr($this->add_autoplay($data->html)).'"';
			if($this->is_ratio($data->width, $data->height, 16/9) && $this->is_ratio($data->thumbnail_width, $data->thumbnail_height, 4/3))
			{
				$oembed_class = ' H8UoEmbed--ratio-16-9';
				$img_class = ' H8UoEmbed-img--ratio-4-3';
			}

			$html = '<p class="H8UoEmbed'.$oembed_class.'"'.$oembed_style.'>';
			$html .= '<a class="H8UoEmbed-link" href="'.esc_url($url).'" title="'.esc_attr($title).'"'.$oembed_html.'><img src="'.esc_url($data->thumbnail_url).'" alt="'.esc_attr($title).'" class="H8UoEmbed-img'.$img_class.'"'.$img_size.'/></a>'; 
			$html .= '</p>';
		}
		return $html;
	}

	/**
	 * Sets the default size for video embeds to match the default large size image set in the site configuration.
	 * This function is called on the embed_defaults filter.
	 *
	 * @see wp_embed_defaults() in wp-includes/media.php
	 */
	function set_default_size_to_large($args)
	{
		$large_size_w = get_option('large_size_w');
		$large_size_h = get_option('large_size_h');
		if(!empty($large_size_w) && !empty($large_size_h) && is_numeric($large_size_w) && is_numeric($large_size_h))
		{
			$args['width'] = $large_size_w;
			$args['height'] = $large_size_h;
		}
		return $args;
	}

	/**
	 * Add necessary scripts for videos on the current page.
	 */
	function add_script() {
		wp_enqueue_script('H8UoEmbed', plugins_url('assets/h8uoembed.js', __FILE__));
	}

	/**
	 * Add necessary styles for videos on the current page.
	 */
	function add_css() {
		wp_enqueue_style('H8UoEmbed', plugins_url('assets/h8uoembed.css', __FILE__));
	}

	/**
	 * Add autoplay parameters to video embed URL if possible,
	 * so that the video automatically plays when the user clicks on the static link.
	 *
	 * @param string $data the HTML content sent from the oEmbed response
	 */
	private function add_autoplay($data) {
		$regex = '/^(<iframe.*? src=")(.*?)((\?)(.*?))?(".*)$/';
		$data = preg_replace($regex, '$1$2?$5&autoplay=1$6', $data);
		return $data;
	}

	/**
	 * Check if the width and height is a specific ratio aspect.
	 *
	 * @param int $width
	 * @param int $height
	 * @param int $ratio
	 */
	private function is_ratio($width, $height, $ratio) {
		if(is_numeric($width) && is_numeric($height) && is_numeric($ratio)) {
			$actual_ratio = round($width / $height, 2);
			$desired_ratio = round($ratio, 2);
			return ($actual_ratio === $desired_ratio);
		}
		return false;
	}
}

$H8UoEmbed = new H8UoEmbed();