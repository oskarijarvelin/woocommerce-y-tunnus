<?php

/*
 * Plugin Name:       Y-tunnus WooCommerceen
 * Plugin URI:        https://oskarijarvelin.fi
 * Description:       Handle the basics with this plugin.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.2
 * Author:            Oskari Järvelin
 * Author URI:        https://oskarijarvelin.fi
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wcytunnus
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 */

// Add Y-tunnus field to the checkout page
add_action( 'woocommerce_after_order_notes', 'wcytunnus_add_field' );
function wcytunnus_add_field( $checkout ) {
    echo '<div id="wcytunnus_field"><h2>' . __('Y-tunnus') . '</h2>';

    woocommerce_form_field( 'wcytunnus', array(
        'type'          => 'text',
        'class'         => array( 'wcytunnus-field form-row-wide') ,
        'label'         => __( 'Y-tunnus' ),
        'placeholder'   => __( 'Yrityksen Y-tunnus' ),
    ), $checkout->get_value( 'wcytunnus' ));

    echo '</div>';
}

// Validate Y-tunnus field
add_action( 'woocommerce_checkout_update_order_meta', 'wcytunnus_update_order_meta' );
function wcytunnus_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['wcytunnus'] ) ) {
        update_post_meta( $order_id, '_wcytunnus', sanitize_text_field( $_POST['wcytunnus'] ) );
    }
}

// Display Y-tunnus on the order edit page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'wcytunnus_admin_order_meta', 10, 1 );
function wcytunnus_admin_order_meta( $order ) {
    echo '<p><strong>' . __( 'Y-tunnus', 'wcytunnus' ) . ':</strong> ' . get_post_meta( $order->id, '_wcytunnus', true ) . '</p>';
}

// Display Y-tunnus on the order email
add_filter( 'woocommerce_email_order_meta_keys', 'wcytunnus_email' );
function wcytunnus_email( $keys ) {
     $keys['Y-tunnus'] = '_wcytunnus';
     return $keys;
}
