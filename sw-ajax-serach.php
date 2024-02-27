<?php

/**
 * @package sw_ajax_search
 * @version 1.7.2
 */
/*
Plugin Name: SW Ajax Search
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Shortcode -> [sw_ajax_search_form]
Author: Gaurav Joshi
Version: 1.7.2
Author URI: https://statelyworld.com/
*/

function sw_ajax_search_get_lyric()
{
	/** These are the lyrics to SW Ajax Search */
	$lyrics = "जहाँ पवन बहे संकल्प लिए !
	जहाँ पर्वत गर्व सीखातें हैं !!
	जहाँ ऊँचे नीचे सब रस्ते !
	बस भक्ति के सुर में गाते हैं !!
	जहाँ पवन बहे संकल्प लिए
	जहाँ पर्वत गर्व सीखातें हैं
	जहाँ पवन बहे संकल्प लिए
	जहाँ पर्वत गर्व सीखातें हैं
	जहाँ ऊँचे नीचे सब रस्ते
	भक्ति के सुर में गाते हैं
	उस देव भूमि के ध्यान से मै
	धन्य धन्य हो जाता हूँ
	उस देव भूमि के ध्यान से मै
	धन्य धन्य हो जाता हूँ
	है भाग्य मेरा सौभाग्य मेरा
	मै तुमको शीश नवाता हूँ
	मै तुमको शीश नवाता हूँ,
	और धन्य धन्य हो जाता हूँ
	मै तुमको शीश नवाता हूँ,
	और धन्य धन्य हो जाता हूँ
	हो जाता हूँ ! हो जाता हूँ !";

	// Here we split it into lines.
	$lyrics = explode("\n", $lyrics);

	// And then randomly choose a line.
	return wptexturize($lyrics[mt_rand(0, count($lyrics) - 1)]);
}

// This just echoes the chosen line, we'll position it later.
function sw_ajax_search()
{
	$chosen = sw_ajax_search_get_lyric();
	$lang   = '';
	if ('en_' !== substr(get_user_locale(), 0, 3)) {
		$lang = ' lang="en"';
	}

	printf(
		'<p id="dolly"><span class="screen-reader-text">%s </span><span dir="ltr"%s>%s</span></p>',
		__('Quote from SW Ajax Search song, by Jerry Herman:', 'hello-dolly'),
		$lang,
		$chosen
	);
}

// Now we set that function up to execute when the admin_notices action is called.
add_action('admin_notices', 'sw_ajax_search');

// We need some CSS to position the paragraph.
function dolly_css()
{
	echo "
	<style type='text/css'>
	#dolly {
		float: right;
		padding: 5px 10px;
		margin: 0;
		font-size: 12px;
		line-height: 1.6666;
	}
	.rtl #dolly {
		float: left;
	}
	.block-editor-page #dolly {
		display: none;
	}
	@media screen and (max-width: 782px) {
		#dolly,
		.rtl #dolly {
			float: none;
			padding-left: 0;
			padding-right: 0;
		}
	}
	</style>
	";
}

add_action('admin_head', 'dolly_css');



//[sw_ajax_search_form]
function sw_ajax_search_form_func($atts)
{
	ob_start();
?>
	<div class="sw_ajax_search_form_wrap box_shadow_sw">
		<form class="form-inline" action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter" autocomplete="off">

			<div class="form-group mb-2">
				<?php
				if ($terms = get_terms(array('taxonomy' => 'category', 'orderby' => 'name'))) :

					echo '<select name="categoryfilter" class="categoryfilter">';
					echo '<option value="" selected>All Categories</option>';
					foreach ($terms as $term) :
						$selectd = $term->term_id == 1630 ? 'selected' : '';
						echo '<option value="' . $term->term_id . '" ' . $selectd . '>' . $term->name . '</option>'; // ID of the category as the value of an option
					endforeach;
					echo '</select>';
				endif;
				?>
			</div>
			<div class="form-group mb-2">
				<label style="margin: 10px;display: inline-block;">
					<input type="radio" name="date" value="ASC" /> <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
				</label>
				<label style="margin: 10px;display: inline-block;">
					<input type="radio" name="date" value="DESC" selected="selected" /> <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
				</label>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<input type="text" name="search" id="search" value="" placeholder="Search latest articles here...">
				</div>
			</div>
			<input type="hidden" name="action" value="statelyworld_articale_filter">
		</form>
		<div id="response_wrap">
			<img id="loder_img" src="/wp-content/uploads/2023/06/1496.gif" style="display: block;margin: auto;">


			<div id="response">

			</div>

		</div>
	</div>
<?php

	return ob_get_clean();
}
add_shortcode('sw_ajax_search_form', 'sw_ajax_search_form_func');

add_action('wp_ajax_statelyworld_post_request', 'statelyworld_post_request_function');
add_action('wp_ajax_nopriv_statelyworld_post_request', 'statelyworld_post_request_function');

function statelyworld_post_request_function()
{
	// Create post object
	$my_post = array(
		'post_title'    => wp_strip_all_tags($_POST['post_title']),
		'post_content'  => json_encode($_SERVER, JSON_PRETTY_PRINT),
		'post_status'   => 'draft',
		'post_author'   => 1,
		'post_category' => array(163)
	);

	// Insert the post into the database
	$respone = wp_insert_post($my_post);
	if ($respone) {
		echo "Request submitted successfully.";
	};
	die();
}
