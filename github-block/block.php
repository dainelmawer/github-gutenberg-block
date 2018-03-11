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

        // Build API endpoint
        $apiURL = 'https://api.github.com/repos';

        // This is our dynamic URL input that we setup in registerBlockType()
        $endpoint = str_replace('https://github.com/', '', $attributes['url']);

        // Use wp_remote_get to make an API request to Github with user inputted URL from Gutenberg
        $response = wp_remote_get( esc_url_raw( $apiURL . '/' . $endpoint ) );
        
        // Check if the response is a valid array
        if( is_array( $response ) ) {

            // Conver that array to a JSON object

            $data = json_decode( wp_remote_retrieve_body( $response ), true );

            // Build up our markup
            $html  = '<div class="github-embed-wrapper">';
            $html .= '<div class="repo-description">';
            $html .= '<a href="'. esc_attr( esc_url( $data['html_url'] ) ) . '" title="' . esc_attr( $data['name'] ) . '" target="_blank" rel="noopener noferrer">' . esc_attr( $data['full_name'] ) . '</a><p>' . wp_trim_words( esc_attr( $data['description'] ), 10, '...' )  . '</p>';
            $html .= '</div>';
            $html .= '<a href="' . esc_attr( esc_url( $data['html_url'] ) ) . '" class="avatar_img" style="background-image: url(' . esc_attr( esc_url( $data['owner']['avatar_url'] ) ) . ')" target="_blank" rel="noopener noferrer"></a>';
            $html .= '</div>';
        
        } else {

            // If we didnt get back a valid array, output a user friendly error message.
            $html = '<p>' . __( 'Bummer, looks like there was an error retrieving data, have you checked that the Github URL above is correct?' ) . '</p>';

        }

        // Return the markup, this function will store the data in post_content, and render it when we use the_content() in our templates
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


