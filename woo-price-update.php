<?php

/**
 * Plugin Name: Woo Product price updater
 * Description: The plugin allows you to update prices for woocommerce products
 * Version: 1.0.0
 * Author: Oleksandr Dovgopoly
 * Text Domain: woo-price-update
 */


defined('ABSPATH') || exit;  // Exit if accessed directly

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (is_admin() && is_plugin_active('woocommerce/woocommerce.php')) {
    require_once plugin_dir_path(__FILE__) . '/includes/woo-price-update-admin.php';
    require_once plugin_dir_path(__FILE__) . '/includes/woo-price-update-ajax-handler.php';
}
