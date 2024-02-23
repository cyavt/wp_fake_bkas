<?php if ( !defined( 'ABSPATH' ) ) exit();

class Ovabrw_Setting_Tab extends WC_Settings_Page {

    /**
     * Constructor
     */
    public function __construct() {

        $this->id    = 'ova_brw';

        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
        add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
        add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

        parent::__construct();

    }

    /**
     * Add plugin options tab
     *
     * @return array
     */
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs[$this->id] = esc_html__( 'Booking Tours', 'ova-brw' );
        return $settings_tabs;
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function get_sections() {

        $sections = array(
            ''                          => esc_html__( 'General', 'ova-brw' ),
            'detail_rental_setting'     => esc_html__( 'Product Details', 'ova-brw' ),
            'booking_form'              => esc_html__( 'Booking Form', 'ova-brw' ),
            'request_booking_form'      => esc_html__( 'Request Booking Form', 'ova-brw' ),
            'cancel_setting'            => esc_html__( 'Cancel Order', 'ova-brw' ),
            'reminder_setting'          => esc_html__( 'Reminder', 'ova-brw' ),
            'manage_order'              => esc_html__( 'Manage Order', 'ova-brw' ),
        );

        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }


    /**
     * Output the settings
     */
    public function output() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::output_fields( $settings );
    }


    /**
     * Save settings
     */
    public function save() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::save_fields( $settings );
    }


    /**
     * Get sections
     *
     * @return array
     */
    public function get_settings( $section = null ) {

        if( apply_filters( 'ovabrw_add_custom_tax_by_code', false ) == true ){
            $total_custom_tax = array(
                        'name' => esc_html__( 'Total Custom Taxonomy', 'ova-brw' ),
                        'type' => 'number',
                        'default' => 0,
                        'desc' => esc_html__('Read documentation to know more', 'ova-brw'),
                        'id'   => 'ova_brw_number_taxonomy'
                    );    
        }else{
            $total_custom_tax = array();
        }
        

        switch( $section ){

            case '' :

                $settings = array(

                    array(
                        'title' => esc_html__( 'General', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_general',
                    ),
                   
                    $total_custom_tax,
                    array(
                        'name'      => esc_html__( 'Date Format', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => array(
                            'd-m-Y' => esc_html__( 'd-m-Y', 'ova-brw' ).' ('.wp_date( 'd-m-Y', current_time('timestamp') ).')',
                            'm/d/Y' => esc_html__( 'm/d/Y', 'ova-brw' ).' ('.wp_date( 'm/d/Y', current_time('timestamp') ).')',
                            'Y/m/d' => esc_html__( 'Y/m/d', 'ova-brw' ).' ('.wp_date( 'Y/m/d', current_time('timestamp') ).')',
                            'Y-m-d' => esc_html__( 'Y-m-d', 'ova-brw' ).' ('.wp_date( 'Y-m-d', current_time('timestamp') ).')',
                        ),
                        'default'   => 'd-m-Y',
                        'id'        => 'ova_brw_booking_form_date_format'
                    ),

                    array(
                        'name'      => esc_html__( 'Language', 'ova-brw' ),
                        'type'      => 'select',
                        'options' => [
                            'ar' => esc_html__('Arabic', 'ova-brw'),
                            'az' => esc_html__('Azerbaijanian (Azeri)', 'ova-brw'),
                            'bg' => esc_html__('Bulgarian','ova-brw'),
                            'bs' => esc_html__('Bosanski','ova-brw'),
                            'ca' => esc_html__('Català','ova-brw'),
                            'ch' => esc_html__('Simplified Chinese','ova-brw'),
                            'cs' => esc_html__('Čeština','ova-brw'),
                            'da' => esc_html__('Dansk','ova-brw'),
                            'de' => esc_html__('German','ova-brw'),
                            'el' => esc_html__('Ελληνικά','ova-brw'),
                            'en' => esc_html__('English','ova-brw'),
                            'en-GB' => esc_html__('English (British) ','ova-brw'),
                            'es' => esc_html__('Spanish','ova-brw'),
                            'et' => esc_html__('Eesti','ova-brw'),
                            'eu' => esc_html__('Euskara','ova-brw'),
                            'fa' => esc_html__('Persian','ova-brw'),
                            'fi' => esc_html__('Finnish (Suomi)','ova-brw'),
                            'fr' => esc_html__('French','ova-brw'),
                            'gl' => esc_html__('Galego','ova-brw'),
                            'he' => esc_html__('Hebrew (עברית)','ova-brw'),
                            'hr' => esc_html__('Hrvatski','ova-brw'),
                            'hu' => esc_html__('Hungarian','ova-brw'),
                            'id' => esc_html__('Indonesian','ova-brw'),
                            'it' => esc_html__('Italian','ova-brw'),
                            'ja' => esc_html__('Japanese','ova-brw'),
                            'ko' => esc_html__('Korean (한국어)','ova-brw'),
                            'kr' => esc_html__('Korean','ova-brw'),
                            'lt' => esc_html__('Lithuanian (lietuvių) ','ova-brw'),
                            'lv' => esc_html__('Latvian (Latviešu)','ova-brw'),
                            'mk' => esc_html__('Macedonian (Македонски)','ova-brw'),
                            'mn' => esc_html__('Mongolian (Монгол)','ova-brw'),
                            'nl' => esc_html__('Dutch','ova-brw'),
                            'no' => esc_html__('Norwegian','ova-brw'),
                            'pl' => esc_html__('Polish','ova-brw'),
                            'pt' => esc_html__('Portuguese','ova-brw'),
                            'pt-BR' => esc_html__('Português(Brasil)','ova-brw'),
                            'ro' => esc_html__('Romanian','ova-brw'),
                            'ru' => esc_html__('Russian','ova-brw'),
                            'se' => esc_html__('Swedish','ova-brw'),
                            'sk' => esc_html__('Slovenčina','ova-brw'),
                            'sl' => esc_html__('Slovenščina','ova-brw'),
                            'sq' => esc_html__('Albanian (Shqip)','ova-brw'),
                            'sr' => esc_html__('Serbian Cyrillic (Српски)','ova-brw'),
                            'sr-YU' => esc_html__('Serbian (Srpski)','ova-brw'),
                            'sv' => esc_html__('Svenska','ova-brw'),
                            'th' => esc_html__('Thai','ova-brw'),
                            'tr' => esc_html__('Turkish','ova-brw'),
                            'uk' => esc_html__('Ukrainian','ova-brw'),
                            'vi' => esc_html__('Vietnamese','ova-brw'),
                            'zh' => esc_html__('Simplified Chinese (简体中文)','ova-brw'),
                            'zh-TW' => esc_html__('Traditional Chinese (繁體中文)','ova-brw'),
                        ],
                        'default'   => 'en',
                        'desc_tip'  => esc_html__('Display in Calendar','ova-brw'),
                        'id'        => 'ova_brw_calendar_language_general'
                    ),

                    array(
                        'name'      => esc_html__( 'Unavailable Date for booking', 'ova-brw' ),
                        'type'      => 'text',
                        'desc_tip'  => esc_html__('0: Sunday, 1: Monday, 2: Tuesday, 3: Wednesday, 4: Thursday, 5: Friday, 6: Saturday . Example: 0,6','ova-brw'),
                        'id'        => 'ova_brw_calendar_disable_week_day'
                    ),

                    array(
                        'name'      => esc_html__( 'The day that each week begins', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            '1' => esc_html__('Monday', 'ova-brw'),
                            '2' => esc_html__('Tuesday', 'ova-brw'),
                            '3' => esc_html__('Wednesday', 'ova-brw'),
                            '4' => esc_html__('Thursday', 'ova-brw'),
                            '5' => esc_html__('Friday', 'ova-brw'),
                            '6' => esc_html__('Saturday ', 'ova-brw'),
                            '0' => esc_html__('Sunday', 'ova-brw'),
                        ],
                        'default'   => '0',
                        'desc_tip'  => esc_html__('0: Sunday, 1: Monday, 2: Tuesday, 3: Wednesday, 4: Thursday, 5: Friday, 6: Saturday . Example: 0','ova-brw'),
                        'id'        => 'ova_brw_calendar_first_day'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Taxonomy depend Category', 'ova-brw' ),
                        'type'      => 'select',
                        'default'   => 'no',
                        'desc_tip'  => esc_html__('Taxonomies displayed by Product Category','ova-brw'),
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'        => 'ova_brw_search_show_tax_depend_cat',
                    ),

                    array(
                        'name'      => esc_html__( 'Google Key Map', 'ova-brw' ),
                        'type'      => 'text',
                        'default'   => '',
                        'desc_tip'  => esc_html__('You can get here: https://developers.google.com/maps/documentation/javascript/get-api-key','ova-brw'),
                        'id'        => 'ova_brw_google_key_map'
                    ),

                    array(
                        'name'      => esc_html__( 'Zoom', 'ova-brw' ),
                        'type'      => 'text',
                        'class'     => 'ova_brw_zoom_map_default',
                        'default'   => 17,
                        'id'        => 'ova_brw_zoom_map_default'
                    ),

                    array(
                        'name'      => esc_html__( 'Latitude Map default', 'ova-brw' ),
                        'type'      => 'text',
                        'class'     => 'ova_brw_latitude_map_default',
                        'default'   => 39.177972,
                        'desc_tip'  => esc_html__('The default latitude of map when the event do not exist','ova-brw'),
                        'id'        => 'ova_brw_latitude_map_default'
                    ),

                    array(
                        'name'      => esc_html__( 'Longitude Map default', 'ova-brw' ),
                        'type'      => 'text',
                        'class'     => 'ova_brw_longitude_map_default',
                        'default'   => -100.363750,
                        'desc_tip'  => esc_html__('The default longitude of map when the event do not exist','ova-brw'),
                        'id'        => 'ova_brw_longitude_map_default'
                    ),

                    'section_end' => array(
                        'type'  => 'sectionend',
                        'id'    => 'ova_brw_end_general'
                    )
                );

                break;

            case 'detail_rental_setting' :

                // Get templates from elementor
                $templates = get_posts( array('post_type' => 'elementor_library', 'meta_key' => '_elementor_template_type', 'meta_value' => 'page' ) );
                
                $list_templates = array( 'default' => 'Default' );

                if( ! empty( $templates ) ) {
                    foreach( $templates as $template ) {
                        $id_template    = $template->ID;
                        $title_template = $template->post_title;
                        $list_templates[$id_template] = $title_template;
                    }
                }

                $settings = array(

                    array(
                        'title' => esc_html__( 'Product Details', 'ova-brw' ),
                        'type'  => 'title',
                        'desc'  => '',
                        'id'    => 'ova_brw_style_template',
                    ),

                    array(
                        'name'      => esc_html__( 'Product Templates', 'ova-brw' ),
                        'type'      => 'select',
                        'desc_tip'  => esc_html__( 'Default or Other (made in Templates of Elementor )', 'ova-brw' ),
                        'options'   => $list_templates,
                        'default'   => 'default',
                        'id'        => 'ova_brw_template_elementor_template'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Booking Form', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_template_show_booking_form'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Request Booking Form', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_template_show_request_booking'
                    ),

                    'section_end' => array(
                        'type'  => 'sectionend',
                        'id'    => 'ova_brw_end_style_template'
                    )
                );            

                break;

            case 'booking_form':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Booking Form', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_booking_form',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Resources and Services in Cart, Checkout, Order Detail', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'no',
                        'id'        => 'ova_brw_booking_form_show_extra'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Amount of insurance', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_booking_form_show_amount_insurance'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Number of Tours Available', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_booking_form_show_quantity_availables'
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_end_booking_form',
                    ),

                );
                break;

            case 'request_booking_form':

                $all_pages = get_pages();
                $list_page[''] = esc_html__( 'Select Page', 'ova-brw' );
                if( ! empty( $all_pages ) ) {
                    foreach( $all_pages as $page ) {
                    $id_page = $page->ID;
                    $title_page = $page->post_title;
                    $link_page = get_page_link( $id_page );

                    $list_page[$link_page] = $title_page;
                    }
                }

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Request Booking Form in Tab', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_request_booking_form',
                    ),

                    array(
                        'name'      => esc_html__( 'Thank Page', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => $list_page,
                        'id'        => 'ova_brw_request_booking_form_thank_page',
                    ),

                    array(
                        'name'      => esc_html__( 'Error Page', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => $list_page,
                        'id'        => 'ova_brw_request_booking_form_error_page',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Phone', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_request_booking_form_show_number'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Address', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_request_booking_form_show_address'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Date', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_request_booking_form_show_dates'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Extra Services', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_request_booking_form_show_extra_service'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Service', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_request_booking_form_show_service'
                    ),

                    array(
                        'name'      => esc_html__( 'Show Extra Info', 'ova-brw' ),
                        'type'      => 'select',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'default'   => 'yes',
                        'id'        => 'ova_brw_request_booking_form_show_extra_info'
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_request_booking_form',
                    ),
                  
                    array(
                        'title' => esc_html__( 'Email Settings', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_request_booking_mail_setting',
                    ),

                    array(
                        'name'      => esc_html__( 'Subject', 'ova-brw' ),
                        'type'      => 'text',
                        'desc_tip'  => esc_html__( 'The subject displays in the email list', 'ova-brw' ),
                        'default'   => esc_html__( 'Request For Booking', 'ova-brw' ) ,
                        'id'        => 'ova_brw_request_booking_mail_subject',
                    ),

                    array(
                        'name'      => esc_html__( 'From name', 'ova-brw' ),
                        'type'      => 'text',
                        'desc_tip'  => esc_html__( 'The subject displays in mail detail', 'ova-brw' ),
                        'default'   => esc_html__( 'Request For Booking', 'ova-brw' ) ,
                        'id'        => 'ova_brw_request_booking_mail_from_name',
                    ),

                    array(
                        'name'      => esc_html__( 'Send from email', 'ova-brw' ),
                        'type'      => 'text',
                        'desc_tip'  => esc_html__( 'The customer will know them to receive mail from which email address is', 'ova-brw' ),
                        'default'   => get_option( 'admin_email' ),
                        'id'        => 'ova_brw_request_booking_mail_from_email',
                    ),

                    array(
                        'name'          => esc_html__( 'Cc', 'ova-brw' ),
                        'type'          => 'text',
                        'desc_tip'      => esc_html__( 'Emails separated by "|".', 'ova-brw' ),
                        'default'       => '',
                        'placeholder'   => 'email1@gmail.com|email2@gmail.com',
                        'id'            => 'ova_brw_request_booking_mail_cc_email',
                    ),

                    array(
                        'name'      => esc_html__( 'Email Content', 'ova-brw' ),
                        'type'      => 'textarea',
                        'desc_tip'  => esc_html__( 'Use tags to generate email template. For example: You booked the tour: [product-name] from [check-in] to [check-out]. [order_details]', 'ova-brw' ),
                        'default'   => esc_html__( 'You booked the tour: [product-name] from [check-in] to [check-out]. [order_details]', 'ova-brw' ),
                        'id'        => 'ova_brw_request_booking_mail_content',
                        'custom_attributes' => [
                            'rows' => '5',
                        ],
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_request_booking_mail_setting',
                    ),
                );
                break;
        
            case 'cancel_setting':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Cancel Order', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_cancel_setting',
                    ),

                    array(
                        'name'      => esc_html__( 'Cancellation is accepted before x hours', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => '0',
                        'id'        => 'ova_brw_cancel_before_x_hours',
                    ),

                    array(
                        'name'      => esc_html__( 'Cancellation is accepted if the total order is less than x amount', 'ova-brw' ),
                        'type'      => 'text',
                        'default'   => 1,
                        'id'        => 'ova_brw_cancel_condition_total_order',
                    ),
                     array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_end_cancel_setting',
                    ),
                );
                break;

            case 'reminder_setting':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Reminder', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_reminder_setting',
                    ),

                     array(
                        'name'      => esc_html__( 'Enable', 'ova-brw' ),
                        'type'      => 'select',
                        'desc_tip'  => esc_html__( 'Allow to send mail to customer', 'ova-brw' ),
                        'default'   => 'yes',
                        'options'   => [
                            'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                            'no'    => esc_html__( 'No', 'ova-brw' ),
                        ],
                        'id'        => 'remind_mail_enable',
                    ),

                    array(
                        'name'      => esc_html__( 'Before x day', 'ova-brw' ),
                        'type'      => 'text',
                        'default'   => 1,
                        'id'        => 'remind_mail_before_xday',
                    ),

                    array(
                        'name'      => esc_html__( 'Send a mail every x seconds', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 86400,
                        'id'        => 'remind_mail_send_per_seconds',
                    ),

                    array(
                        'name'      => esc_html__( 'Subject', 'ova-brw' ),
                        'type'      => 'text',
                        'desc_tip'  => esc_html__( 'The subject displays in the email list', 'ova-brw' ),
                        'default'   => esc_html__( 'Remind Check-in Date', 'ova-brw' ) ,
                        'id'        => 'reminder_mail_subject',
                    ),

                    array(
                        'name'      => esc_html__( 'From name', 'ova-brw' ),
                        'type'      => 'text',
                        'desc_tip'  => esc_html__( 'The subject displays in mail detail', 'ova-brw' ),
                        'default'   => esc_html__( 'Remind Check-in Date', 'ova-brw' ) ,
                        'id'        => 'reminder_mail_from_name',
                    ),

                    array(
                        'name'      => esc_html__( 'Send from email', 'ova-brw' ),
                        'type'      => 'text',
                        'desc_tip'  => esc_html__( 'The customer will know them to receive mail from which email address is', 'ova-brw' ),
                        'default'   => get_option( 'admin_email' ),
                        'id'        => 'reminder_mail_from_email',
                    ),

                    array(
                        'name'      => esc_html__( 'Email Content', 'ova-brw' ),
                        'type'      => 'textarea',
                        'desc_tip'  => esc_html__( 'Use tags to generate email template. For example: You booked the tour: [product-name] at [check-in]', 'ova-brw' ),
                        'default'   => esc_html__( 'You booked the tour: [product-name] at [check-in]', 'ova-brw' ),
                        'id'        => 'reminder_mail_content',
                        'custom_attributes' => [
                            'rows' => '5',
                        ],
                    ),
                    
                     array(
                        'type' => 'sectionend',
                        'id'   => 'reminder_end_setting',
                    ),
                );
                break;

            case 'manage_order':

                $settings = array(
                   
                    array(
                        'title' => esc_html__( 'Admin Settings', 'ova-brw' ),
                        'type'  => 'title',
                        'id'    => 'ova_brw_admin_manage_order_setting',
                        'desc'  => esc_html__( 'The fields are sorted ascending. To hide the field, enter the number: 0 or empty', 'ova-brw' ),
                    ),

                    array(
                        'name'      => esc_html__( 'Show Order ID', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 1,
                        'id'        => 'admin_manage_order_show_id',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Customer', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 2,
                        'id'        => 'admin_manage_order_show_customer',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Time', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 3,
                        'id'        => 'admin_manage_order_show_time',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Deposit Status', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 4,
                        'id'        => 'admin_manage_order_show_deposit',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Insurance Status', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 5,
                        'id'        => 'admin_manage_order_show_insurance',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Product', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 6,
                        'id'        => 'admin_manage_order_show_product',
                    ),

                    array(
                        'name'      => esc_html__( 'Show Order Status', 'ova-brw' ),
                        'type'      => 'number',
                        'default'   => 7,
                        'id'        => 'admin_manage_order_show_order_status',
                    ),

                    array(
                        'type' => 'sectionend',
                        'id'   => 'ova_brw_admin_manage_order_setting',
                    ),  
                );

                break;
        }

        return apply_filters( 'wc_settings_tab_ova-brw_settings', $settings, $section );
    }
}
return new Ovabrw_Setting_Tab();
