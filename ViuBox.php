<?php

/*
Plugin Name: ViuBox SYZ
Description: Adds a Viubox Measurements Button on WooCommerce single product pages.
Author: SenseMi ViuBox
Author URI: http://www.viubox.com
Plugin URI: https://viubox.com/products/remote-body-measurements-app/
License: GPLv2
Text Domain: viubox-syz
Domain Path: /languages
Version: 2.2.2
*/

define( "VIUBOX_SYZ_VERSION", "2.2.2" );

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load scripts and styles on the front-end
add_action( 'wp_enqueue_scripts', 'viubox_syz_register_assets' );

// Call the chosen woocommerce hook to insert the button on the product page
add_action( 'init', 'viubox_syz_insert_locaiton' );

// Loads the plugin's translated strings from the languages folder inside the plugin folder
add_action( 'init', 'viubox_syz_load_plugin_textdomain' );

// Create admin menu element
add_action( 'admin_menu', 'viubox_syz_add_admin_menu' );

// Loads the plugin's translated strings from the languages folder inside the plugin folder
function viubox_syz_load_plugin_textdomain() {
    load_plugin_textdomain( 'viubox-syz', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}

// Load scripts and styles on the front-end
function viubox_syz_register_assets() {
    if ( function_exists( 'is_product' ) && is_product() ) {
        wp_register_script( 'viubox-syz-axios-script', plugin_dir_url( __FILE__ ) . 'scripts/axios.min.js', array( 'jquery' ), VIUBOX_SYZ_VERSION, false );
        wp_register_script( 'viubox-syz-embed-script', 'https://widget.viubox.com/js/embed-hidden.js', array( 'jquery' ), VIUBOX_SYZ_VERSION, false );
        wp_register_script( 'viubox-syz-product-script', plugin_dir_url( __FILE__ ) . 'scripts/product.js', array( 'jquery' ), VIUBOX_SYZ_VERSION, false );
        wp_register_style( 'viubox-syz-style', plugin_dir_url( __FILE__ ) . 'styles/style.css', false, VIUBOX_SYZ_VERSION );
        wp_enqueue_script( 'viubox-syz-axios-script', '', array(), false, false );
        wp_enqueue_script( 'viubox-syz-embed-script', '', array(), false, false );
        wp_enqueue_script( 'viubox-syz-product-script', '', array(), false, false );
        wp_enqueue_style( 'viubox-syz-style' );
        $settings = viubox_syz_get_settings();
        $add_css = '
            .viubox-syz-svg-path {
                stroke: ' . sanitize_hex_color( $settings['viubox-syz-text-color'] ) . ' !important;
            }
            a.viubox-syz-measurments-button,
            a.viubox-syz-measurments-button:visited,
            a.viubox-syz-measurments-button:active,
            a.viubox-syz-measurments-button:focus {
                background-color: ' . sanitize_hex_color( $settings['viubox-syz-background-color'] ) . ';
                width: ' . intval( $settings['viubox-syz-width'] ) . 'px;
                max-width: 100%;
                box-sizing: border-box;
                font-size: ' . intval( $settings['viubox-syz-text-size'] ) . 'px;
                margin-top: ' . intval( $settings['viubox-syz-margin-top'] ) . 'px;
                margin-right: ' . intval( $settings['viubox-syz-margin-right'] ) . 'px;
                margin-bottom: ' . intval( $settings['viubox-syz-margin-bottom'] ) . 'px;
                margin-left: ' . intval( $settings['viubox-syz-margin-left'] ) . 'px;
                padding-top: ' . intval( $settings['viubox-syz-padding-top'] ) . 'px;
                padding-right: 5px;
                padding-bottom: ' . intval( $settings['viubox-syz-padding-bottom'] ) . 'px;
                padding-left: 5px;
                border-radius: ' . intval( $settings['viubox-syz-rounding'] ) . 'px;
                border-width: ' . intval( $settings['viubox-syz-border-size'] ) . 'px;
                border-style: solid;
                border-color: ' . sanitize_hex_color( $settings['viubox-syz-border-color'] ) . ';
            }
            a.viubox-syz-measurments-button:hover {
                background-color: ' . sanitize_hex_color( $settings['viubox-syz-background-color-hover'] ) . ';
                border-width: ' . intval( $settings['viubox-syz-border-size'] ) . 'px;
                border-style: solid;
                border-color: ' . sanitize_hex_color( $settings['viubox-syz-border-color-hover'] ) . ';
            }
            #viubox-syz-text {
                color: ' . sanitize_hex_color( $settings['viubox-syz-text-color'] ) . ';
            }
            a.viubox-syz-measurments-button:hover #viubox-syz-text {
                color: ' . sanitize_hex_color( $settings['viubox-syz-text-color-hover'] ) . ';
            }
            .viubox-syz-measurments-button:hover .viubox-syz-svg-path {
                stroke: ' . sanitize_hex_color( $settings['viubox-syz-text-color-hover'] ) . ' !important;
            }
            #viubox-syz-hanger {
                height: ' . intval( $settings['viubox-syz-hanger-height'] ) . 'px;
            }
        ';
        wp_add_inline_style( 'viubox-syz-style', $add_css );
    }
}

// Call the chosen woocommerce hook to insert the button on the product page
function viubox_syz_insert_locaiton() {
    $insert_location = viubox_syz_get_setting( 'viubox-syz-insert-location' );
    add_action( 'woocommerce_' . sanitize_html_class( $insert_location ), 'viubox_syz_insert_button' );
}

// Insert the button on every single product page
function viubox_syz_insert_button() {
    if ( function_exists( 'is_product' ) && function_exists( 'wc_get_product' ) && is_product() ) {
        $button_text = viubox_syz_get_setting( 'viubox-syz-text-value' );
        $product = wc_get_product();
        $sku = "";
        $add_class = "";
        $get_sku = $product->get_sku();
        if ( ! empty( $get_sku ) ) {
            $sku = $get_sku;
        }
        if ( $product->get_type() === "variable" ) {
            echo "<input id='viubox-syz-variation-main-sku' type='hidden' value='" . esc_attr( $sku ) . "' />";
            $variations = $product->get_available_variations();
            if ( ! empty( $variations ) ) {
                foreach ( $variations as $variation ) {
                    if ( is_array( $variation ) && array_key_exists( 'variation_id', $variation ) && array_key_exists( 'sku', $variation ) ) {
                        echo "<input id='viubox-syz-variation-sku-" . esc_attr( $variation['variation_id'] ) . "' type='hidden' value='"
                            . esc_attr( $variation['sku'] ) . "' />";
                    }
                }
            }
        }
        ?>
        <div class="viubox-syz-contain-button display-n">
            <a id="viubox-syz-measurments" class="viubox-syz-measurments-button" href="javascript:void(0)" style="opacity: 1;" data-syzsku="<?php echo esc_attr( $sku ); ?>">
                <span class="viubox-syz-inner-button">
                    <svg id='viubox-syz-hanger' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400.25 310.719">
                        <defs>
                            <style>
                                .viubox-syz-svg-path {
                                    fill: none;
                                    stroke: black;
                                    stroke-linecap: round;
                                    stroke-width: 15px;
                                    fill-rule: evenodd;
                                }
                            </style>
                        </defs>
                        <path class="viubox-syz-svg-path" d="M92,381c-28.982,0-49.715-37.636-23-58s134.285-91.95,207-146c68.919-51.229-39.355-143.265-76-55" transform="translate(-49.156 -77.781)"/>
                        <path class="viubox-syz-svg-path" d="M289,225s118.864,83.022,138,96c32.784,22.234,5.38,66.483-24,59" transform="translate(-49.156 -77.781)"/>
                        <path class="viubox-syz-svg-path" d="M247,365V306" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy" data-name="Shape 3 copy" class="viubox-syz-svg-path" d="M278,365V329" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy_2" data-name="Shape 3 copy 2" class="viubox-syz-svg-path" d="M218,365V329" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy_3" data-name="Shape 3 copy 3" class="viubox-syz-svg-path" d="M187,372V347" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy_4" data-name="Shape 3 copy 4" class="viubox-syz-svg-path" d="M310,372V347" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy_5" data-name="Shape 3 copy 5" class="viubox-syz-svg-path" d="M336,376V360" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy_6" data-name="Shape 3 copy 6" class="viubox-syz-svg-path" d="M157,376V360" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy_7" data-name="Shape 3 copy 7" class="viubox-syz-svg-path" d="M125,376v-4" transform="translate(-49.156 -77.781)"/>
                        <path id="Shape_3_copy_8" data-name="Shape 3 copy 8" class="viubox-syz-svg-path" d="M368,376v-4" transform="translate(-49.156 -77.781)"/>
                    </svg>
                    <span id='viubox-syz-text' class="display-n"><?php echo esc_html( $button_text ); ?></span>
                </span>
            </a>
        </div>
        <?php
    }
}

/**
 * Returns a plugin setting value
 * @param string $name
 * @return mixed
 */
function viubox_syz_get_setting( $name ) {
    $settings = viubox_syz_get_settings();
    if ( array_key_exists( $name, $settings ) ) {
        return $settings[ $name ];
    }
    return false;
}

/**
 * Returns the plugin settings
 * @return array
 */
function viubox_syz_get_settings() {
    $default_settings = Array(
        'viubox-syz-insert-location' => "before_add_to_cart_quantity",
        'viubox-syz-text-value' => "What's my size?",
        'viubox-syz-width' => 200,
        'viubox-syz-text-size' => 16,
        'viubox-syz-text-color' => "#000000",
        'viubox-syz-text-color-hover' => "#ffffff",
        'viubox-syz-background-color' => "#ffffff",
        'viubox-syz-background-color-hover' => "#000000",
        'viubox-syz-margin-top' => 0,
        'viubox-syz-margin-right' => 0,
        'viubox-syz-margin-bottom' => 20,
        'viubox-syz-margin-left' => 0,
        'viubox-syz-padding-top' => 10,
        'viubox-syz-padding-bottom' => 10,
        'viubox-syz-border-size' => 3,
        'viubox-syz-border-color' => "#000000",
        'viubox-syz-border-color-hover' => "#000000",
        'viubox-syz-rounding' => 0,
        'viubox-syz-hanger-height' => 21,
    );
    $settings = get_option( 'viubox-syz-settings', array() );
    $return_settings = Array();
    foreach ( $default_settings as $key => $value ) {
        if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
            $return_settings[ $key ] = $settings[ $key ];
        } else {
            $return_settings[ $key ] = $value;
        }
    }
    return $return_settings;
}

// Create admin menu element
function viubox_syz_add_admin_menu() {
    $settings_page = add_menu_page(
        esc_html__( 'ViuBox SYZ', 'viubox-syz' ),
        esc_html__( 'ViuBox SYZ', 'viubox-syz' ),
        'manage_options',
        'viubox-syz',
        'viubox_syz_admin_page_settings',
        'dashicons-universal-access'
    );
    add_action( 'load-' . $settings_page, 'viubox_syz_settings_scripts' );
}

// Load the admin settings page scripts and styles
function viubox_syz_settings_scripts() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_register_script( 'viubox-syz-settings', plugin_dir_url( __FILE__ ) . 'scripts/settings.js', array( 'jquery', 'wp-color-picker' ), VIUBOX_SYZ_VERSION, false );
    $localize = array(
        'confirmReset' => esc_js( __( 'Are you sure you want to reset the plugin settings? Your current settings will be permanently lost.', 'viubox-syz' ) ),
    );
    wp_localize_script( 'viubox-syz-settings', 'localizedButtonData', $localize );
    wp_enqueue_script( 'viubox-syz-settings' );
    wp_register_style( 'viubox-syz-settings-style', plugin_dir_url( __FILE__ ) . 'styles/settings.css', false, VIUBOX_SYZ_VERSION );
    wp_enqueue_style( 'viubox-syz-settings-style' );
}

// Outputs the HTML code for the settings admin page and handle form submission
function viubox_syz_admin_page_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to access this page.', 'viubox-syz' ) );
    }
    $status = 'none';
    $message = '';

    do {
        if ( ! isset( $_POST['viubox-syz-reset-hidden'] ) || "yes" !== $_POST['viubox-syz-reset-hidden'] ) {
            break;
        }
        $status = 'error';
        if ( ! wp_verify_nonce( $_POST['viubox-syz-nonce-name'], 'viubox-syz-nonce' ) ) {
            $message = __( 'Error: Invalid security nonce. Please reload the page.', 'viubox-syz' );
            break;
        }
        delete_option( 'viubox-syz-settings' );
        $message = __( 'The settings were reset to default values.', 'viubox-syz' );
        $status = "done";
    } while ( false );

    do {
        if ( ! isset( $_POST['viubox-syz-submit'] ) ) {
            break;
        }
        $status = 'error';
        if ( ! wp_verify_nonce( $_POST['viubox-syz-nonce-name'], 'viubox-syz-nonce' ) ) {
            $message = __( 'Error: Invalid security nonce. Please reload the page.', 'viubox-syz' );
            break;
        }
        $current_settings = viubox_syz_get_settings();
        foreach ( $current_settings as $setting_name => $setting_value ) {
            if ( ! isset( $_POST[ $setting_name ] ) ) {
                $message = __( 'Error: Missing POST field data.', 'viubox-syz' );
                break 2;
            }
        }
        if ( ! viubox_syz_is_whole_positive_number_or_zero( $_POST['viubox-syz-text-size'] )
            || ! viubox_syz_is_whole_positive_number_or_zero( $_POST['viubox-syz-width'] )
            || ! viubox_syz_is_whole_positive_number_or_zero( $_POST['viubox-syz-padding-top'] )
            || ! viubox_syz_is_whole_positive_number_or_zero( $_POST['viubox-syz-padding-bottom'] )
            || ! viubox_syz_is_whole_positive_number_or_zero( $_POST['viubox-syz-border-size'] )
            || ! viubox_syz_is_whole_positive_number_or_zero( $_POST['viubox-syz-rounding'] )
            || ! viubox_syz_is_whole_positive_number_or_zero( $_POST['viubox-syz-hanger-height'] )
            || ! viubox_syz_is_whole_number( $_POST['viubox-syz-margin-top'] )
            || ! viubox_syz_is_whole_number( $_POST['viubox-syz-margin-left'] )
            || ! viubox_syz_is_whole_number( $_POST['viubox-syz-margin-right'] )
            || ! viubox_syz_is_whole_number( $_POST['viubox-syz-margin-bottom'] )
            || ! viubox_syz_is_hex_color( $_POST['viubox-syz-text-color'] )
            || ! viubox_syz_is_hex_color( $_POST['viubox-syz-text-color-hover'] )
            || ! viubox_syz_is_hex_color( $_POST['viubox-syz-background-color'] )
            || ! viubox_syz_is_hex_color( $_POST['viubox-syz-background-color-hover'] )
            || ! viubox_syz_is_hex_color( $_POST['viubox-syz-border-color'] )
            || ! viubox_syz_is_hex_color( $_POST['viubox-syz-border-color-hover'] )
            || sanitize_text_field( $_POST['viubox-syz-text-value'] ) !== trim( $_POST['viubox-syz-text-value'] )
            || ! in_array( $_POST['viubox-syz-insert-location'], Array( 'before_add_to_cart_quantity', 'after_add_to_cart_quantity', 'before_add_to_cart_button',
                'after_add_to_cart_button', 'before_add_to_cart_form', 'after_add_to_cart_form' ) ) ) {
            $message = __( 'Error: Invalid form data sent.', 'viubox-syz' );
            break;
        }
        $new_settings = Array(
            'viubox-syz-insert-location' =>  sanitize_html_class( $_POST['viubox-syz-insert-location'] ),
            'viubox-syz-text-value' => sanitize_text_field( stripslashes( $_POST['viubox-syz-text-value'] ) ),
            'viubox-syz-text-size' => intval( $_POST['viubox-syz-text-size'] ),
            'viubox-syz-width' => intval( $_POST['viubox-syz-width'] ),
            'viubox-syz-text-color' => sanitize_hex_color( $_POST['viubox-syz-text-color'] ),
            'viubox-syz-text-color-hover' => sanitize_hex_color( $_POST['viubox-syz-text-color-hover'] ),
            'viubox-syz-background-color' => sanitize_hex_color( $_POST['viubox-syz-background-color'] ),
            'viubox-syz-background-color-hover' => sanitize_hex_color( $_POST['viubox-syz-background-color-hover'] ),
            'viubox-syz-margin-top' => intval( $_POST['viubox-syz-margin-top'] ),
            'viubox-syz-margin-right' => intval( $_POST['viubox-syz-margin-right'] ),
            'viubox-syz-margin-bottom' => intval( $_POST['viubox-syz-margin-bottom'] ),
            'viubox-syz-margin-left' => intval( $_POST['viubox-syz-margin-left'] ),
            'viubox-syz-padding-top' => intval( $_POST['viubox-syz-padding-top'] ),
            'viubox-syz-padding-bottom' => intval( $_POST['viubox-syz-padding-bottom'] ),
            'viubox-syz-border-size' => intval( $_POST['viubox-syz-border-size'] ),
            'viubox-syz-border-color' => sanitize_hex_color( $_POST['viubox-syz-border-color'] ),
            'viubox-syz-border-color-hover' => sanitize_hex_color( $_POST['viubox-syz-border-color-hover'] ),
            'viubox-syz-rounding' => intval( $_POST['viubox-syz-rounding'] ),
            'viubox-syz-hanger-height' => intval( $_POST['viubox-syz-hanger-height'] ),
        );
        update_option( 'viubox-syz-settings', $new_settings );
        $message = __( 'The settings were saved.', 'viubox-syz' );
        $status = "done";
    } while ( false );
    ?>

    <div class="wrap">
        <?php
        if ( "error" === $status ) {
            ?>
            <div class="error notice">
                <p><?php echo esc_html( $message ); ?></p>
            </div>
            <?php
        } elseif ( "done" === $status ) {
            ?>
            <div class="updated notice">
                <p><?php echo esc_html( $message ); ?></p>
            </div>
            <?php
        }
        ?>
        <h1><?php echo esc_html__( 'ViuBox SYZ', 'viubox-syz' ); ?></h1>

        <hr>

        <form name="viubox-syz-settings-form" id="viubox-syz-settings-form" action="" autocomplete="off" method="post">

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-insert-location"><?php echo esc_html__( 'Button Insert Location', 'viubox-syz' ); ?></label>
            </p>
            <p>
                <?php
                $insert_location_values = Array(
                    'before_add_to_cart_quantity',
                    'after_add_to_cart_quantity',
                    'before_add_to_cart_button',
                    'after_add_to_cart_button',
                    'before_add_to_cart_form',
                    'after_add_to_cart_form',
                );
                $insert_location_names = Array(
                    'Before add to cart quantity',
                    'After add to cart quantity',
                    'Before add to cart button',
                    'After add to cart button',
                    'Before add to cart form',
                    'After add to cart form',
                );
                viubox_syz_setting_select( 'viubox-syz-insert-location', $insert_location_values, $insert_location_names );
                ?>
            </p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-text-value"><?php echo esc_html__( 'Button Text', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-text-value' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-text-size"><?php echo esc_html__( 'Button Text Size (px)', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-text-size', 'viubox-syz-small-field' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-width"><?php echo esc_html__( 'Button Width (px)', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-width', 'viubox-syz-small-field' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-text-color"><?php echo esc_html__( 'Button Text Color', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-text-color' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-text-color-hover"><?php echo esc_html__( 'Button Hover Text Color', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-text-color-hover' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-background-color"><?php echo esc_html__( 'Button Background Color', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-background-color' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-background-color-hover"><?php echo esc_html__( 'Button Hover Background Color', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-background-color-hover' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-margin-top"><?php echo esc_html__( 'Button Outer Spacing (px)', 'viubox-syz' ); ?></label>
            </p>
            <p>
                <?php echo esc_html__( 'Top:', 'viubox-syz' ); ?> <?php viubox_syz_setting_input_text( 'viubox-syz-margin-top', 'viubox-syz-small-field' ); ?>&nbsp;&nbsp;&nbsp;
                <?php echo esc_html__( 'Right:', 'viubox-syz' ); ?> <?php viubox_syz_setting_input_text( 'viubox-syz-margin-right', 'viubox-syz-small-field' ); ?>&nbsp;&nbsp;&nbsp;
                <?php echo esc_html__( 'Bottom:', 'viubox-syz' ); ?> <?php viubox_syz_setting_input_text( 'viubox-syz-margin-bottom', 'viubox-syz-small-field' ); ?>&nbsp;&nbsp;&nbsp;
                <?php echo esc_html__( 'Left:', 'viubox-syz' ); ?> <?php viubox_syz_setting_input_text( 'viubox-syz-margin-left', 'viubox-syz-small-field' ); ?>
            </p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-padding-top"><?php echo esc_html__( 'Button Inner Spacing (px)', 'viubox-syz' ); ?></label>
            </p>
            <p>
                <?php echo esc_html__( 'Top:', 'viubox-syz' ); ?> <?php viubox_syz_setting_input_text( 'viubox-syz-padding-top', 'viubox-syz-small-field' ); ?>&nbsp;&nbsp;&nbsp;
                <?php echo esc_html__( 'Bottom:', 'viubox-syz' ); ?> <?php viubox_syz_setting_input_text( 'viubox-syz-padding-bottom', 'viubox-syz-small-field' ); ?>
            </p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-border-size"><?php echo esc_html__( 'Button Border Size (px)', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-border-size', 'viubox-syz-small-field' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-border-color"><?php echo esc_html__( 'Button Border Color', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-border-color' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-border-color-hover"><?php echo esc_html__( 'Button Hover Border Color', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-border-color-hover' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-rounding"><?php echo esc_html__( 'Button Rounding (px)', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-rounding', 'viubox-syz-small-field' ); ?></p>

            <p class="viubox-syz-label-contain">
                <label for="viubox-syz-hanger-height"><?php echo esc_html__( 'Hanger Icon Height (px)', 'viubox-syz' ); ?></label>
            </p>
            <p><?php viubox_syz_setting_input_text( 'viubox-syz-hanger-height', 'viubox-syz-small-field' ); ?></p>

            <p><hr></p>

            <p>
                <input id="viubox-syz-reset-hidden" name="viubox-syz-reset-hidden" type="hidden" value="no" />
                <input type="submit" class="button button-primary" name="viubox-syz-submit" id="viubox-syz-submit"
                    value="<?php echo esc_attr__( 'Save Settings', 'viubox-syz' ); ?>">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" onclick="ResetButtonSettings()" class="button" name="viubox-syz-submit-default" id="viubox-syz-submit-default"
                    value="<?php echo esc_attr__( 'Reset Settings', 'viubox-syz' ); ?>">
                <?php wp_nonce_field( 'viubox-syz-nonce', 'viubox-syz-nonce-name' ); ?>
            </p>
        </form>
    </div>
    <?php
}

/**
 * Checks if the provided variable is a whole positive number or 0. Allows a string number too!
 * @param mixed $number
 * @return bool
 */
function viubox_syz_is_whole_positive_number_or_zero( $number ) {
    if ( is_numeric( $number ) && ! preg_match( '/[^0-9]/', $number ) && intval( $number ) == $number && $number > -1
        && ! ( strlen( strval( $number ) ) > 1 && 0 == $number ) && ! ( substr( strval( $number ), 0, 1 ) === '0' && strlen( strval( $number ) ) > 1 ) ) {
        return true;
    }
    return false;
}

/**
 * Checks if the provided variable is a whole number. Allows a string number too!
 * @param mixed $number
 * @return bool
 */
function viubox_syz_is_whole_number( $number ) {
    if ( is_numeric( $number ) && ! preg_match( '/[^0-9\-]/', $number ) && intval( $number ) == $number
        && ! ( strlen( strval( $number ) ) > 1 && 0 == $number ) && ! ( substr( strval( $number ), 0, 1 ) === '0' && strlen( strval( $number ) ) > 1 ) ) {
        return true;
    }
    return false;
}

/**
 * Checks if the provided variable is a valid HEX color value.
 * @param string $string
 * @return int
 */
function viubox_syz_is_hex_color( $string ) {
    return preg_match( '/^#[a-f0-9]{6}$/i', $string );
}

/**
 * Display an html select form element to use in a plugin settings page. It is automatically set to the current setting value
 * @param string $name
 * @param array $option_values
 * @param mixed $option_names
 */
function viubox_syz_setting_select( $name, $option_values, $option_names ) {
    if ( ! is_array( $option_names ) && 'same-as-values' == $option_names ) {
        $option_names = $option_values;
    }
    $current_db_value = viubox_syz_get_setting( $name );
    echo '<select autocomplete="off" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" size="1">';
    for ( $i = 0; $i < count( $option_values ); $i++ ) {
        echo '<option value="' . esc_attr( $option_values[ $i ] ) . '" ' . selected( $option_values[ $i ], $current_db_value, false ) . ' >'
            . esc_html( $option_names[ $i ] ) . '</option>';
    }
    echo '</select>';
}

/**
 * Display an html input type text form element to use in a plugin settings page. It is automatically filled with the current setting value.
 * @param string $name
 */
function viubox_syz_setting_input_text( $name, $add_class = '' ) {
    $current_db_value = viubox_syz_get_setting( $name );
    echo '<input autocomplete="off" type="text" class="' . esc_attr( $add_class ) . '" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name )
        . '" value="' . esc_attr( $current_db_value ) . '" onkeypress="return event.keyCode != 13;" />';
}
