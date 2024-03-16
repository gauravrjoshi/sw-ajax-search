<?php

/**
 * Plugin Name: SW Ajax Search
 * Plugin URI: https://wordpress.org/plugins/sw-ajax-search
 * Description: Shortcode -> [sw_ajax_search_form]
 * Author: Gaurav Joshi
 * Version: 1.7.5
 * Author URI: https://statelyworld.com/
 */

// Shortcode for search form
function sw_ajax_search_form_func()
{
	ob_start();
	include plugin_dir_path(__FILE__) . 'sw-search-form.php';
	return ob_get_clean();
}
add_shortcode('sw_ajax_search_form', 'sw_ajax_search_form_func');

// Enqueue styles and scripts
function sw_ajax_search_enqueue_styles()
{
	wp_enqueue_style('sw-ajax-search-styles', plugins_url('assets/css/sw-ajax-search-style.css', __FILE__), array(), '1.0.0');


	// Check if Font Awesome is already enqueued
	if (!wp_style_is('font-awesome', 'enqueued')) {
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '1.0.0');
	}

	wp_enqueue_script('sw-ajax-search-script', plugins_url('/assets/js/sw-ajax-search-script.js', __FILE__), array('jquery'), filemtime(plugin_dir_path(__FILE__) . '/assets/js/sw-ajax-search-script.js'), true);

	// Create a nonce and pass it to the script
	wp_localize_script('sw-ajax-search-script', 'sw_ajax_search_params', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('sw_ajax_search_nonce'), // Create nonce
	));
}
add_action('wp_enqueue_scripts', 'sw_ajax_search_enqueue_styles');

// AJAX actions

add_action('wp_ajax_stately_world_article_filter', 'stately_world_article_filter_function');
add_action('wp_ajax_nopriv_stately_world_article_filter', 'stately_world_article_filter_function');

function stately_world_article_filter_function()
{
	// Verify nonce
	if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'sw_ajax_search_nonce')) {
		wp_die('Security check failed');
	}
	// Validate and sanitize all inputs
	$search = sanitize_text_field($_POST['search']);
	$order = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'DESC';

	// Define the query
	$args = array(
		'orderby' => 'date',
		'order' => $order,
		'posts_per_page' => 10,
		'post_status' => array('publish', 'private', 'draft'),
		's' => $search
	);


	$query = new WP_Query($args);
	$data = [];
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$data[] = [
				'url' => esc_url(get_permalink()),
				'title' => get_the_title(),
				'status' => get_post_status(),
				'date' => get_the_date(),
			];
		}
		wp_reset_postdata();
	}
	header('content-type: application/json; charset=utf-8');
	echo wp_json_encode($data); // Ensure $html is safe to output
	wp_die(); // Use wp_die() in AJAX handlers
}
