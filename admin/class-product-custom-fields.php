<?php

namespace WCCF\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Product_Custom_Fields {

    public static function init() {
        add_action( 'woocommerce_product_options_general_product_data', [ __CLASS__, 'add_custom_option_fields' ] );
        add_action( 'woocommerce_process_product_meta', [ __CLASS__, 'save_custom_option_fields' ] );
    }

    public static function add_custom_option_fields() {
        woocommerce_wp_checkbox([
            'id'          => '_enable_custom_option',
            'label'       => __('Habilitar Opção Adicional', 'woocommerce-custom-fees'),
            'description' => __('Habilitar essa opção para adicionar um custo adicional a este produto.', 'woocommerce-custom-fees'),
        ]);

        woocommerce_wp_text_input([
            'id'          => '_custom_option_name',
            'label'       => __('Nome da Opção Adicional', 'woocommerce-custom-fees'),
            'description' => __('Digite o nome da opção adicional. Ex: Embrulhar para presente.', 'woocommerce-custom-fees'),
            'desc_tip'    => true,
        ]);

        woocommerce_wp_text_input([
            'id'                => '_custom_option_price',
            'label'             => __('Custo da Opção Adicional', 'woocommerce-custom-fees'),
            'description'       => __('Digite o custo adicional.', 'woocommerce-custom-fees'),
            'desc_tip'          => true,
            'type'              => 'number',
            'custom_attributes' => [
                'step' => '0.01',
                'min'  => '0',
            ],
        ]);

        woocommerce_wp_checkbox([
            'id'          => '_custom_option_per_item',
            'label'       => __('Aplicar custo por item', 'woocommerce-custom-fees'),
            'description' => __('Marque esta opção para aplicar o custo adicional por item no carrinho. Deixe desmarcada para aplicar o custo por pedido.', 'woocommerce-custom-fees'),
        ]);

        woocommerce_wp_select([
            'id'          => '_custom_option_position',
            'label'       => __('Posição da Opção Adicional', 'woocommerce-custom-fees'),
            'description' => __('Selecione onde a opção adicional deve aparecer na página do produto.', 'woocommerce-custom-fees'),
            'desc_tip'    => true,
            'options'     => [
                'before_add_to_cart_button' => __('Antes do botão "Adicionar ao Carrinho"', 'woocommerce-custom-fees'),
                'after_add_to_cart_button'  => __('Depois do botão "Adicionar ao Carrinho"', 'woocommerce-custom-fees'),
            ],
        ]);
    }

    public static function save_custom_option_fields( $post_id ) {
        $fields = [
            '_enable_custom_option'     => isset( $_POST['_enable_custom_option'] ) ? 'yes' : 'no',
            '_custom_option_name'       => sanitize_text_field( $_POST['_custom_option_name'] ?? 'Opção Adicional' ),
            '_custom_option_price'      => sanitize_text_field( $_POST['_custom_option_price'] ?? '0' ),
            '_custom_option_per_item'   => isset( $_POST['_custom_option_per_item'] ) ? 'yes' : 'no',
            '_custom_option_position'   => sanitize_text_field( $_POST['_custom_option_position'] ?? 'before_add_to_cart_button' ),
        ];

        foreach ( $fields as $key => $value ) {
            update_post_meta( $post_id, $key, $value );
        }
    }
}

Product_Custom_Fields::init();