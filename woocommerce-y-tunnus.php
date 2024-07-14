<?php

/*
 * Plugin Name:       Y-tunnus WooCommerceen
 * Plugin URI:        https://oskarijarvelin.fi
 * Description:
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
add_action( 'woocommerce_billing_fields', 'wcytunnus_add_field' );
function wcytunnus_add_field( $fields ) {

    $fields[ 'wcytunnus' ]   = array(
		'label'        => 'Y-tunnus',
		'required'     => false,
		'class'        => array( 'form-row-wide' ),
		'priority'     => 30,
	);

	return $fields;
}

// Add Y-tunnus field to customer details
add_filter( 'woocommerce_customer_meta_fields', 'wcytunnus_customer_field' );
function wcytunnus_customer_field( $admin_fields ) {

	$admin_fields[ 'billing' ][ 'fields' ][ 'wcytunnus' ] = array(
		'label' => 'Y-tunnus',
		'description' => 'Yrityksen Y-tunnus',
	);

	return $admin_fields;
}

// Save Y-tunnus field to the order meta
add_action('woocommerce_checkout_update_order_meta','wcytunnus_update_order_meta');
function wcytunnus_update_order_meta($order_id) {
    if (!empty($_POST['billing']['wcytunnus'])) {
        update_post_meta($order_id, 'wcytunnus', sanitize_text_field($_POST['billing']['wcytunnus']));
    }
}

// Display Y-tunnus field on the order edit page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'wcytunnus_admin_order_meta', 10, 1 );
function wcytunnus_admin_order_meta( $order ) {
	?>
    <div class="address">
        <p><strong>Y-tunnus:</strong><?php echo $order->get_meta( 'wcytunnus' ); ?>
        </p>
    </div>
    <div class="edit_address">
        <?php
            woocommerce_wp_text_input( array(
                'id' => 'wcytunnus',
                'label' => 'Y-tunnus:',
                'value' => $order->get_meta( 'wcytunnus' ),
                'wrapper_class' => 'form-field-wide'
            ) );
        ?>
    </div>
	<?php
}

// Process and save the Y-tunnus field
add_action( 'woocommerce_process_shop_order_meta', 'misha_save_general_details' );
function misha_save_general_details( $order_id ){
	$order = wc_get_order( $order_id );
	$order->update_meta_data( 'wcytunnus', wc_clean( $_POST[ 'wcytunnus' ] ) );
	$order->save();
}

// Load Ajax custom field data as customer billing/shipping address fields
add_filter( 'woocommerce_ajax_get_customer_details' , 'add_custom_fields_to_ajax_customer_details', 10, 3 );
function add_custom_fields_to_ajax_customer_details( $data, $customer, $user_id ) {
    $data['wcytunnus'] = $customer->get_meta('wcytunnus');
    return $data;
}

// Display Y-tunnus on the order email
add_filter( 'woocommerce_email_order_meta_keys', 'wcytunnus_email' );
function wcytunnus_email( $keys ) {
    $keys['Y-tunnus'] = 'wcytunnus';
    return $keys;
}
