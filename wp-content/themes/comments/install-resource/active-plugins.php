<?php

require_once (TRIPGO_URL.'/install-resource/class-tgm-plugin-activation.php');

add_action( 'tgmpa_register', 'tripgo_register_required_plugins' );


function tripgo_register_required_plugins() {
   
    $plugins = array(

        array(
            'name'                     => esc_html__('Elementor','tripgo'),
            'slug'                     => 'elementor',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Contact Form 7','tripgo'),
            'slug'                     => 'contact-form-7',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Widget importer exporter','tripgo'),
            'slug'                     => 'widget-importer-exporter',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('One click demo import','tripgo'),
            'slug'                     => 'one-click-demo-import',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('OvaTheme Framework','tripgo'),
            'slug'                     => 'ova-framework',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-framework.zip',
            'version'                   => '1.0.0'
        ),
        array(
            'name'                     => esc_html__('Woocommerce','tripgo'),
            'slug'                     => 'woocommerce',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Travel and Tour Booking','tripgo'),
            'slug'                     => 'ova-brw',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-brw.zip',
            'version'                   => '1.1.0'
        ),
        array(
            'name'                     => esc_html__('OvaTheme Destination','tripgo'),
            'slug'                     => 'ova-destination',
            'required'                 => true,
            'source'                   => get_template_directory() . '/install-resource/plugins/ova-destination.zip',
            'version'                   => '1.0.3'
        ),
        array(
            'name'                     => esc_html__('CMB2','tripgo'),
            'slug'                     => 'cmb2',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('ReviewX','tripgo'),
            'slug'                     => 'reviewx',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('Mailchimp','tripgo'),
            'slug'                     => 'mailchimp-for-wp',
            'required'                 => true,
        ),
        array(
            'name'                     => esc_html__('YITH WooCommerce Wishlist','tripgo'),
            'slug'                     => 'yith-woocommerce-wishlist',
            'required'                 => true,
        ),
    );

   
    $config = array(
        'id'           => 'tripgo',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.

        
    );

    tripgo_tgmpa( $plugins, $config );
}

add_action( 'pt-ocdi/after_import', 'tripgo_after_import_setup' );
function tripgo_after_import_setup() {
    // Assign menus to their locations.
    $primary = get_term_by( 'name', 'Primary Menu', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
            'primary' => $primary->term_id,
        )
    );

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home 1' );
    $blog_page_id  = get_page_by_title( 'Blog' );


    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );
    

}


add_filter( 'pt-ocdi/import_files', 'tripgo_import_files' );
function tripgo_import_files() {
    return array(
        array(
            'import_file_name'             => 'Demo Import',
            'categories'                   => array( 'Category 1', 'Category 2' ),
            'local_import_file'            => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/demo-content.xml',
            'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/widgets.wie',
            'local_import_customizer_file'   => trailingslashit( get_template_directory() ) . 'install-resource/demo-import/customize.dat',
            // 'import_preview_image_url'     => 'http://demo.ovathemes.com/documentation/demo-import.jpg',
        )
    );
}

