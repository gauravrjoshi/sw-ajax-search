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
?>
	<div class="sw_ajax_search_form_wrap box_shadow_sw">
			<form class="form-inline" action="<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>" method="POST" id="filter" autocomplete="off">


			<div class="form-group mb-2">
				<?php
				if ($terms = get_terms(array('taxonomy' => 'category', 'orderby' => 'name'))) :

					echo '<select name="categoryfilter" class="categoryfilter">';
					echo '<option value="" selected>All Categories</option>';
					foreach ($terms as $term) :
						$selectd = $term->term_id == 1630 ? 'selected' : '';
						echo '<option value="' . esc_attr($term->term_id) . '" ' . esc_attr($selectd) . '>' . esc_html($term->name) . '</option>';

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
			<?php wp_nonce_field('statelyworld_post_request_action', 'statelyworld_post_request_nonce'); ?>
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

function statelyworld_post_request_function() {
    // Verify nonce first. Check if it's not set or if the verification fails.
    if ( ! isset($_POST['statelyworld_post_request_nonce']) || 
         ! wp_verify_nonce($_POST['statelyworld_post_request_nonce'], 'statelyworld_post_request_action') ) {
        wp_die('Security check failed');
    }

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
