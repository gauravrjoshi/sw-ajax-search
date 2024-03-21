<?php

/**
 * Plugin Name: SW Ajax Search
 * Plugin URI: https://wordpress.org/plugins/sw-ajax-search
 * Description: SW Ajax Search simplifies the search experience for users on your WordPress website by implementing AJAX-based search functionality. This plugin allows you to insert a search form anywhere on your site using a simple shortcode.
 * Author: Gaurav Joshi
 * Version: 1.7.6
 * Author URI: https://statelyworld.com/
 */

// Shortcode for search form
function sw_ajax_search_form_func()
{
	// Retrieve option value from the database
	$enable_global_search_checked = get_option('sw_ajax_search_enable_global_search', '0');
	// Add action hook if global search is enabled
	if ($enable_global_search_checked == '1') {
		return null;
	} else {
		ob_start();
		include plugin_dir_path(__FILE__) . 'sw-search-form.php';
		return ob_get_clean();
	}
}
add_shortcode('sw_ajax_search_form', 'sw_ajax_search_form_func');



/**
 * Adds the SW Global Search form to the footer.
 */
function sw_global_search()
{
	$search_form_path = plugin_dir_path(__FILE__) . 'sw-search-form.php';
	if (file_exists($search_form_path)) {
		include $search_form_path;
	}
}

// Retrieve option value from the database
$enable_global_search_checked = get_option('sw_ajax_search_enable_global_search', '0');
// Add action hook if global search is enabled
if ($enable_global_search_checked == '1') {
	add_action('wp_footer', 'sw_global_search');
}

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


	// Get the current user's roles
	$user = wp_get_current_user();
	$user_roles = $user->roles;

	// Check if the user has the 'administrator' role
	$is_admin = in_array('administrator', $user_roles);

	// Define the query args
	$args = array(
		'post_type'        => 'post',
		'orderby'        => 'date',
		'order'          => $order,
		'posts_per_page' => 10,
		'post_status'    => array('publish'),
		's'              => $search,
		'inclusive'       => false,
	);

	// If user is an admin, include 'private' and 'draft' post statuses
	if ($is_admin) {
		$args['post_status'][] = 'private';
		$args['post_status'][] = 'draft';
	}

	$query = new WP_Query($args);
	$data = [];
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$content = get_the_content();
			preg_match("/\b$search\b/i", $content, $matches); // Case insensitive match
			$data[] = array(
				'url'       => esc_url(get_permalink()),
				'title'     => get_the_title(),
				'status'    => get_post_status(),
				'date'      => get_the_date(),
				'paragraph'      => !empty($matches) ? $matches[0] : null,
			);
		}
		wp_reset_postdata();
	}
	header('content-type: application/json; charset=utf-8');
	echo wp_json_encode($data); // Ensure $html is safe to output
	wp_die(); // Use wp_die() in AJAX handlers
}



// Add a menu item to the admin menu
function sw_ajax_search_menu()
{
	add_menu_page(
		'SW AJAX Search',      // Page title
		'SW AJAX Search',      // Menu title
		'manage_options',     // Capability required to access the menu
		'sw-ajax-search',     // Menu slug
		'sw_ajax_search_page', // Callback function to display the page content
		'dashicons-search',   // Icon URL or Dashicons class
		100                    // Position in the menu
	);
}
add_action('admin_menu', 'sw_ajax_search_menu');

// Callback function to display the page content
function sw_ajax_search_page()
{
	// Check if form is submitted
	if (isset($_POST['sw_ajax_search_submit'])) {
		// Save the option value in the database
		$enable_global_search = isset($_POST['enable_global_search']) ? '1' : '0';
		update_option('sw_ajax_search_enable_global_search', $enable_global_search);
		echo '<div class="updated"><p>Settings saved.</p></div>';
	}

	// Retrieve option value from the database
	$enable_global_search_checked = get_option('sw_ajax_search_enable_global_search', '0');
?>
	<div class="wrap">
		<h2>SW AJAX Search Settings</h2>
		<form method="post" action="">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Enable Global Search</th>
					<td>
						<label for="enable_global_search">
							<input type="checkbox" id="enable_global_search" name="enable_global_search" value="1" <?php checked($enable_global_search_checked, '1'); ?>>
							Enable global search
						</label>
					</td>
				</tr>
			</table>
			<input type="hidden" name="sw_ajax_search_submit" value="1">
			<?php submit_button('Save Settings'); ?>
		</form>
	</div>
<?php
}
