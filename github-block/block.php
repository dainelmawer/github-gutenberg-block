<?php


if( ! function_exists( 'github_embed_init' ) ) {

    /*
    * Init Register Block Type
    *
    * This function inits a new function called register_block_type()
    * This function ties the JS block in the editor with a PHP version
    * whose data will be stored in the db and displayed on the frontend.
    * The style.css in the block will style the editor and front-end versions.
    */

    function github_embed_init() {

        register_block_type( 'dainemawer/github', array(
            'render_callback' => 'render_github_embed',
        ));

    }

    add_action( 'init', 'github_embed_init' );

}

if( ! function_exists( 'get_md5_post_hash' ) ) {

    /*
    * A function to hash the post_modified property in the db
    *
    * This function md5's the post_modified string for the given
    * post in order to be used as a cache buster for transients
    * in the render_github_embed()
    * @param object $post WordPress post object
    *
    */

    function get_md5_post_hash( $post ) {

        // If there is no post object, bail.
        if( ! $post ) {
            return;
        }

        // Uses the native PHP md5 function to convert post_modified date
        // to a hash which we can append to the transient name
        $modified = md5( $post->post_modified );
        return $modified;

    }

}

if( ! function_exists( 'render_github_embed' ) ) {

    /*
    * Render Gutenberg Block in PHP
    *
    * Its up to us now to render the block in the exact same way as we have in block.js
    * We will need to provide the same markup and data handling as we did in JS
    *
    * @param array $attributes allows you to access any attributes setup in registerBlockType()  
    */

    function render_github_embed( $attributes ) {

        // Grab access to the $post object
        global $post;

        // We'll set a default variable called $data which we will replace as we go through the logic
        $data = '';

        // Check if the $attributes array is empty, stop here if it is.
        if( empty( $attributes ) ) {
            return;
        }

        // Build API endpoint
        $apiURL = 'https://api.github.com/repos';

        // This is our dynamic URL input that we setup in registerBlockType()
        $endpoint = str_replace('https://github.com/', '', $attributes['url'] );


        // Using our function above, we'll hash the post_modified time.
        $bust = get_md5_post_hash( $post );

        // Check if our transient is in the database
        $transient = get_transient( 'github_block_' . $bust );

        // If it is there, set $data to the value of the transient
        if( ! empty( $transient ) ) {

            $data = $transient;

        // If it isnt, perform a GET request to the Public Github API
        } else {

            // Using wp_remote_get to grab the response from the API
            $response = wp_remote_get( esc_url_raw( $apiURL . '/' . $endpoint ) );

            // Check if the response is valid
            if( is_array( $response ) ) {

                // Convert that array to a JSON object
                $data = json_decode( wp_remote_retrieve_body( $response ), true );

                // Set our transient in the DB with the data from the API. 
                // Cache will be busted after successful post modification.
                set_transient( 'github_block_' . $bust, $data, DAY_IN_SECONDS );

            } 

        }

        // Check if there is a value for $data[message] and bail.
        if( ! empty( $data['message'] )  ) {
            return;
        }

        // Final check for the existance of data
        if( $data ) {

                $html  = '<div class="github-embed-wrapper">';
                $html .= '<div class="repo-description">';
                $html .= '<a href="'. esc_attr( esc_url( $data['html_url'] ) ) . '" title="' . esc_attr( $data['name'] ) . '" target="_blank" rel="noopener noferrer">' . esc_attr( $data['full_name'] ) . '</a><p>' . wp_trim_words( esc_attr( $data['description'] ), 10, '...' )  . '</p>';
                $html .= '</div>';
                $html .= '<a href="' . esc_attr( esc_url( $data['html_url'] ) ) . '" class="avatar_img" style="background-image: url(' . esc_attr( esc_url( $data['owner']['avatar_url'] ) ) . ')" target="_blank" rel="noopener noferrer"></a>';
                $html .= '</div>';

        } else {

            $html = '<p>' . __( 'Bummer, looks like there was an error retrieving data, have you checked that the Github URL above is correct?' ) . '</p>';

        }

        // Return data and Markup
        return $html; 

    }

}

if( ! function_exists( 'github_block_editor_assets' ) ) {

    /*
    * Enqueues Block Javascript
    * block.build.js contains compiled ES6 Javascript that allows the block
    * to work in the Gutenberg editor
    * @since 0.1.0
    */

    function github_block_editor_assets() {
        wp_enqueue_script(
            'github-block-scripts',
            plugins_url( 'block.build.js', __FILE__ ),
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'underscore' ),
            filemtime( plugin_dir_path( __FILE__ ) . 'block.build.js' )
        );
    }

    add_action( 'enqueue_block_editor_assets', 'github_block_editor_assets' );

}

if( ! function_exists( 'github_block_block_assets' ) ) {

    /*
    * Enqueues Block Styles for Client-facing & Editor Screens
    * This file will be appended to the admin and the front-end so that all 
    * instances of blocks can share the same styles.
    * @since 0.1.0
    */

    function github_block_block_assets() {
        wp_enqueue_style(
            'github-block-styles',
            plugins_url( 'style.css', __FILE__ ),
            array( 'wp-blocks' ),
            filemtime( plugin_dir_path( __FILE__ ) . 'style.css' )
        );
    }

    add_action( 'enqueue_block_assets', 'github_block_block_assets' );

}


