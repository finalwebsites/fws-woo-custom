<?php
/**
 * Plugin Name:			Custom WooCommerce
 * Plugin URI:			https://www.finalwebsites.nl/
 * Description:			Verschillende aanpassingen voor WooCommerce
 * Version:				1.0.0
 * Author:				Olaf Lederer
 * Author URI:			https://www.finalwebsites.nl
 * Requires at least:	5.4
 * Tested up to:		5.4.3
 *
 * Text Domain: fwstextdomain
 * Domain Path: /languages/
 *
 */

define('FWS_WOO_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ));
include_once FWS_WOO_PLUGIN_PATH.'/options.php';

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'fwstextdomain', false, FWS_WOO_PLUGIN_PATH . '/languages/' );
});

add_filter( 'woocommerce_locate_template', 'fws_plugin_woocommerce_locate_template', 10, 3 );
function fws_plugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
	global $woocommerce;

	$two_col = get_option('fws_custom_woo_two_col_checkout');
	if ($template_name == 'checkout/form-checkout.php' && $two_col == false) return $template;
	
	$cart_coupon = get_option('fws_custom_woo_hide_coupon_checkout');
	if ($template_name == 'checkout/form-coupon.php' && $cart_coupon == false) return $template;
	
	$info_cart = get_option('fws_custom_woo_hide_shipment_info_cartpage');
	if ($template_name == 'cart/cart-shipping.php' && $info_cart == false) return $template;
	
	$move_coupon = get_option('fws_custom_woo_move_coupon_cart_page');
	if ($template_name == 'cart/cart.php' && $move_coupon == false) return $template;
	
	$_template = $template;
	if ( ! $template_path ) $template_path = $woocommerce->template_url;
	$plugin_path  = FWS_WOO_PLUGIN_PATH . '/woocommerce/';
	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array( $template_path . $template_name, $template_name )
	);
	// Modification: Get the template from this plugin, if it exists
	if ( ! $template && file_exists( $plugin_path . $template_name ) )
	$template = $plugin_path . $template_name;
	// Use default template
	if ( ! $template )
	$template = $_template;
	return $template;
}

add_action('wp_enqueue_scripts', 'fws_load_style_scripts', 100);
function fws_load_style_scripts() {
	wp_enqueue_style( 'fws-woocustom-style', plugin_dir_url(__FILE__).'style.css', array() );
}


add_filter( 'request', function ( $query_vars ) {
	if (get_option('fws_custom_woo_search_products')) {
		if( !empty( $_GET['s'] ) ) {
			$query_vars['post_type'] = 'product';
		}
	}
    return $query_vars;
});


function wc_hide_shipping_when_free_is_available_keep_local( $rates, $package ) {	
    $new_rates = array();
    foreach ( $rates as $rate_id => $rate ) {
        if ( 'free_shipping' === $rate->method_id ) {
            $new_rates[ $rate_id ] = $rate;
            break;
        }
    }
    if ( ! empty( $new_rates ) ) {
        foreach ( $rates as $rate_id => $rate ) {
            if ('local_pickup' === $rate->method_id ) {
                $new_rates[ $rate_id ] = $rate;
                break;
            }
        }
        return $new_rates;
    }
    return $rates;
}

add_action( 'after_setup_theme', function() {
	if (get_option('fws_custom_woo_move_categorie_descriptions')) {
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
		add_action( 'woocommerce_after_shop_loop', 'woocommerce_taxonomy_archive_description', 100 );
	}
	if (get_option('fws_custom_woo_hide_titels_for_tabs')) {
		add_filter('woocommerce_product_description_heading', '__return_null');
		add_filter('woocommerce_product_additional_information_heading', '__return_null');
	}
}, 10);

add_action('woocommerce_before_cart_collaterals', function() {
	if (false == get_option('fws_custom_woo_move_coupon_cart_page')) return;
	if ( wc_coupons_enabled() ) { ?>
		<form class="woocommerce-coupon-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<div class="coupon-custom">
				<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon', 'woocommerce' ); ?>" /> 
				<button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply', 'woocommerce' ); ?></button>
				<?php do_action( 'woocommerce_cart_coupon' ); ?>
			</div>
		</form>
	<?php }
}, 9);


function fws_remove_product_page_skus( $enabled ) {
	$hide_sku = get_option('fws_custom_woo_hide_sku');
    if ( ! is_admin() && is_product() && $hide_sku ) {
        return false;
    }
    return $enabled;
}
add_filter( 'wc_product_sku_enabled', 'fws_remove_product_page_skus' );

function fws_add_stock_status_info() {
	global $product;
	if (false == get_option('fws_custom_woo_alt_stock_messages')) return;
	$stock_status = $product->get_stock_status();
	switch ($stock_status) {
		case 'instock':
		$status = __('Uit voorraad leverbaar', 'fwstextdomain' );
		break;
		case 'onbackorder':
		$status = __('Levertijd 3-5 werkdagen', 'fwstextdomain' );
		break;
		case 'outofstock':
		$status = __('Niet meer leverbaar', 'fwstextdomain' );
		break;
		default:
		$status = '';
		break;
	}
	if ($status) echo '
	<div class="voorraad">'.$status.'</div>';
}
add_action('woocommerce_single_product_summary', 'fws_add_stock_status_info', 30);




add_action( 'woocommerce_before_shop_loop_item_title', 'fws_new_badge_shop_page', 3 );       
function fws_new_badge_shop_page() {
   global $product;
   $days = get_option('fws_custom_woo_new_product_badge');
   if ((int)$days == 0) return;
   $created = strtotime( $product->get_date_created() );
   if ( ( time() - ( 60 * 60 * 24 * $days ) ) < $created ) {
      echo '<span class="itsnew onsale">' . esc_html__( 'Nieuw', 'fwstextdomain' ) . '</span>';
   }
}

// extra
add_action( 'init', 'fws_remove_wc_breadcrumbs' );
function fws_remove_wc_breadcrumbs() {
	if (get_option('fws_custom_woo_remove_breadcrumbs')) {
		if ('Storefront' == get_current_theme()) {
			remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
		} else {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
		}
	}
	if (get_option('fws_custom_woo_hide_shipments_when_free')) {
		add_filter( 'woocommerce_package_rates', 'wc_hide_shipping_when_free_is_available_keep_local', 10, 2 );
	}
}



