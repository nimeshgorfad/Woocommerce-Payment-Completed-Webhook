<?php 
/*
 * Plugin Name: Woocommerce Payment Completed Webhook
 * Plugin URI:  https://www.freelancer.in/u/nimeshgorfad
 * Description: webhook call when payment completed in woocommerce
 * Version: 1.0.0
 * Author: Nimesh Gorfad
 * Author URI: https://www.freelancer.in/u/nimeshgorfad
 * Text Domain: woopcw
 * Requires at least: 6.1
 * Requires PHP: 7.3 
*/  

define( 'NKG_WEBHOOK_URL', 'YOUR_WEB_HOOK_URL');

add_action( 'woocommerce_payment_complete', 'nkg_so_payment_complete' ); 
//add_action( 'woocommerce_update_order', 'nkg_so_payment_complete', 10, 1 );
// call on payment completed
function nkg_so_payment_complete( $order_id ){
	$url = NKG_WEBHOOK_URL;
	 	
   $order_json = nkg_get_order_json( $order_id );  
 
	// Webhook Call 
	wp_remote_post( $url, array(
	 'method' => 'POST',
	 'headers'     => array('Content-Type' => 'application/json; charset=utf-8'), 	
		'data_format' => 'body',	 
	 'body' => $order_json,
	 )
	); 
}



// Function to get order data in JSON format
function nkg_get_order_json($order_id) {
	
    // Get the order object
    $order = wc_get_order($order_id);

    if (!$order) {
        return json_encode(["error" => "Order not found."]);
    }

    // Build the order data array
    $order_data = [
        "id" => $order->get_id(),
        "parent_id" => $order->get_parent_id(),
        "status" => $order->get_status(),
        "currency" => $order->get_currency(),
        "version" => $order->get_version(),
        "prices_include_tax" => $order->get_prices_include_tax(),
        "date_created" => $order->get_date_created()->date('c'),
        "date_modified" => $order->get_date_modified()->date('c'),
        "discount_total" => $order->get_discount_total(),
        "discount_tax" => $order->get_discount_tax(),
        "shipping_total" => $order->get_shipping_total(),
        "shipping_tax" => $order->get_shipping_tax(),
        "cart_tax" => $order->get_cart_tax(),
        "total" => $order->get_total(),
        "total_tax" => $order->get_total_tax(),
        "customer_id" => $order->get_customer_id(),
        "order_key" => $order->get_order_key(),
        "billing" => $order->get_address('billing'),
        "shipping" => $order->get_address('shipping'),
        "payment_method" => $order->get_payment_method(),
        "payment_method_title" => $order->get_payment_method_title(),
        "transaction_id" => $order->get_transaction_id(),
        "customer_ip_address" => $order->get_customer_ip_address(),
        "customer_user_agent" => $order->get_customer_user_agent(),
        "created_via" => $order->get_created_via(),
        "customer_note" => $order->get_customer_note(),
        "date_completed" => $order->get_date_completed() ? $order->get_date_completed()->date('c') : null,
        "date_paid" => $order->get_date_paid() ? $order->get_date_paid()->date('c') : null,
        "cart_hash" => $order->get_cart_hash(),
        "meta_data" => $order->get_meta_data(),
        "line_items" => [],
        "tax_lines" => [],
        "shipping_lines" => [],
        "fee_lines" => [],
        "coupon_lines" => [],
        "refunds" => [],
        "_links" => [
            "self" => [
                ["href" => get_rest_url(null, "/wc/v3/orders/{$order_id}")]
            ],
            "collection" => [
                ["href" => get_rest_url(null, "/wc/v3/orders")]
            ]
        ]
    ];

    // Add line items
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        $order_data['line_items'][] = [
            "id" => $item_id,
            "name" => $item->get_name(),
            "product_id" => $item->get_product_id(),
            "variation_id" => $item->get_variation_id(),
            "quantity" => $item->get_quantity(),
            "tax_class" => $item->get_tax_class(),
            "subtotal" => $item->get_subtotal(),
            "subtotal_tax" => $item->get_subtotal_tax(),
            "total" => $item->get_total(),
            "total_tax" => $item->get_total_tax(),
            "taxes" => $item->get_taxes(),
            "meta_data" => $item->get_meta_data(),
            "sku" => $product ? $product->get_sku() : '',
            "price" => $product ? $product->get_price() : 0
        ];
    }

    // Add tax lines
    foreach ($order->get_taxes() as $tax_id => $tax) {
        $order_data['tax_lines'][] = [
            "id" => $tax_id,
            "rate_code" => $tax->get_rate_code(),
            "rate_id" => $tax->get_rate_id(),
            "label" => $tax->get_label(),
            "compound" => $tax->get_compound(),
            "tax_total" => $tax->get_tax_total(),
            "shipping_tax_total" => $tax->get_shipping_tax_total(),
            "meta_data" => $tax->get_meta_data()
        ];
    }

    // Add shipping lines
    foreach ($order->get_shipping_methods() as $shipping_id => $shipping) {
        $order_data['shipping_lines'][] = [
            "id" => $shipping_id,
            "method_title" => $shipping->get_method_title(),
            "method_id" => $shipping->get_method_id(),
            "total" => $shipping->get_total(),
            "total_tax" => $shipping->get_total_tax(),
            "taxes" => $shipping->get_taxes(),
            "meta_data" => $shipping->get_meta_data()
        ];
    }

    // Add coupon lines
    foreach ($order->get_items('coupon') as $coupon_id => $coupon) {
        $order_data['coupon_lines'][] = [
            "id" => $coupon_id,
            "code" => $coupon->get_code(),
            "discount" => $coupon->get_discount(),
            "discount_tax" => $coupon->get_discount_tax(),
            "meta_data" => $coupon->get_meta_data()
        ];
    }

    // Add fee lines
    foreach ($order->get_items('fee') as $fee_id => $fee) {
        $order_data['fee_lines'][] = [
            "id" => $fee_id,
            "name" => $fee->get_name(),
            "tax_class" => $fee->get_tax_class(),
            "tax_status" => $fee->get_tax_status(),
            "amount" => $fee->get_amount(),
            "total" => $fee->get_total(),
            "total_tax" => $fee->get_total_tax(),
            "taxes" => $fee->get_taxes(),
            "meta_data" => $fee->get_meta_data()
        ];
    }

    // Add refunds
    foreach ($order->get_refunds() as $refund_id => $refund) {
        $order_data['refunds'][] = [
            "id" => $refund_id,
            "reason" => $refund->get_reason(),
            "total" => $refund->get_amount(),
            "date" => $refund->get_date_created()->date('c')
        ];
    }

    return json_encode($order_data);
   // return json_encode($order_data, JSON_PRETTY_PRINT);
}
