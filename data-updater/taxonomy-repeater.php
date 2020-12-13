<?php
/**
 * taxonomy data updater call function
 *
 * @return void
 * @since 2.1
 */
function void_grid_data_update_taxonomy_repeater(){

    global $wpdb;

    // get all pages id which is used elementor controls usage for taking void query builder used pages id
	$sql = $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE `meta_key` = %s", "_elementor_controls_usage");
	$control_usage = $wpdb->get_results( $sql , OBJECT );

    $cnt = 0;
	foreach($control_usage as $key =>$value){

        $meta_value = unserialize($value->meta_value);

        // void query builder used widget's page condition
		if(array_key_exists('void-post-grid', $meta_value)){
            $update_status = void_grid_data_taxonomy_update($value->post_id);
            if($update_status){
                $cnt++;
            }
		}
    }

    // update info ready
    $update_info = [
        'taxonomy_repeater' => [
            'from_version' => '2.0',
            'to_version' => VOID_GRID_VERSION,
            'status' => '1'
        ],
    ];

    if($cnt > 0){
        // store update info in db
        return update_option('VOID_GRID_DATA_UPDATE', $update_info);
    }
    
}

/**
 * taxonomy data updater by id function
 *
 * @return void
 * @since 2.1
 * @param int $id
 */
function void_grid_data_taxonomy_update($id){

    // get elementor settings
    $elementor_db_setting = json_decode(get_post_meta($id, '_elementor_data', true));

    // loop for multiple elementor control widget data in a page
	foreach($elementor_db_setting as $key=>$value){

        // loop for multiple column
        foreach($value->elements as $key_column => $value_column){
            // loop for multiple section in a column
            foreach( $value_column->elements as $k => $v){

                // check inner section used in column
                if($v->elType == 'section'){
                    // loop for multiple inner section in a section
                    foreach($v->elements as $sk => $sv){
                        // loop for mutiple widget in an inner section
                        foreach($sv->elements as $sek => $sev){
                            // check is it widget and void-post-grid widget
                            if($sev->elType == 'widget' && $sev->widgetType == 'void-post-grid'){
                                // avoid non-object error
                                $settings = (is_object($sev->settings)) ? $sev->settings: (object)($sev->settings);
                                // property check
                                if(property_exists($settings, 'taxonomy_type') || property_exists($settings, 'terms')){
                    
                                    $display_type = isset($settings->display_type)? $settings->display_type: '1';
                                    $image_style = isset($settings->image_style)? $settings->image_style: '1';

                                    // display type handler will be remove after data updater
                                    switch ($display_type) {
                                        case "1":
                                            $display_type = 'grid-1';
                                            break;
                                        case "2":
                                            $display_type = 'list-1';
                                            break;
                                        case "3":
                                            $display_type = 'first-full-post-grid-1';
                                            break;
                                        case "4":
                                            $display_type = 'first-full-post-list-1';
                                            break;
                                        case "5":
                                            $display_type = 'minimal';
                                            break;
                                        default:
                                            $display_type = $display_type;
                                    }

                                    // image style handler will be remove after data updater
                                    switch ($image_style) {
                                        case '':
                                            $image_style = '';
                                            break;
                                        case '2':
                                            $image_style = 'top-left';
                                            break;
                                        case '3':
                                            $image_style = 'top-right';
                                            break;
                                        default:
                                            $image_style = $image_style;
                                    }

                                    // migrate data to new format
                                    $convert_data_new_format = [
                                        'taxonomy_type' => (isset($settings->taxonomy_type) ? $settings->taxonomy_type : ''),
                                        'terms' => (isset($settings->terms) ? $settings->terms: ''),
                                        '_id' => '',
                                        'url_param' => '',
                                        'compare' => 'LIKE'
                                    ];

                                    // assign data in display type and image style
                                    $settings->display_type = $display_type;
                                    $settings->image_style = $image_style;
                    
                                    // add new property in elementor setting for migration
                                    $settings->{"tax_fields"}[] = (object)$convert_data_new_format;
                                    $settings->{"tax_fields_relation"} = '';
                                    
                                    // clear previous used data
                                    unset($settings->taxonomy_type);
                                    unset($settings->terms);
                    
                                    // assign new migrated data to the settings
                                    $value->elements[$key_column]->elements[$k]->elements[$sk]->elements[$sek]->settings = $settings;
                                }
                            }
                        }
                    }
                }

                // check is it widget and void-post-grid widget
                if($v->elType == 'widget' && $v->widgetType == 'void-post-grid'){

                    // avoid non-object error
                    $settings = (is_object($v->settings)) ? $v->settings: (object)($v->settings);
                    // property check
                    if(property_exists($settings, 'taxonomy_type') || property_exists($settings, 'terms')){
        
                        $display_type = isset($settings->display_type)? $settings->display_type: '1';
                        $image_style = isset($settings->image_style)? $settings->image_style: '1';

                        // display type data converter
                        switch ($display_type) {
                            case "1":
                                $display_type = 'grid-1';
                                break;
                            case "2":
                                $display_type = 'list-1';
                                break;
                            case "3":
                                $display_type = 'first-full-post-grid-1';
                                break;
                            case "4":
                                $display_type = 'first-full-post-list-1';
                                break;
                            case "5":
                                $display_type = 'minimal';
                                break;
                            default:
                                $display_type = $display_type;
                        }

                        // image style data converter
                        switch ($image_style) {
                            case '':
                                $image_style = '';
                                break;
                            case '2':
                                $image_style = 'top-left';
                                break;
                            case '3':
                                $image_style = 'top-right';
                                break;
                            default:
                                $image_style = $image_style;
                        }

                        // migrate data to new format
                        $convert_data_new_format = [
                            'taxonomy_type' => (isset($settings->taxonomy_type) ? $settings->taxonomy_type : ''),
                            'terms' => (isset($settings->terms) ? $settings->terms: ''),
                            '_id' => '',
                            'compare' => 'LIKE'
                        ];

                        // assign data in display type and image style
                        $settings->display_type = $display_type;
                        $settings->image_style = $image_style;
        
                        // add new property in elementor setting for migration
                        $settings->{"tax_fields"}[] = (object)$convert_data_new_format;
                        $settings->{"tax_fields_relation"} = '';
                        
                        // clear previous used data
                        unset($settings->taxonomy_type);
                        unset($settings->terms);
        
                        // assign new migrated data to the settings
                        $value->elements[$key_column]->elements[$k]->settings = $settings;
                    }
                }

            }
        }

        // assign data to the the elementor settings
        $elementor_db_setting[$key] = $value;
        
    }

    // update it on database
	return update_post_meta($id, '_elementor_data', json_encode($elementor_db_setting, JSON_UNESCAPED_UNICODE));
}

?>