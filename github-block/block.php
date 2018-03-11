<?php

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


