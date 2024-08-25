<?php

namespace WCCF\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Cart_Fee_Handler {

    public static function init() {
        add_filter( 'woocommerce_add_cart_item_data', [ __CLASS__, 'add_custom_option_price' ], 10, 2 );
        add_action( 'woocommerce_before_calculate_totals', [ __CLASS__, 'adjust_cart_item_price' ], 20, 1 );
        add_action( 'woocommerce_cart_calculate_fees', [ __CLASS__, 'apply_custom_option_fee' ], 20, 1 );
        add_filter( 'woocommerce_get_item_data', [ __CLASS__, 'display_custom_option_in_cart' ], 10, 2 );
        add_action( 'woocommerce_add_order_item_meta', [ __CLASS__, 'add_custom_option_to_order_items' ], 10, 2 );
    }

    public static function add_custom_option_price( $cart_item_data, $product_id ) {
        if ( isset( $_POST['custom_option'] ) ) {
            $cart_item_data['custom_option'] = $_POST['custom_option'];
            $cart_item_data['unique_key'] = md5( microtime() . rand() ); // Garante que cada item do carrinho seja único
        }
        return $cart_item_data;
    }

    public static function adjust_cart_item_price( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        foreach ( $cart->get_cart() as $cart_item ) {
            if ( isset( $cart_item['custom_option'] ) ) {
                $additional_price = (float) $cart_item['custom_option'];
                $original_price = (float) $cart_item['data']->get_price();
                $custom_option_per_item = get_post_meta( $cart_item['product_id'], '_custom_option_per_item', true );

                if ( $custom_option_per_item === 'yes' ) {
                    $new_price = $original_price + $additional_price;
                    $cart_item['data']->set_price( $new_price );
                }
            }
        }
    }

    public static function apply_custom_option_fee( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        $additional_cost = 0;
        $custom_option_name = '';

        foreach ( $cart->get_cart() as $cart_item ) {
            if ( isset( $cart_item['custom_option'] ) ) {
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];
                $custom_option_price = (float) $cart_item['custom_option'];
                $custom_option_per_item = get_post_meta( $product_id, '_custom_option_per_item', true );
                $custom_option_name = get_post_meta( $product_id, '_custom_option_name', true );

                if ( $custom_option_per_item !== 'yes' ) {
                    $additional_cost += $custom_option_price;
                }
            }
        }

        if ( $additional_cost > 0 ) {
            WC()->cart->add_fee( $custom_option_name, $additional_cost );
        }
    }

    public static function display_custom_option_in_cart( $item_data, $cart_item ) {
        if ( isset( $cart_item['custom_option'] ) ) {
            $product_id = $cart_item['product_id'];
            $custom_option_name = get_post_meta( $product_id, '_custom_option_name', true );
            $custom_option_price = esc_html( $cart_item['custom_option'] );
            $custom_option_per_item = get_post_meta( $product_id, '_custom_option_per_item', true );
            $currency_symbol = get_woocommerce_currency_symbol();

            if ( $custom_option_per_item === 'yes' ) {
                $item_data[] = array(
                    'name' => esc_html( $custom_option_name ),
                    'value' => sprintf( __( 'Sim (%s%s por item)', 'woocommerce-custom-fees' ), $currency_symbol, $custom_option_price ),
                );
            } else {
                $item_data[] = array(
                    'name' => esc_html( $custom_option_name ),
                    'value' => sprintf( __( 'Sim (%s%s)', 'woocommerce-custom-fees' ), $currency_symbol, $custom_option_price ),
                );
            }
        }
        return $item_data;
    }

    public static function add_custom_option_to_order_items( $item_id, $values ) {
        if ( isset( $values['custom_option'] ) ) {
            $product_id = $values['product_id'];
            $custom_option_name = get_post_meta( $product_id, '_custom_option_name', true );
            $custom_option_price = esc_html( $values['custom_option'] );
            $custom_option_per_item = get_post_meta( $product_id, '_custom_option_per_item', true );
            $currency_symbol = get_woocommerce_currency_symbol();

            if ( $custom_option_per_item === 'yes' ) {
                wc_add_order_item_meta( $item_id, $custom_option_name, sprintf( __( 'Sim (%s%s por item)', 'woocommerce-custom-fees' ), $currency_symbol, $custom_option_price ) );
            } else {
                wc_add_order_item_meta( $item_id, $custom_option_name, sprintf( __( 'Sim (%s%s)', 'woocommerce-custom-fees' ), $currency_symbol, $custom_option_price ) );
            }
        }
    }
}

Cart_Fee_Handler::init();