<?php
/**
 * Plugin Name: WooCommerce Custom Fees
 * Description: Adiciona uma funcionalidade de custo adicional personalizável aos produtos no WooCommerce.
 * Version: 1.0.0
 * Author: Bruno Mendes, Vulgo Zé Ganhão
 * Text Domain: woocommerce-custom-fees
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Definir o caminho base do plugin para uso futuro
define( 'WCCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Carregar traduções
function wccf_load_textdomain() {
    load_plugin_textdomain( 'woocommerce-custom-fees', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wccf_load_textdomain' );

// Incluir arquivos necessários
require_once WCCF_PLUGIN_DIR . 'admin/class-product-custom-fields.php';
require_once WCCF_PLUGIN_DIR . 'includes/class-product-fee-display.php';
require_once WCCF_PLUGIN_DIR . 'includes/class-cart-fee-handler.php';


