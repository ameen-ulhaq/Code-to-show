<?php 

// Adding a button in each product list/ shop page 
add_action('woocommerce_after_shop_loop_item', 'add_a_custom_button', 5 );
function add_a_custom_button() {
    global $product;

    // Not for variable and grouped products that doesn't have an "add to cart" button
    // if( $product->is_type('variable') || $product->is_type('grouped') ) return;

    // Output the custom button linked to the product
    echo '<a class="button view-button" href="' . esc_attr( $product->get_permalink() ) . '">' . __('View Details') . '</a>';
}

/**
 * Add a custom product data tab
 */
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
function woo_new_product_tab( $tabs ) {
	//Remove tab
	unset($tabs['additional_information']);
	// Rename the reviews tab\
	$tabs['reviews']['title'] = __( 'Product Reviews' );
	// Adds the new tab
	$tabs['faq'] = array(
		'title' 	=> __( 'FAQ', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'woo_faq_tab_content'
	);

	// Reordring tabs/
	$tabs['description']['priority'] = 5;
	$tabs['reviews']['priority'] = 10;
	$tabs['faq']['priority'] = 15;
	$tabs['ingredients']['priority'] = 20;
	$tabs['direction_to_use']['priority'] = 25;
	$tabs['product_videos']['priority'] = 30;
	return $tabs;
}
// custom Tab content using  ACF field
function woo_faq_tab_content() {
	echo'<div class="faq-tab-content" id="faq-tab-content">';

	if( get_field('product_faq') ):
	?>			
		<?php the_field('product_faq');?>
	<?php		
	endif;		
	
	echo'</div>';	
}

// chaging product page thumbnail to points
add_filter('woocommerce_single_product_carousel_options', 'woocommerce_single_product_carousel_options_custom');
function woocommerce_single_product_carousel_options_custom($args){
    $args['controlNav'] = TRUE;
    return $args;
}

// Change WooCommerce "Related products" text
add_filter( 'gettext', 'change_you_may_also_like' );
function change_you_may_also_like( $translated ) {
   $translated = str_replace( 'Related products', 'customers also bought', $translated );
   return $translated;
}
//
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_thumbnails', 20 );

// Adding back button
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
	$bbtn = '';
	 if( is_product() ) {
		$bbtn = '<button type="button" class="nav-back-btn" onclick="history.back();">back</button>';
	 }

    return array(
            'delimiter'   => ' &gt; ',
            'wrap_before' => $bbtn.' <nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
		);
}

// chaing price location
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_before_single_variation', 'woocommerce_template_single_price', 25 );


// adding quantity button
//plus button
add_action( 'woocommerce_before_add_to_cart_quantity', 'bbloomer_display_quantity_plus' );
function bbloomer_display_quantity_plus() {
   echo '<button type="button" class="minus" ><i class="fas fa-minus"></i></button>';
}
  //minus button
add_action( 'woocommerce_after_add_to_cart_quantity', 'bbloomer_display_quantity_minus' );
function bbloomer_display_quantity_minus() {
   echo '<button type="button" class="plus" ><i class="fas fa-plus"></i></button>';
}

add_action( 'wp_footer', 'bbloomer_add_cart_quantity_plus_minus' );
function bbloomer_add_cart_quantity_plus_minus() {
   // Only run this on the single product page
   if ( ! is_product() ) return;
   ?>
      <script type="text/javascript">
      jQuery(document).ready(function($){   
         $('form.cart').on( 'click', 'button.plus, button.minus', function() {
            // Get current quantity values
            var qty = $( this ).closest( 'form.cart' ).find( '.qty' );
            var val   = parseFloat(qty.val());
            var max = parseFloat(qty.attr( 'max' ));
            var min = parseFloat(qty.attr( 'min' ));
            var step = parseFloat(qty.attr( 'step' ));
            // Change the value if plus or minus
            if ( $( this ).is( '.plus' ) ) {
               if ( max && ( max <= val ) ) {
                  qty.val( max );
               } else {
                  qty.val( val + step );
               }
            } else {
               if ( min && ( min >= val ) ) {
                  qty.val( min );
               } else if ( val > 1 ) {
                  qty.val( val - step );
               }
            }
         });
      });  
      </script>
   <?php
}

/**
 * Change the custom dropdown  "Choose an option" text on the front end
 */
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'bt_dropdown_choice' );
function bt_dropdown_choice( $args ){
	if( is_product() ) {
			$args['show_option_none'] = "Please choose an option"; // Change your text here
	}  
	return $args;    
}

// Remove Descrtion heading
add_filter( 'woocommerce_product_description_heading', '__return_null' );

// Changing shop page title
add_filter( 'woocommerce_page_title', 'woo_shop_page_title');
function woo_shop_page_title( $page_title ) {
	$post_id = get_option( 'woocommerce_shop_page_id' );
	echo get_field('shop_page_title', $post_id) ;
}
// Chaging featured image woocoomerce product page
add_filter('woocommerce_single_product_image_thumbnail_html', 'remove_featured_image', 10, 2);
function remove_featured_image($html, $attachment_id ) {
    global $post, $product;

    $featured_image = get_post_thumbnail_id( $post->ID );

    if ( $attachment_id == $featured_image )
        $html = '<img width="600" height="493" src="https://godefroybeauty.gowiththrive.net/wp-content/uploads/2020/10/Group-7453.png" class="wp-post-image">';

    return $html;
}

// Showing all categories before shop 
function getCat(){
   $taxonomy     = 'product_cat';
 $orderby      = 'name';  
 $show_count   = 0;      // 1 for yes, 0 for no
 $pad_counts   = 0;      // 1 for yes, 0 for no
 $hierarchical = 1;      // 1 for yes, 0 for no  
 $title        = '';  
 $empty        = 0;

 $args = array(
        'taxonomy'     => $taxonomy,
        'orderby'      => $orderby,
        'show_count'   => $show_count,
        'pad_counts'   => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li'     => $title,
        'hide_empty'   => $empty
 );
 $html = '';
 $html.= "<div class='cat-filter'><h3>SHOP BY CATEGORY</h3><ul class='cat-dropdown'>";
$all_categories = get_categories( $args );
foreach ($all_categories as $cat) {
   if($cat->category_parent == 0) {
       $category_id = $cat->term_id;       
       $html .='<li><a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .' Products</a>'; 

       $args2 = array(
               'taxonomy'     => $taxonomy,
               'child_of'     => 0,
               'parent'       => $category_id,
               'orderby'      => $orderby,
               'show_count'   => $show_count,
               'pad_counts'   => $pad_counts,
               'hierarchical' => $hierarchical,
               'title_li'     => $title,
               'hide_empty'   => $empty
       );
       $sub_cats = get_categories( $args2 );
       if($sub_cats) {
          $html .= '<ul>';
           foreach($sub_cats as $sub_category) {
               $html .= '<li><a href="'.get_term_link($sub_category->slug, 'product_cat').'">'.$sub_category->name.'</a></li>';
           }
          $html .= '</ul></li>';
       }
   }       
}
 $html.= "</ul></div>";

echo $html;
}
add_action( 'woocommerce_before_shop_loop', 'getCat', 100 );


function woocommerce_product_category() {
	global $product;
	$id = $product->get_id();
	$term_list = wp_get_post_terms($id,'product_cat',array('fields'=>'ids'));
	$cat_id = (int)$term_list[0];
	$cat_id2 = (int)$term_list[1];

	echo ($id == 1120 ? '<span class="cat-name">'.get_term( $cat_id2 )->name.'</span>' : '<span class="cat-name">'.get_term( $cat_id )->name.'</span>');
}
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_product_category', 100 );


// Get Product ID
  
$product->get_id();
  
// Get Product General Info
  
$product->get_type();
$product->get_name();
$product->get_slug();
$product->get_date_created();
$product->get_date_modified();
$product->get_status();
$product->get_featured();
$product->get_catalog_visibility();
$product->get_description();
$product->get_short_description();
$product->get_sku();
$product->get_menu_order();
$product->get_virtual();
get_permalink( $product->get_id() );
  
// Get Product Prices
  
$product->get_price();
$product->get_regular_price();
$product->get_sale_price();
$product->get_date_on_sale_from();
$product->get_date_on_sale_to();
$product->get_total_sales();
  
// Get Product Tax, Shipping & Stock
  
$product->get_tax_status();
$product->get_tax_class();
$product->get_manage_stock();
$product->get_stock_quantity();
$product->get_stock_status();
$product->get_backorders();
$product->get_sold_individually();
$product->get_purchase_note();
$product->get_shipping_class_id();
  
// Get Product Dimensions
  
$product->get_weight();
$product->get_length();
$product->get_width();
$product->get_height();
$product->get_dimensions();
  
// Get Linked Products
  
$product->get_upsell_ids();
$product->get_cross_sell_ids();
$product->get_parent_id();
  
// Get Product Variations and Attributes
 
$product->get_children(); // get variations
$product->get_attributes();
$product->get_default_attributes();
$product->get_attribute( 'attributeid' ); //get specific attribute value
  
// Get Product Taxonomies
  
$product->get_categories();
$product->get_category_ids();
$product->get_tag_ids();
  
// Get Product Downloads
  
$product->get_downloads();
$product->get_download_expiry();
$product->get_downloadable();
$product->get_download_limit();
  
// Get Product Images
  
$product->get_image_id();
$product->get_image();
$product->get_gallery_image_ids();
  
// Get Product Reviews
  
$product->get_reviews_allowed();
$product->get_rating_counts();
$product->get_average_rating();
$product->get_review_count();

// Custom End points Woocomemrce
function wpb_woo_endpoint_title( $title, $id ) {
	
	if ( is_wc_endpoint_url( 'downloads' ) && in_the_loop() ) { // add your endpoint urls
		$title = "Download"; // change your entry-title
	}
	elseif ( is_wc_endpoint_url( 'orders' ) && in_the_loop() ) {
		$title = "My Orders";
	}
	elseif ( is_wc_endpoint_url( 'quotes' ) && in_the_loop() ) {
		$title = "My Quotes";
	}
	elseif ( is_wc_endpoint_url( 'edit-address' ) && in_the_loop() ) {
		$title = "My Address";
	}
	elseif ( is_wc_endpoint_url( 'edit-account' ) && in_the_loop() ) {
		$title = "Change My Details";
	}
	elseif ( is_wc_endpoint_url( 'lost-password' ) && in_the_loop() ) {
		$title = "Lost Password";
	}
	return $title;
}
add_filter( 'the_title', 'wpb_woo_endpoint_title', 10, 2 );

// Show wooomerce title
function woo_page_shortcode(){
    return get_the_title();
}
add_shortcode('woo_page_shortcode','woo_page_shortcode');   

// =====Checkout =====
// Comment label changes
function md_custom_woocommerce_checkout_fields( $fields ) 
{
    $fields['order']['order_comments']['placeholder'] = 'Add your note about modifications';
    $fields['order']['order_comments']['label'] = 'Modifications';

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'md_custom_woocommerce_checkout_fields' );

// Custom fields
function shipping_quote_checkbox_fields( $checkout ) {

    echo '<div class="custom-checkout-fields"><h6>'.__('Shipping Quote: ').'</h6><p>'.__('Check this box to request shipping charges from J&K.').'</p>';
    woocommerce_form_field( 'shipping_checkbox', array(
        'type'          => 'checkbox',
        'label'         => __('Provide Separate Shipping Quote.'),
        'required'  => false,
    ), $checkout->get_value( 'shipping_checkbox' ));

    echo '</div>';

}
add_action('woocommerce_after_order_notes', 'shipping_quote_checkbox_fields');

// saving
add_action('woocommerce_checkout_update_order_meta', 'shipping_checkout_order_meta');
function shipping_checkout_order_meta( $order_id ) {
if ($_POST['shipping_checkbox']) update_post_meta( $order_id, 'shipping_checkbox', esc_attr($_POST['shipping_checkbox']));
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'shipping_checkout_field_display_admin_order_meta', 10, 1 );
function shipping_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Shipping Quote Request: ').'</strong> <br/>' . (get_post_meta( $order->get_id(), 'shipping_checkbox') ? 'Yes': 'No')  . '</p>';
}
// =====Checkout =====


// ===============Cart=====================
// Display a checkbox field after billing fields
add_action( 'woocommerce_after_cart_table', 'add_custom_checkout_checkbox', 20 );
add_action( 'woocommerce_after_checkout_billing_form', 'add_custom_checkout_checkbox', 20 );
function add_custom_checkout_checkbox(){

    if( WC()->session->get('custom_fee') ){
        $checked = 1;
    }
	else {
		$checked = 0;
	}

	if( WC()->session->get('custom_quantity')){
		$qty = WC()->session->get('custom_quantity');
    }
	else {
		$qty = 1;
	}

	echo '<div class="custom-cart-fields"><h5> '. __('Assembly Charges') .' </h5>';	
    // woocommerce_form_field( 'custom_fee', array(
    //     'type'  => 'checkbox',
    //     'label' => __('Cabinet assembly charges. $15/Cabinet'),
    //     'class' => array( 'form-row-wide form-one' ),
    // ), $checked );

	echo ' <label class="checkbox">';
		echo '<input type="checkbox" class="input-checkbox " name="custom_fee" id="custom_fee" value=" '. WC()->session->get('custom_fee')  .' " '. (WC()->session->get('custom_fee') ? 'checked' : '')  .'>Cabinet assembly charges. $15/Cabinet';
	echo '</label>';

	woocommerce_form_field( 'custom_quantity', array(
        'type'  => 'number',
        'label' => __('Enter numbers of quantity you want o'),
        'class' => array( 'form-row-wide form-two' ),
    ), $qty );

	echo '</div>';
	// echo 'C: '. WC()->session->get('custom_fee');
	// echo '<br>Q: '. WC()->session->get('custom_quantity');
}

/*Remove "(optional)" label on checkbox field*/
add_filter( 'woocommerce_form_field' , 'remove_order_comments_optional_fields_label', 10, 4 );
function remove_order_comments_optional_fields_label( $field, $key, $args, $value ) {
    // Only on checkout page for Order notes field
    if( 'custom_fee' === $key && ( is_checkout() || is_cart() ) ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
    return $field;
}


// Ajax / jQuery script
add_action( 'wp_footer', 'custom_fee_script' );
function custom_fee_script() {
    // On checkoutpage
    if( ( ( is_cart() || is_checkout() ) && ! is_wc_endpoint_url() ) ) :		
    ?>

    <script type="text/javascript">

	
    jQuery( function($){
        if (typeof woocommerce_params === 'undefined')
            return false;

        console.log('defined');	
		// for checkbox
		// $( 'body' ).trigger( 'wc_update_cart' ); 
			
		$( document ).on('click', "#custom_fee", function(){
		// $('input[name=custom_fee]').click( function(){

			console.log("set 1");
			var fee = $(this).prop('checked') === true ? '1' : '';

			console.log(fee);
			$.ajax({
				type: 'POST',
				url: woocommerce_params.ajax_url,
				data: {
					'action': 'custom_fee',
					'custom_fee': fee,
				},
				success: function (result) {
					$('body').trigger('update_checkout');
					$( 'body' ).trigger( 'wc_update_cart' ); 
					console.log(result);
				},
				error: function(request, status, error) {
					console.log(request.responseText);
				}
			});
		});	
		// for qty
		$( document ).on('blur', "input[name=custom_quantity]", function(){
			console.log("set 2");

			if( $('input[name=custom_fee]').prop('checked') === true ) {

				var custom_quantity = $(this).val();

				console.log(custom_quantity);
				$.ajax({
					type: 'POST',
					url: woocommerce_params.ajax_url,
					data: {
						'action': 'custom_quantity',
						'custom_quantity': custom_quantity,
					},
					success: function (result) {
						$('body').trigger('update_checkout');
						$( 'body' ).trigger( 'wc_update_cart' ); 
						console.log(result);
					},
					error: function(request, status, error) {
						console.log(request.responseText);
					}
				});
			}else{
				alert('Please select above checkbox!');
				$(this).val(1);
			}
		}); 
    });

    </script>
    <?php
    endif;
}

// Get the ajax request and set value to WC session
add_action( 'wp_ajax_custom_fee', 'get_ajax_custom_fee' );
add_action( 'wp_ajax_nopriv_custom_fee', 'get_ajax_custom_fee' );
function get_ajax_custom_fee() {
    if ( isset($_POST['custom_fee']) ) {
        WC()->session->set('custom_fee', ($_POST['custom_fee'] ? '1' : '0') );
       
        echo WC()->session->get('custom_fee');
    }
    die();
}
add_action( 'wp_ajax_custom_quantity', 'get_ajax_custom_quantity' );
add_action( 'wp_ajax_nopriv_custom_quantity', 'get_ajax_custom_quantity' );
function get_ajax_custom_quantity() {
	if ( isset($_POST['custom_quantity']) ) {
        WC()->session->set('custom_quantity', $_POST['custom_quantity'] );

        echo WC()->session->get('custom_quantity');
    }
    die();
}

// Add / Remove a custom fee
add_action( 'woocommerce_cart_calculate_fees', 'add_remove_custom_fee', 10, 1 );
function add_remove_custom_fee( $cart ) {
    // Only on checkout
    if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) )
        return;

	if(WC()->session->get('custom_quantity')) {
		$qty = WC()->session->get('custom_quantity');
	} else {
		$qty = 1;
	}
	$fee_amount = 15;

	$fee_amount = $fee_amount * $qty;
    

    // $categories   = array('cabinets');

    // foreach ( WC()->cart->get_cart() as $cart_item ) {
    //     // Check for product categories
    //     if ( has_term( $categories, 'product_cat', $cart_item['product_id'] ) ) {
    //         $fee_amount += 25;
    //     }
    // }

    if( WC()->session->get('custom_fee') )
        $cart->add_fee( __( 'Pre-Assembled', 'woocommerce'), $fee_amount );
}


// Custom mini Cart shortcode
function custom_mini_cart() { 
    echo '<a href="#" class="dropdown-back" data-toggle="dropdown"> ';
    echo '<i class="fa fa-shopping-cart" aria-hidden="true"></i>';
    echo '<div class="basket-item-count" style="display: inline;">';
        echo '<span class="cart-items-count count">';
            echo WC()->cart->get_cart_contents_count();
        echo '</span>';
    echo '</div>';
    echo '</a>';
    echo '<ul class="dropdown-menu dropdown-menu-mini-cart">';
        echo '<li> <div class="widget_shopping_cart_content">';
                  woocommerce_mini_cart();
            echo '</div></li></ul>';

      }
add_shortcode( 'custom-techno-mini-cart', 'custom_mini_cart' );

/**
 * Remove the breadcrumbs 
 */
add_action( 'init', 'woo_remove_wc_breadcrumbs' );
function woo_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}

// Set woocommerce quanity input
function woocommerce_quantity_input_min_callback( $min, $product ) {
	$min = 1;  
	return $min;
}
add_filter( 'woocommerce_quantity_input_min', 'woocommerce_quantity_input_min_callback', 10, 2 );
