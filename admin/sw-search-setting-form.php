<?php // Retrieve option value from the database
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
                        <input type="hidden" name="sw_ajax_search_nonce" value="<?php echo esc_attr(wp_create_nonce('sw_ajax_search_nonce')); ?>">
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