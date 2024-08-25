<?php

namespace WCCF\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Product_Fee_Display {

    public static function init() {
        add_action( 'template_redirect', [ __CLASS__, 'determine_hook_for_custom_option' ] );
    }

    public static function determine_hook_for_custom_option() {
        if ( is_product() ) {
            global $post;
            $product = wc_get_product( $post->ID );
            if ( $product && get_post_meta( $product->get_id(), '_enable_custom_option', true ) === 'yes' ) {
                $custom_option_position = get_post_meta( $product->get_id(), '_custom_option_position', true );

                switch ( $custom_option_position ) {
                    case 'before_add_to_cart_button':
                        add_action( 'woocommerce_before_add_to_cart_button', [ __CLASS__, 'add_custom_option_to_product' ] );
                        break;
                    case 'after_add_to_cart_button':
                        add_action( 'woocommerce_after_add_to_cart_button', [ __CLASS__, 'add_custom_option_to_product' ] );
                        break;
                    default:
                        add_action( 'woocommerce_before_add_to_cart_button', [ __CLASS__, 'add_custom_option_to_product' ] );
                        break;
                }
            }
        }
    }

    public static function add_custom_option_to_product() {
        global $product;
        if ( get_post_meta( $product->get_id(), '_enable_custom_option', true ) === 'yes' ) {
            $custom_option_name = get_post_meta( $product->get_id(), '_custom_option_name', true );
            $custom_option_price = get_post_meta( $product->get_id(), '_custom_option_price', true );
            $currency_symbol = get_woocommerce_currency_symbol(); // Obtém o símbolo da moeda configurada no WooCommerce

            echo '<div class="custom-option">
                    <label for="custom_option">
                        <input type="checkbox" name="custom_option" id="custom_option" value="' . esc_attr( $custom_option_price ) . '"> ' . esc_html( $custom_option_name ) . ' (' . esc_html( '+' . $currency_symbol . ' ' . $custom_option_price ) . ')
                    </label>
                  </div>';
        }
    }
}

Product_Fee_Display::init();