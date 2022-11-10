<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


/**
 * AJAX handler. Get product variations table by parent ID
 */
add_action('wp_ajax_get_product_variations', 'get_product_variations_table');

function get_product_variations_table()
{
    $prod_id = (float) $_POST['prod_id'];

    $product = new WC_Product_Variable($prod_id);
    $variations = $product->get_available_variations();

    $respone = '';

    if ($variations) {
        $formatted_attrs = '';

        foreach ($product->get_variation_attributes() as $taxonomy => $terms_slug) {
            $formatted_attrs .= '<th>' . wc_attribute_label($taxonomy, $product) . '</th>';
        }

        $respone = '<table>';
        $respone .= '<thead><tr><th>Is Update?</th>' . $formatted_attrs . '<th>Current price</th></tr></thead>';
        $respone .= '<tbody>';

        foreach ($variations as $variation) {
            $variation_price = $variation['price_html'];

            if ($variation_price == '') {
                $variation_price = $product->get_price_html();
            }

            $respone .= '<tr><td><input type="checkbox" name="variation[]" value="' . $variation['variation_id'] . '"/></td>';

            foreach ($variation['attributes'] as $variation_attribute => $term_slug) {
                $attr_taxonomy = str_replace('attribute_', '', $variation_attribute);
                $term_name = 'Any';

                if ($term_slug !== '') {
                    $term_name = ($term = get_term_by('slug', $term_slug, $attr_taxonomy)) ? $term->name : $term_slug;
                }

                $respone .= '<td>' . $term_name . '</td>';
            }

            $respone .= '<td>' . $variation_price . '</td></tr>';
        }

        $respone .= '</tbody></table>';
    } else {
        $respone = '<p>' . __('No variations yet', 'woo-price-update') . '</p>';
    }

    echo $respone;

    wp_die(); // this is required to terminate immediately and return a proper response
}


/**
 * AJAX handler. Update price form submit
 */
add_action('wp_ajax_update_price_submit', 'update_price_form_submit');

function update_price_form_submit()
{
    $rate = 3;

    $new_price = (float) $_POST['new-price'] * $rate;
    $prod_id = (float) $_POST['select-product'];
    $variations = $_POST['variation'];

    if (!empty($variations)) {
        // Update variations
        $i = count($variations);
        for ($j = 0; $j < $i; $j++) {
            $variation_id = $variations[$j];
            $variation = wc_get_product_object('variation', $variation_id);
            $variation->set_props(
                array(
                    'regular_price' => $new_price,
                    'sale_price' => '',
                )
            );
            $variation->save();
        }
    } else {
        // Single product update
        update_product_price_by_id($prod_id, $new_price);
    }

    wp_die(); // this is required to terminate immediately and return a proper response
}


/**
 *  Update product price helper function
 */
function update_product_price_by_id($prod_id, $new_price)
{
    $product = new WC_Product($prod_id);

    // Set product prices
    if ($product->is_on_sale()) {
        if ($new_price >= $product->get_regular_price()) {
            $product->set_sale_price('');
            $product->set_regular_price($new_price);
        } else {
            $product->set_sale_price($new_price);
        }
    } else {
        $product->set_regular_price($new_price);
    }

    // Save data and update caches
    $product->save();
}
