<?php
/*
Plugin Name: H8UoEmbed
Plugin URI: http://github.com/HTeuMeuLeu/H8UoEmbed
Version: 0.1
Description: Overrides WordPress default oEmbed settings to optimize performance.
Author: HTeuMeuLeu
Author URI: http://www.hteumeuleu.fr
License: WTFPL
License URI: http://www.wtfpl.net/
*/

class H8UoEmbed {

	var $video_providers = array();

	function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks for actions and filters through WordPress.
	 */
	private function init_hooks() {
		add_filter('oembed_dataparse', array(&$this, 'override_oembed'), 1, 3);
		add_filter('embed_defaults', array(&$this, 'set_default_size_to_large'));
		add_filter('embed_oembed_html', array(&$this, 'add_video_provider'), 1, 4);

		add_action('wp_footer', array(&$this, 'add_css'));
		add_action('wp_footer', array(&$this, 'add_script'));
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
			$img_size = '';
			$oembed_size = '';
			$title = '';
			$oembed_html = '';

			if(!empty($data->thumbnail_width) && !empty($data->thumbnail_height) && is_numeric($data->thumbnail_width) && is_numeric($data->thumbnail_height))
				$img_size = ' width="'.esc_attr($data->thumbnail_width).'" height="'.esc_attr($data->thumbnail_height).'"';
			if(!empty($data->width) && !empty($data->height) && is_numeric($data->width) && is_numeric($data->height))
				$oembed_size = ' style="max-width:'.esc_attr($data->width).'px; max-height:'.esc_attr($data->height).'px;"';
			if(!empty($data->title) && is_string($data->title))
				$title = $data->title;
			if(!empty($data->html) && is_string($data->html))
				$oembed_html = ' data-H8UoEmbed-html="'.esc_attr($this->add_autoplay($data->html)).'"';

			$html = '<div class="H8UoEmbed"'.$oembed_size.'>';
			$html .= '<a class="H8UoEmbed-link" href="'.esc_url($url).'" title="'.esc_attr($title).'"'.$oembed_html.'><img src="'.esc_url($data->thumbnail_url).'" alt="'.esc_attr($title).'"'.$img_size.'/></a>'; 
			$html .= '</div>';
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
	 * Add necessary scripts for videos on the current page, based on the list of providers generated for this page.
	 */
	function add_script() {
		if($this->assets_are_needed()) {
?>
	<script>
		document.addEventListener('DOMContentLoaded', H8UoEmbedInit);

		function H8UoEmbedInit() {
			var H8UoEmbedVideos = document.querySelectorAll('.H8UoEmbed-link[data-H8UoEmbed-html]');
			for(var i=0; i < H8UoEmbedVideos.length; i++)
			{
				H8UoEmbedVideos[i].addEventListener('click', function(e) {
					e.preventDefault();
					var oEmbedHTML = this.getAttribute('data-H8UoEmbed-html');
					this.parentNode.innerHTML = oEmbedHTML;
				});
			}
		}
	</script>
<?php
		}
	}

	/**
	 * Add necessary styles for videos on the current page, based on the list of providers generated for this page.
	 * If a provider is not necessary, its styles are not included.
	 */
	function add_css() {
		if($this->assets_are_needed()) {
			$video_providers_styles = array(
				'youtube.com' =>
					'.H8UoEmbed-link[href*="youtube.com"]:before,'.
					'.H8UoEmbed-link[href*="youtu.be"]:before { left:0; right:0; top:0; padding:8px 15px; font:13px/1 Arial, sans-serif; color:#fff; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; background:#000; background:rgba(0,0,0,0.8); }'.
					'.H8UoEmbed-link[href*="youtube.com"]:after,'.
					'.H8UoEmbed-link[href*="youtu.be"]:after { content:\'\25BA\'; position:absolute; left:50%; top:50%; margin:-30px 0 0 -42px; width:84px; height:60px; font:25px/60px Arial, sans-serif; color:#fff; text-align:center; text-indent:0; background:#000; background:rgba(0,0,0,0.8); border:none; border-radius:10px / 30px; }'.
					'.H8UoEmbed-link[href*="youtube.com"]:hover:after,'.
					'.H8UoEmbed-link[href*="youtu.be"]:hover:after { background-color:#cc181e; }',
				'vimeo.com' =>
					'.H8UoEmbed-link[href*="vimeo.com"]:before { padding:6px 4px; left:10px; right:auto; top:10px; max-width:calc(100% - 20px); color:#00adef; font-weight:bold; font-size:20px; background:rgba(23,35,35,0.8); }'.
					'.H8UoEmbed-link[href*="vimeo.com"]:after { margin:-20px 0 0 -32px; width:65px; height:40px; line-height:40px; font-size:20px; border-radius:5px; border:none; text-indent:0; background:rgba(23,35,35,0.8); }'.
					'.H8UoEmbed-link[href*="vimeo.com"]:hover:after { background-color:#00adef; }',
				'dailymotion.com' =>
					'.H8UoEmbed-link[href*="dailymotion.com"]:before { top:auto; bottom:0; min-height:60px; padding:4px 20px 4px 80px; font:bold 18px/1.25 Arial, sans-serif; border-top:1px solid rgba(0,0,0,0.3); background:rgba(0,0,0,0.2); }'.
					'.H8UoEmbed-link[href*="dailymotion.com"]:after { left:4px; top:auto; bottom:4px; margin:0; width:70px; height:60px; line-height:60px; font-size:25px; border-radius:4px; border:1px solid #000; text-indent:0; background:#171d1b; }'.
					'.H8UoEmbed-link[href*="dailymotion.com"]:hover:before { color:#ffcc33; border-top-color:#000; background:rgba(0,0,0,0.8); }'.
					'.H8UoEmbed-link[href*="dailymotion.com"]:hover:after { color:#ffcc33; }'
			);

			$current_video_providers_styles = implode("\n", array_intersect_key($video_providers_styles, $this->video_providers));
?>
	<style type="text/css">
		.H8UoEmbed { background:#000; }
			.H8UoEmbed iframe, .H8UoEmbed object, .H8UoEmbed video { display:block; }
		.H8UoEmbed-link { position:relative; display:block; }
			.H8UoEmbed-link img { display:block; margin:0; width:100%; height:auto; }
			.H8UoEmbed-link:before { content:attr(title); position:absolute; left:0; right:0; top:0; padding:8px 15px; font:13px/1 Arial, sans-serif; color:#fff; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; background:#000; background:rgba(0,0,0,0.8); transition:all ease-in-out 0.3s; }
			.H8UoEmbed-link:after { content:'\25BA'; position:absolute; left:50%; top:50%; margin:-35px 0 0 -35px; width:60px; height:60px; font:30px/60px Arial, sans-serif; color:#fff; text-align:center; text-indent:5px; border-radius:35px; border:5px solid #fff; transition:all ease-in-out 0.3s; }
		<?php echo $current_video_providers_styles; ?>
	</style>
<?php
		}
	}

	/**
	 * Add a video provider to the list of video providers seen on the current page.
	 * This function is called on the embed_oembed_html filter.
	 *
	 * @see WP_Embed::shortcode()
	 */
	function add_video_provider($html, $url, $attr, $post_ID) {
		if(strpos($html, 'data-H8UoEmbed-html') !== false) {
			$url_parsed = parse_url($url);
			if(!empty($url_parsed)) {
				$host = $url_parsed['host'];
				$host = str_replace('www.', '', $host);
				if(!in_array($host, $this->video_providers))
					$this->video_providers[$host][] = $url;
			}
		}
		return $html;
	}

	/**
	 * Check if the CSS and JS assets need to be added on the current page.
	 */
	private function assets_are_needed() {
		return !empty($this->video_providers);
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
}

$H8UoEmbed = new H8UoEmbed();