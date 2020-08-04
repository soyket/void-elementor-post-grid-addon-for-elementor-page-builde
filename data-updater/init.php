<?php

include VOID_GRID_PLUGIN_DIR.'data-updater/taxonomy-repeater.php';

// get data update info from database
$update_data = get_option('VOID_GRID_DATA_UPDATE');
/**
 * call taxonomy repeater data updater function by get method
 */
if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['void_grid_database_updater']) && $_GET['void_grid_database_updater'] == 'taxonomy-repeater' ){

    if ( !isset($update_data['taxonomy_repeater']['status']) || $update_data['taxonomy_repeater']['status'] != '1') {
        
        void_grid_data_update_taxonomy_repeater();

        delete_option('void_grid_elementor_post_grid_activation_time');
        // call to show updated notice
        add_action('admin_notices', 'void_grid_data_updated_notice');
    }
}

/**
 * database updater function
 *
 * @return void
 * @since 2.1
 */
function void_grid_data_updated_notice() {

    // get data update info from database
    $update_data = get_option('VOID_GRID_DATA_UPDATE');

    // show database update notice after updated
    if ( isset($update_data['taxonomy_repeater']['status']) || $update_data['taxonomy_repeater']['status'] == '1') {
        ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php echo __( '<strong>Success! </strong> database updated successfully.' ); ?></p>
    </div>
    <?php
    }
}

?>