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

    // Allow overriding via URL ?lang=en (legacy fallback)
    if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], array( 'vi', 'en' ) ) ) {
        $lang = sanitize_text_field( $_GET['lang'] );
        setcookie( 'booking_lang', $lang, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
    } else {
        // Read URL path to determine language
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $home_path = parse_url(home_url(), PHP_URL_PATH) ?: '';
        if ( $home_path ) {
            $uri = preg_replace('#^' . preg_quote($home_path, '#') . '#', '', $uri);
        }
        
        // Check if URI starts with /en/ or exactly /en
        if ( preg_match('#^/en(/|\?|$)#i', $uri) ) {
            $lang = 'en';
            // Set cookie for reference
            setcookie( 'booking_lang', 'en', time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
        } elseif ( isset( $_COOKIE['booking_lang'] ) && in_array( $_COOKIE['booking_lang'], array( 'vi', 'en' ) ) ) {
            $cookie_lang = sanitize_text_field( $_COOKIE['booking_lang'] );
            // Auto-redirect to /en/ only if on root homepage and cookie is 'en'
            if ( $cookie_lang === 'en' && ($uri === '/' || $uri === '') && ! is_admin() ) {
                wp_redirect( home_url('/en/') );
                exit;
            }
        }
    }

    if ( ! defined( 'SITE_LANG' ) ) {
        define( 'SITE_LANG', $lang );
    }
}

// ==========================================
// 1.5 REWRITE RULES FOR /en/ DIRECTORY
// ==========================================
add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'lang';
    return $vars;
});

add_filter( 'rewrite_rules_array', function( $rules ) {
    $new_rules = array();
    
    // Front page rule
    $new_rules['^en/?$'] = 'index.php?lang=en';
    
    // Duplicate existing rules with /en/ prefix
    foreach ( $rules as $key => $val ) {
        // Skip wp-json or other system endpoints if necessary
        if ( strpos( $key, 'wp-json' ) === 0 || strpos( $key, 'en/' ) === 0 ) {
            continue;
        }
        
        $new_key = 'en/' . ltrim( $key, '^' );
        $new_val = $val;
        if ( strpos( $new_val, '?' ) !== false ) {
            $new_val .= '&lang=en';
        } else {
            $new_val .= '?lang=en';
        }
        $new_rules['^' . $new_key] = $new_val;
    }
    
    // Merge new rules BEFORE old rules
    return $new_rules + $rules;
});

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
// 3. AUTO-APPEND ?lang=en TO LINKS
// ==========================================
function bookingroom_append_lang_to_link( $url ) {
    if ( ! is_admin() && defined( 'SITE_LANG' ) ) {
        $home_url = home_url();
        if ( strpos( $url, $home_url ) === 0 ) {
            $path = substr( $url, strlen( $home_url ) );
            
            if ( SITE_LANG === 'en' ) {
                if ( ! preg_match('#^/en(/|\?|$)#i', $path) ) {
                    $url = rtrim( $home_url, '/' ) . '/en' . ( $path ? ( strpos( $path, '/' ) === 0 ? $path : '/' . $path ) : '/' );
                }
            } elseif ( isset($_SERVER['REQUEST_URI']) && preg_match('#^/vn(/|\?|$)#i', $_SERVER['REQUEST_URI']) ) {
                // If we are currently browsing the physical /vn/ folder, keep links in /vn/
                if ( ! preg_match('#^/vn(/|\?|$)#i', $path) ) {
                    $url = rtrim( $home_url, '/' ) . '/vn' . ( $path ? ( strpos( $path, '/' ) === 0 ? $path : '/' . $path ) : '/' );
                }
            }
        }
    }
    return $url;
}

// ==========================================
// 3.5 URL HELPER FOR LANGUAGE SWITCHER
// ==========================================
function bookingroom_get_lang_switch_url( $target_lang ) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $home_path = parse_url(home_url(), PHP_URL_PATH) ?: '';
    if ( $home_path ) {
        $uri = preg_replace('#^' . preg_quote($home_path, '#') . '#', '', $uri);
    }
    
    // Remove /en or /vn if it exists to get the base path
    $uri_base = preg_replace('#^/(en|vn)(/|\?|$)#i', '$2', $uri);
    $uri_base = $uri_base ?: '/';
    
    if ( $target_lang === 'en' ) {
        if ( $uri_base === '/' || $uri_base === '' ) {
            $uri_en = '/en/';
        } else {
            $uri_en = '/en' . ( strpos( $uri_base, '/' ) === 0 ? $uri_base : '/' . $uri_base );
        }
        return home_url( $uri_en );
    } elseif ( $target_lang === 'vi' || $target_lang === 'vn' ) {
        // If the user requested 'vn' as the physical folder, we can route them there
        // Actually, the language switcher button is hardcoded to switch to VI, but we can direct them to /vn/
        // However, root is fine for VI. If they want to specifically use /vn/, they can.
        // Let's generate a /vn/ link for Vietnamese to match the user's folder!
        if ( $uri_base === '/' || $uri_base === '' ) {
            $uri_vn = '/vn/';
        } else {
            $uri_vn = '/vn' . ( strpos( $uri_base, '/' ) === 0 ? $uri_base : '/' . $uri_base );
        }
        return home_url( $uri_vn );
    }
    
    return home_url( $uri_base );
}
add_filter( 'post_link', 'bookingroom_append_lang_to_link', 10 );
add_filter( 'page_link', 'bookingroom_append_lang_to_link', 10 );
add_filter( 'post_type_link', 'bookingroom_append_lang_to_link', 10 );
add_filter( 'term_link', 'bookingroom_append_lang_to_link', 10 );
add_filter( 'home_url', 'bookingroom_append_lang_to_link', 10 );

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

// ==========================================
// 7. HELPER FOR CUSTOM FIELDS
// ==========================================
function bookingroom_get_meta_lang( $post_id, $meta_key, $single = true ) {
    if ( defined( 'SITE_LANG' ) && SITE_LANG === 'en' ) {
        $en_meta = get_post_meta( $post_id, $meta_key . '_en', $single );
        if ( ! empty( $en_meta ) ) {
            return $en_meta;
        }
    }
    return get_post_meta( $post_id, $meta_key, $single );
}

