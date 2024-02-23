<?php if (!defined( 'ABSPATH' )) exit;

// Get current ID of post/page, etc
if( !function_exists( 'tripgo_get_current_id' )):
	function tripgo_get_current_id(){
	    
	    $current_page_id = '';
	    // Get The Page ID You Need
	    
	    if(class_exists("woocommerce")) {
	        if( is_shop() ){ ///|| is_product_category() || is_product_tag()) {
	            $current_page_id  =  get_option ( 'woocommerce_shop_page_id' );
	        }elseif(is_cart()) {
	            $current_page_id  =  get_option ( 'woocommerce_cart_page_id' );
	        }elseif(is_checkout()){
	            $current_page_id  =  get_option ( 'woocommerce_checkout_page_id' );
	        }elseif(is_account_page()){
	            $current_page_id  =  get_option ( 'woocommerce_myaccount_page_id' );
	        }elseif(is_view_order_page()){
	            $current_page_id  = get_option ( 'woocommerce_view_order_page_id' );
	        }
	    }
	    if($current_page_id=='') {
	        if ( is_home () && is_front_page () ) {
	            $current_page_id = '';
	        } elseif ( is_home () ) {
	            $current_page_id = get_option ( 'page_for_posts' );
	        } elseif ( is_search () || is_category () || is_tag () || is_tax () || is_archive() ) {
	            $current_page_id = '';
	        } elseif ( !is_404 () ) {
	           $current_page_id = get_the_id();
	        } 
	    }

	    return $current_page_id;
	}
endif;



if (!function_exists('tripgo_is_elementor_active')) {
    function tripgo_is_elementor_active(){
        return did_action( 'elementor/loaded' );
    }
}

if (!function_exists('tripgo_is_woo_active')) {
    function tripgo_is_woo_active(){
        return class_exists('woocommerce');    
    }
}

if (!function_exists('tripgo_is_blog_archive')) {
    function tripgo_is_blog_archive() {
        return (is_home() && is_front_page()) || is_archive() || is_category() || is_tag() || is_home();
    }
}



/* Get ID from Slug of Header Footer Builder - Post Type */
function tripgo_get_id_by_slug( $page_slug ) {
    $page = get_page_by_path( $page_slug, OBJECT, 'ova_framework_hf_el' ) ;
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}


function tripgo_custom_text ($content = "",$limit = 15) {

    $content = explode(' ', $content, $limit);

    if (count($content)>=$limit) {
        array_pop($content);
        $content = implode(" ",$content).'...';
    } else {
        $content = implode(" ",$content);
    }

    $content = preg_replace('`[[^]]*]`','',$content);
    
    return strip_tags( $content );
}



/**
 * Google Font sanitization
 *
 * @param  string   JSON string to be sanitized
 * @return string   Sanitized input
 */
if ( ! function_exists( 'tripgo_google_font_sanitization' ) ) {
    function tripgo_google_font_sanitization( $input ) {
        $val =  json_decode( $input, true );
        if( is_array( $val ) ) {
            foreach ( $val as $key => $value ) {
                $val[$key] = sanitize_text_field( $value );
            }
            $input = json_encode( $val );
        }
        else {
            $input = json_encode( sanitize_text_field( $val ) );
        }
        return $input;
    }
}


/* Default Primary Font in Customize */
if ( ! function_exists( 'tripgo_default_primary_font' ) ) {
    function tripgo_default_primary_font() {
        $customizer_defaults = json_encode(
            array(
                'font' => 'HK Grotesk',
                'regularweight' => '300,400,500,600,700,800,900',
                'category' => 'serif'
            )
        );

        return $customizer_defaults;
    }
}

if ( ! function_exists( 'tripgo_woo_sidebar' ) ) {
    function tripgo_woo_sidebar(){
        if( class_exists('woocommerce') && is_product() ){
            return get_theme_mod( 'woo_product_layout', 'woo_layout_1c' );
        }else{
            return get_theme_mod( 'woo_archive_layout', 'woo_layout_1c' );
        }
    }
}

if( !function_exists( 'tripgo_blog_show_media' ) ){
    function tripgo_blog_show_media(){
        $show_media = get_theme_mod( 'blog_archive_show_media', 'yes' );
        return isset( $_GET['show_media'] ) ? sanitize_text_field( $_GET['show_media'] ) : $show_media;
    }
}

if( !function_exists( 'tripgo_blog_show_title' ) ){
    function tripgo_blog_show_title(){
        $show_title = get_theme_mod( 'blog_archive_show_title', 'yes' );
        return isset( $_GET['show_title'] ) ? sanitize_text_field( $_GET['show_title'] ) : $show_title;
    }
}

if( !function_exists( 'tripgo_blog_show_date' ) ){
    function tripgo_blog_show_date(){
        $show_date = get_theme_mod( 'blog_archive_show_date', 'yes' );
        return isset( $_GET['show_date'] ) ? sanitize_text_field( $_GET['show_date'] ) : $show_date;
    }
}

if( !function_exists( 'tripgo_blog_show_cat' ) ){
    function tripgo_blog_show_cat(){
        $show_cat = get_theme_mod( 'blog_archive_show_cat', 'yes' );
        return isset( $_GET['show_cat'] ) ? sanitize_text_field( $_GET['show_cat'] ) : $show_cat;
    }
}

if( !function_exists( 'tripgo_blog_show_author' ) ){
    function tripgo_blog_show_author(){
        $show_author = get_theme_mod( 'blog_archive_show_author', 'yes' );
        return isset( $_GET['show_author'] ) ? sanitize_text_field( $_GET['show_author'] ) : $show_author;
    }
}

if( !function_exists( 'tripgo_blog_show_comment' ) ){
    function tripgo_blog_show_comment(){
        $show_comment = get_theme_mod( 'blog_archive_show_comment', 'yes' );
        return isset( $_GET['show_comment'] ) ? sanitize_text_field( $_GET['show_comment'] ) : $show_comment;
    }
}

if( !function_exists( 'tripgo_blog_show_excerpt' ) ){
    function tripgo_blog_show_excerpt(){
        $show_excerpt = get_theme_mod( 'blog_archive_show_excerpt', 'yes' );
        return isset( $_GET['show_excerpt'] ) ? sanitize_text_field( $_GET['show_excerpt'] ) : $show_excerpt;
    }
}


if( !function_exists( 'tripgo_blog_show_readmore' ) ){
    function tripgo_blog_show_readmore(){
        $show_readmore = get_theme_mod( 'blog_archive_show_readmore', 'yes' );
        return isset( $_GET['show_readmore'] ) ? sanitize_text_field( $_GET['show_readmore'] ) : $show_readmore;
    }
}



if( !function_exists( 'tripgo_post_show_media' ) ){
    function tripgo_post_show_media(){
        $show_media = get_theme_mod( 'blog_single_show_media', 'yes' );
        return isset( $_GET['show_media'] ) ? sanitize_text_field( $_GET['show_media'] ) : $show_media;
    }
}

if( !function_exists( 'tripgo_post_show_title' ) ){
    function tripgo_post_show_title(){
        $show_title = get_theme_mod( 'blog_single_show_title', 'yes' );
        return isset( $_GET['show_title'] ) ? sanitize_text_field( $_GET['show_title'] ) : $show_title;
    }
}

if( !function_exists( 'tripgo_post_show_date' ) ){
    function tripgo_post_show_date(){
        $show_date = get_theme_mod( 'blog_single_show_date', 'yes' );
        return isset( $_GET['show_date'] ) ? sanitize_text_field( $_GET['show_date'] ) : $show_date;
    }
}

if( !function_exists( 'tripgo_post_show_cat' ) ){
    function tripgo_post_show_cat(){
        $show_cat = get_theme_mod( 'blog_single_show_cat', 'yes' );
        return isset( $_GET['show_cat'] ) ? sanitize_text_field( $_GET['show_cat'] ) : $show_cat;
    }
}

if( !function_exists( 'tripgo_post_show_author' ) ){
    function tripgo_post_show_author(){
        $show_author = get_theme_mod( 'blog_single_show_author', 'yes' );
        return isset( $_GET['show_author'] ) ? sanitize_text_field( $_GET['show_author'] ) : $show_author;
    }
}

if( !function_exists( 'tripgo_post_show_comment' ) ){
    function tripgo_post_show_comment(){
        $show_comment = get_theme_mod( 'blog_single_show_comment', 'yes' );
        return isset( $_GET['show_comment'] ) ? sanitize_text_field( $_GET['show_comment'] ) : $show_comment;
    }
}

if( !function_exists( 'tripgo_post_show_tag' ) ){
    function tripgo_post_show_tag(){
        $show_tag = get_theme_mod( 'blog_single_show_tag', 'yes' );
        return isset( $_GET['show_tag'] ) ? sanitize_text_field( $_GET['show_tag'] ) : $show_tag;
    }
}

if( !function_exists( 'tripgo_post_show_share_social_icon' ) ){
    function tripgo_post_show_share_social_icon(){
        $show_share_social_icon = get_theme_mod( 'blog_single_show_share_social_icon', 'yes' );
        return isset( $_GET['show_share_social_icon'] ) ? sanitize_text_field( $_GET['show_share_social_icon'] ) : $show_share_social_icon;
    }
}

if( !function_exists( 'tripgo_post_show_next_prev_post' ) ){
    function tripgo_post_show_next_prev_post(){
        $show_next_prev_post = get_theme_mod( 'blog_single_show_next_prev_post', 'yes' );
        return isset( $_GET['show_next_prev_post'] ) ? sanitize_text_field( $_GET['show_next_prev_post'] ) : $show_next_prev_post;
    }
}

/* Get Gallery ids Product */
if ( !function_exists( 'tripgo_get_gallery_ids' ) ) {
    function tripgo_get_gallery_ids( $product_id ) {
        $product = wc_get_product( $product_id );

        if ( $product ) {
            $arr_image_ids = array();

            $product_image_id = $product->get_image_id();
            if ( $product_image_id ) {
                array_push( $arr_image_ids, $product_image_id );
            }

            $product_gallery_ids = $product->get_gallery_image_ids();
            if ( $product_gallery_ids && is_array( $product_gallery_ids ) ) {
                $arr_image_ids = array_merge( $arr_image_ids, $product_gallery_ids );
            }

            return $arr_image_ids;
        }
        return false;
    }
}

/* Get Price Product */
if ( !function_exists( 'tripgo_get_price_product' ) ) {
    function tripgo_get_price_product( $product_id ) {
        $product        = wc_get_product( $product_id );
        $regular_price  = $sale_price = 0;

        if ( $product->is_on_sale() && $product->get_sale_price() ) {
            $regular_price  = $product->get_sale_price();
            $sale_price     = $product->get_regular_price();
        } else {
            $regular_price = $product->get_regular_price();
        }

        $result = array(
            'regular_price' => $regular_price,
            'sale_price'    => $sale_price,
        );

        return $result;
    }
}