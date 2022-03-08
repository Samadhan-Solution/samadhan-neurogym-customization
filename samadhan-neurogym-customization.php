<?php
/*
Plugin Name: Samadhan Neurogym Customization
Description: Simple Neurogym Customization
Author URI: http://www.samadhan.com.bd/
Author: Samadhan
Plugin URI: http://www.samadhan.com.bd/
Version: 1.0.2
*/

function samadhan_css_and_js_load(){
    $path=plugin_dir_url(__FILE__);
    wp_enqueue_style('smdn-css',$path.'apps/css/style.css');
    wp_enqueue_script('smdn-js',$path.'apps/js/custom.js');
}


add_filter('gettext','smdn_gettext',10,3);
function smdn_gettext( $translation, $text, $domain){
    if ( 'buddyboss' === $domain ) {
        if('Videos'=== $text )
        {
            $translation = 'Member Videos';
        }
    }

    return $translation;
}

function buddypress_custom_group_tab() {
    if ( ! function_exists( 'bp_core_new_subnav_item' ) ||
        ! function_exists( 'bp_is_single_item' ) ||
        ! function_exists( 'bp_is_groups_component' ) ||
        ! function_exists( 'bp_get_group_permalink' ) ||
        empty( get_current_user_id() ) ) {
        return;
    }

    // Check if we are on group page.
    if ( bp_is_groups_component() && bp_is_single_item() ) {

        global $bp;
        // Get current group page link.
        $group_link = bp_get_group_permalink( $bp->groups->current_group );
        //$group_link = home_url('/group-videos');

        // Tab args.
        $tab_args = array(
            'name'                => esc_html__( 'Group Videos', 'default' ),
            'slug'                => 'group-videos',
            'screen_function'     => 'smdn_group_custom_tab_screen',
            'position'            => 60,
            'parent_url'          => $group_link,
            'parent_slug'         => $bp->groups->current_group->slug,
            'default_subnav_slug' => 'group-videos',
            'item_css_id'         => 'group-videos',
        );

        // Add sub-tab.
        bp_core_new_subnav_item( $tab_args, 'groups' );
    }
}

add_action( 'bp_setup_nav', 'buddypress_custom_group_tab' );

/**
 * Set template for new tab.
 */
function smdn_group_custom_tab_screen() {

    // Add title and content here - last is to call the members plugin.php template.
    add_action( 'bp_template_title', 'smdn_custom_group_tab_title' );
    add_action( 'bp_template_content', 'smdn_custom_group_tab_content' );
    bp_core_load_template( 'buddypress/members/single/plugins' );

}

/*Set title for custom tab.*/
function smdn_custom_group_tab_title() {
    echo esc_html__( 'Group Videos', 'default_content' );
}

/*Display content of custom tab.*/
function smdn_custom_group_tab_content() {
    $group_id = bp_get_current_group_id();
    $get_templates_ids=get_template_by_group_id($group_id);
    $templated='';
    foreach ($get_templates_ids as $template){
        $template_id=$template->post_id;
        $templated .=do_shortcode('[elementor-template id="'.$template_id.'"]')."<br/>";
    }
    echo $templated;

}



//Register Meta box
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'smdn-template-id', 'Group Name', 'smdn_get_template', 'elementor_library', 'side' );
} );

//Meta callback function
function smdn_get_template( $post ) {
    samadhan_css_and_js_load();
    $get_group_ids = get_post_meta( $post->ID, 'smdn_groupids_'.$post->ID, true );
    //$get_group_ids=serialize($get_group_ids);


    $group_ids=BP_Groups_Group::get_group_type_ids();
    $option_name='';
    foreach ($group_ids['all'] as $group_id){
        $checked='';
        foreach ($get_group_ids[0] as $get_group_id){

            if($get_group_id==$group_id){
                $checked .='checked';
            }else{
                $checked .='';
            }
        }

        $group = groups_get_group( $group_id );
        $group_name = bp_get_group_name( $group );
        $option_name .= '<li><input type="checkbox" '.$checked.' name="smdn_groupids[]" value="'.$group_id.'"/>'.$group_name.'</li>';
      }

    ?>
    <div id="smdn-list" class="smdn-check-list" tabindex="100">
        <span class="anchor">Select Groups</span>
        <ul class="items">
            <?php echo $option_name?>
        </ul>
    </div>
    <?php
}

//save meta value with save post hook
add_action( 'save_post', function( $post_id ) {

    if ( isset( $_POST['post_type'] ) && isset( $_POST['smdn_groupids'] ) && 'elementor_library' === $_POST['post_type'] ) {

        $group_ids=$_POST['smdn_groupids'];
        update_post_meta( $post_id, 'smdn_groupids_'.$post_id, array($group_ids));
    }
} );

function get_template_by_group_id($group_id){
    global $wpdb;
    $search='%s:1:"'.$group_id.'"%';
    $query="SELECT post_id FROM {$wpdb->prefix}postmeta where meta_key like '%smdn_groupids%' and meta_value like '$search' ";
    $results=$wpdb->get_results($query);
    return $results;
}
