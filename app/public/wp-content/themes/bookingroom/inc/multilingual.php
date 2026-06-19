<?php
/**
 * Multilingual System (No Plugin)
 * Handles Language State, Theme Strings, and Custom Fields (Title, Content, Excerpt)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ==========================================
// 1. LANGUAGE STATE & COOKIE MANAGEMENT
// ==========================================
add_action( 'init', 'bookingroom_language_init', 1 );
function bookingroom_language_init() {
    $lang = 'vi'; // Default

    // If user clicks the language switcher (?lang=en or ?lang=vi)
    if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], array( 'vi', 'en' ) ) ) {
        $lang = sanitize_text_field( $_GET['lang'] );
        // Set cookie for 30 days
        setcookie( 'booking_lang', $lang, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
        
        // Optional: Redirect to remove the ?lang parameter for clean URLs
        // $redirect_url = remove_query_arg( 'lang' );
        // wp_safe_redirect( $redirect_url );
        // exit;
    } else {
        // Read cookie if no URL parameter
        if ( isset( $_COOKIE['booking_lang'] ) && in_array( $_COOKIE['booking_lang'], array( 'vi', 'en' ) ) ) {
            $lang = sanitize_text_field( $_COOKIE['booking_lang'] );
        }
    }

    if ( ! defined( 'SITE_LANG' ) ) {
        define( 'SITE_LANG', $lang );
    }
}

// ==========================================
// 2. THEME STRING HELPER
// ==========================================
/**
 * Helper function to translate static theme strings.
 * Usage: echo t('Tiếng Việt', 'English');
 */
function t( $vi_text, $en_text ) {
    if ( defined( 'SITE_LANG' ) && SITE_LANG === 'en' ) {
        return $en_text;
    }
    return $vi_text;
}

// ==========================================
// 3. URL HELPER FOR LANGUAGE SWITCHER
// ==========================================
function bookingroom_get_lang_switch_url( $target_lang ) {
    // Just append ?lang=target_lang to the current URL
    global $wp;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );
    
    // If it's the home page, $wp->request might be empty
    if ( empty( $wp->request ) ) {
        $current_url = home_url( '/' );
    }
    
    return add_query_arg( 'lang', $target_lang, $current_url );
}

// ==========================================
// 4. DYNAMIC CONTENT TRANSLATION (METABOXES)
// ==========================================
add_action( 'add_meta_boxes', 'bookingroom_lang_meta_boxes' );
function bookingroom_lang_meta_boxes() {
    $post_types = array( 'post', 'page', 'room', 'hotel', 'destination' );
    foreach ( $post_types as $pt ) {
        add_meta_box(
            'bookingroom_lang_metabox',
            'English Translation',
            'bookingroom_lang_metabox_html',
            $pt,
            'normal',
            'high'
        );
    }
}

function bookingroom_lang_metabox_html( $post ) {
    $title_en = get_post_meta( $post->ID, '_title_en', true );
    $content_en = get_post_meta( $post->ID, '_content_en', true );
    $excerpt_en = get_post_meta( $post->ID, '_excerpt_en', true );
    
    wp_nonce_field( 'bookingroom_lang_save', 'bookingroom_lang_nonce' );
    
    echo '<p><label for="title_en"><strong>English Title</strong></label><br>';
    echo '<input type="text" id="title_en" name="title_en" value="' . esc_attr( $title_en ) . '" style="width:100%;"></p>';
    
    echo '<p><label for="excerpt_en"><strong>English Excerpt</strong></label><br>';
    echo '<textarea id="excerpt_en" name="excerpt_en" rows="3" style="width:100%;">' . esc_textarea( $excerpt_en ) . '</textarea></p>';

    echo '<p><label for="content_en"><strong>English Content</strong></label></p>';
    wp_editor( $content_en, 'content_en', array( 'textarea_name' => 'content_en', 'textarea_rows' => 10 ) );
}

add_action( 'save_post', 'bookingroom_lang_save_postdata' );
function bookingroom_lang_save_postdata( $post_id ) {
    if ( ! isset( $_POST['bookingroom_lang_nonce'] ) || ! wp_verify_nonce( $_POST['bookingroom_lang_nonce'], 'bookingroom_lang_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    if ( isset( $_POST['title_en'] ) ) {
        update_post_meta( $post_id, '_title_en', sanitize_text_field( $_POST['title_en'] ) );
    }
    if ( isset( $_POST['excerpt_en'] ) ) {
        update_post_meta( $post_id, '_excerpt_en', sanitize_textarea_field( $_POST['excerpt_en'] ) );
    }
    if ( isset( $_POST['content_en'] ) ) {
        update_post_meta( $post_id, '_content_en', wp_kses_post( $_POST['content_en'] ) );
    }
}

// ==========================================
// 5. OVERRIDE CONTENT DISPLAY FOR FRONTEND
// ==========================================
add_filter( 'the_title', 'bookingroom_translate_title', 10, 2 );
function bookingroom_translate_title( $title, $id = null ) {
    if ( ! is_admin() && defined( 'SITE_LANG' ) && SITE_LANG === 'en' && $id ) {
        $en_title = get_post_meta( $id, '_title_en', true );
        if ( ! empty( $en_title ) ) {
            return $en_title;
        }
    }
    return $title;
}

add_filter( 'the_content', 'bookingroom_translate_content', 10 );
function bookingroom_translate_content( $content ) {
    if ( ! is_admin() && defined( 'SITE_LANG' ) && SITE_LANG === 'en' ) {
        global $post;
        if ( $post ) {
            $en_content = get_post_meta( $post->ID, '_content_en', true );
            if ( ! empty( $en_content ) ) {
                // Apply shortcodes and formatting to English content
                return wpautop( do_shortcode( $en_content ) );
            }
        }
    }
    return $content;
}

add_filter( 'the_excerpt', 'bookingroom_translate_excerpt', 10 );
function bookingroom_translate_excerpt( $excerpt ) {
    if ( ! is_admin() && defined( 'SITE_LANG' ) && SITE_LANG === 'en' ) {
        global $post;
        if ( $post ) {
            $en_excerpt = get_post_meta( $post->ID, '_excerpt_en', true );
            if ( ! empty( $en_excerpt ) ) {
                return $en_excerpt;
            }
        }
    }
    return $excerpt;
}

// ==========================================
// 6. MENU TRANSLATION
// ==========================================
add_filter( 'wp_nav_menu_args', 'bookingroom_translate_menu' );
function bookingroom_translate_menu( $args ) {
    if ( defined( 'SITE_LANG' ) && SITE_LANG === 'en' ) {
        // If an English version of the menu location is registered, use it
        $en_location = $args['theme_location'] . '-en';
        
        $locations = get_nav_menu_locations();
        if ( isset( $locations[ $en_location ] ) && $locations[ $en_location ] != 0 ) {
            $args['theme_location'] = $en_location;
        }
    }
    return $args;
}
