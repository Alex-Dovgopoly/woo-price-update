<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


/**
 * Woo_Price_Update_Admin_Page Class
 *
 */

class Woo_Price_Update_Admin_Page
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'register_admin_page'));
    }

    /**
     * Register the admin page.
     */
    public function register_admin_page()
    {
        $hook_suffix = add_menu_page(
            __('Products price update', 'woo-price-update'),
            __('Woo Price update', 'woo-price-update'),
            'manage_options',
            'woo-price-update',
            array($this, 'display_admin_page'),
            'dashicons-update',
            81
        );

        add_action('admin_print_scripts-' . $hook_suffix, array($this, 'admin_page_script'));
    }

    /**
     * Display admin page content
     */
    public function display_admin_page()
    {
        $products = wc_get_products(array(
            'limit'  => -1,
            'type'   => array("simple", "variable"),
        ));
?>
        <div class="woo-price-update wrap">
            <h2><?php echo get_admin_page_title(); ?></h2>

            <?php if (0 < count($products)) : ?>

                <form id="update_product_price" enctype="multipart/form-data" method="post">
                    <div class="row">
                        <label for="new_product_price"><?php echo __('New price', 'woo-price-update'); ?></label>
                        <input type="number" name="new-price" id="new_product_price" min="0" step="0.01" required>
                    </div>
                    <div class="row">
                        <label for="product"><?php echo __('Select product', 'woo-price-update'); ?></label>
                        <select name="select-product" id="product" required>
                            <option value="" selected disabled hidden><?php echo __('Choose product', 'woo-price-update'); ?></option>
                            <?php
                            foreach ($products as $item) {
                                echo '<option value="' . $item->get_id() . '" data-prod-type="' . $item->get_type() . '">' . $item->get_title() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row variations-row"></div>
                    <input type="submit" value="<?php echo __('Save', 'woo-price-update'); ?>">
                </form>
                <p class="woo-price-update-notification"></p>

            <?php else : ?>
                <p class="woo-price-update-notification"><?php echo __('No products yet', 'woo-price-update'); ?></p>
            <?php endif; ?>
        </div>
<?php
    }

    /**
     * JavaScript for admin page
     */
    function admin_page_script()
    {
        wp_enqueue_script(
            'woo_price_update_script',
            plugin_dir_url(__FILE__) . '../assets/js/script.js',
            array('jquery')
        );
    }
}

new Woo_Price_Update_Admin_Page();
