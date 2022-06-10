<?php 

// Add Shortcode [cart_count]
function get_cart_count() {
	//Check if WooCommerce is active
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		global $woocommerce;
		$cart_url = $woocommerce->cart->get_cart_url();
		$shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
		$cart_contents_count = $woocommerce->cart->cart_contents_count;
		$cart_contents = sprintf(_n('%d item', '%d items', $cart_contents_count, 'FreshySites'), $cart_contents_count);
		$cart_total = $woocommerce->cart->get_cart_total();
	  	
	  	// link for cart with icon
	  	$the_cart_data = '<a class="fs-cart-contents" href="'. $cart_url .'" title="View your shopping cart"><img class="alignnone wp-image-17718 size-full" src="/wp-content/uploads/2020/10/shopping-cart-3.svg" alt="" width="21" height="21">';
		// If more than one item in the cart, then show the qty and cost
		if ( $cart_contents_count > 0 ) {
			$the_cart_data .= ' <span class="cart-quantity">'. $cart_contents_count .'</span> <span class="cart-total">'. $cart_total .'</span>';
		}
	  	// close the cart link
		$the_cart_data .= '</a>';
	  
		return $the_cart_data;
	}
}
add_shortcode( 'cart_count', 'get_cart_count' );
/**
 * Ensure cart contents update when products are added to the cart via AJAX
 * We essentially duplicated what we added above
 */
function my_header_add_to_cart_fragment( $fragments ) {
 
	ob_start();

	$cart_url =  WC()->cart->get_cart_url();
	$cart_contents_count = WC()->cart->cart_contents_count;
	$cart_contents = sprintf(_n('%d item', '%d items', $cart_contents_count, 'FreshySites'), $cart_contents_count);
	$cart_total = WC()->cart->get_cart_total();

	?><a class="fs-cart-contents" href="<?php echo $cart_url; ?>" title="View your shopping cart"><img class="alignnone wp-image-17718 size-full" src="/wp-content/uploads/2020/10/shopping-cart-3-2.svg" alt="" width="21" height="21"><?php
	if ( $cart_contents_count > 0 ) {
	?>
		<span class="cart-quantity"><?php echo $cart_contents_count; ?></span> <span class="cart-total"><?php echo $cart_total; ?></span>
		<?php            
	}
	?></a>
	<?php

	$fragments['a.fs-cart-contents'] = ob_get_clean();
     
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );


// adding nitoces
add_action('woocommerce_before_main_content', 'add_content');
function add_content() {
	$cotnent = '<div class="wpc-custom-notice"></div>';
	echo $cotnent;
}
function wpc_cusotm_notice( $fragments ) {

	ob_start();
	$cart_url =  WC()->cart->get_cart_url();

	wc_add_notice(sprintf(__('Product has been added to your cart.').
	'<a href='.$cart_url.' class="button wc-forward" style="float:right">'. __('View Cart') .'</a>'
	), 'success');
	?>
	
	<div class="wpc-custom-notice"> <?php echo wc_print_notices() ?> </div>
<?php 
	$fragments['div.wpc-custom-notice'] = ob_get_clean();
     
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'wpc_cusotm_notice' );
