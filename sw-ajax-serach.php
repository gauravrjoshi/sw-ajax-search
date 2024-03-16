<?php

/**
 * @package sw_ajax_search
 * @version 1.7.2
 */
/*
Plugin Name: SW Ajax Search
Plugin URI: https://wordpress.org/plugins/sw-ajax-search
Description: Shortcode -> [sw_ajax_search_form]
Author: Gaurav Joshi
Version: 1.7.2
Author URI: https://statelyworld.com/
*/

//[sw_ajax_search_form]
function sw_ajax_search_form_func($atts)
{
	ob_start();
	// Include the HTML file. Adjust the path as needed.
	include(plugin_dir_path(__FILE__) . 'sw-search-form.php');

	return ob_get_clean();
}
add_shortcode('sw_ajax_search_form', 'sw_ajax_search_form_func');

add_action('wp_ajax_statelyworld_post_request', 'statelyworld_post_request_function');
add_action('wp_ajax_nopriv_statelyworld_post_request', 'statelyworld_post_request_function');

function statelyworld_post_request_function()
{
	// Verify nonce first. Check if it's not set or if the verification fails.
	/* if (
		!isset($_POST['action']) ||
		!wp_verify_nonce($_POST['action'], 'statelyworld_post_request_action')
	) {
		wp_die('Security check failed');
	}
 */
	// Proceed with form processing only if nonce verification passed
	$my_post = array(
		'post_title'    => wp_strip_all_tags($_POST['post_title']),
		'post_content'  => wp_json_encode($_SERVER, JSON_PRETTY_PRINT),
		'post_status'   => 'draft',
		'post_author'   => 1,
		'post_category' => array(163)
	);

	// Insert the post into the database
	$response = wp_insert_post($my_post);
	if ($response) {
		echo "Request submitted successfully.";
	}
	die();
}



function sw_ajax_search_enqueue_styles()
{
	// Use plugins_url to get the correct path to your CSS file
	// die(plugins_url( 'assets/css/sw-ajax-search-style.css', __FILE__ ) );
	wp_enqueue_style('sw-ajax-search-styles', plugins_url('assets/css/sw-ajax-search-style.css', __FILE__));


	// The handle name used when Font Awesome was registered or enqueued.
	$font_awesome_handle = 'font-awesome'; // Example handle, adjust based on actual handle name if known.

	// Check if Font Awesome is already enqueued.
	if (!wp_style_is($font_awesome_handle, 'enqueued')) {
		// Since Font Awesome is not enqueued, enqueue it here.
		wp_enqueue_style('font-awesome-free', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
	}

	// Correctly enqueue your JavaScript file
	wp_enqueue_script(
		'sw-ajax-search-script', // Handle for the script.
		plugins_url('/assets/js/sw-ajax-search-script.js', __FILE__), // Path to the script.
		array('jquery'), // Dependencies, e.g., jQuery.
		filemtime(plugin_dir_path(__FILE__) . '/assets/js/sw-ajax-search-script.js'), // Version, for cache busting.
		true // Whether to place the script in the footer.
	);
}

// Hook into wp_enqueue_scripts to add your stylesheet for front-end
add_action('wp_enqueue_scripts', 'sw_ajax_search_enqueue_styles');


add_action('wp_ajax_statelyworld_articale_filter', 'statelyworld_articale_filter_function');
add_action('wp_ajax_nopriv_statelyworld_articale_filter', 'statelyworld_articale_filter_function');

function statelyworld_articale_filter_function()
{

	$meta_query = array('relation' => 'AND');
	$tax_query = array();

	if (isset($_POST['categoryfilter']) && $_POST['categoryfilter'] != '')
		$tax_query[] = array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => $_POST['categoryfilter']
		);
	$search = sanitize_text_field($_POST['search']);

	$status = ['publish'];
	if (is_user_logged_in()) {
		array_push($status, "private", "draft");
	}
	$args = array(
		'orderby' => 'date',
		'order'    => $_POST['date'],
		'posts_per_page' => 10,
		'post_status' => $status,
		'meta_query' => $meta_query,
		'tax_query' => array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => 348
		),
	);

	$status = ['publish'];
	if (!is_admin()) {
		array_push($status, "private", "draft");
	}



	if ((isset($_POST['search']) && $_POST['search'] != '') || (isset($_POST['categoryfilter']) && $_POST['categoryfilter'] != '')) {
		$search_query = new WP_Query(array(
			'orderby' => 'date',
			'order'    => $_POST['date'],
			'posts_per_page' => -1,
			'post_status' => $status,
			'meta_query' => $meta_query,
			'tax_query' => $tax_query,
			'post_type' => 'post',
			's' => $search
		));
	} else {
		$search_query = new WP_Query($args);
	}

	$query = $search_query;

	$html = '<ul id="slider-id" class="slider-class" style="list-style: auto;padding: 0 10px;margin: 0 20px;"> ';

	if ($query->have_posts()) :
		while ($query->have_posts()) : $query->the_post();
			if (is_user_logged_in()) {
				$status_txt  = $query->post->post_status != 'publish' ? "<span style='color: green;text-transform: capitalize;font-weight: 600;'> (" . $query->post->post_status . ")</span>" : '';
			}
			$date = ' <span style="color: #ef7f1a;">(' . get_the_date() . ')</span>';
			$html .= '<li class="article_list_202105030749"><a href="' . get_permalink($post->ID) . '" rel="bookmark" target="_blank">';
			$html .= '' . $query->post->post_title . '';
			$html .=  $query->post->post_status == 'private' ? ' <span style="color: #ef7f1a;">(Comming Soon)</span>' : $date .  $status_txt;
			$html .= '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-clone" data_copy_id="' . get_the_ID() . '" aria-hidden="true" onclick="copyToClipboard(\'' . get_permalink($post->ID) . '\',\'' . get_the_ID() . '\')"></i></li>';
		endwhile;
		wp_reset_postdata();
	else :
		$html .= '<h2 style="text-align: center;color:#ffffff;">Nothing Found</h2>
        <button class="send_post_request_button" type="button" style="margin: 10px auto;display: block;">Submit Request</button>
        <p style="text-align: center;">Submit your request for blog on <strong class="send_post_request"></strong> by clicking above button.
        </p>
        <p style="text-align: center;">Sorry, but nothing matched your search terms. Please try again with some different keywords.</p>';
	endif;
	$html .= ' </ul>';
	echo $html;

	die();
}
