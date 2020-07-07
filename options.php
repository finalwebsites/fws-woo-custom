<?php

/*
 * This option page is based on the class script from Hugh Lashbrooke
 * https://gist.github.com/hlashbrooke/9267467
*/

if ( ! defined( 'ABSPATH' ) ) exit;


class Custom_Woo_Plugin_Settings {

	private $file;
	private $settings_base;
	private $settings;

	public function __construct( $file ) {
		$this->file = $file;
		$this->settings_base = 'fws_custom_woo_';
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_init' , array( $this, 'register_settings' ) );
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );

	}

	public function init() {
		$this->settings = $this->settings_fields();

	}

	public function add_menu_item() {
		$page = add_options_page(
			__( 'Custom WooCommerce Settings', 'fwstextdomain' ),
			__( 'Custom WooCommerce', 'fwstextdomain' ),
			'manage_options',
			'fws-custom-settings',
			array($this, 'settings_page')
		);
	}

	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=fws-custom-settings">' . __( 'Settings', 'plugin_textdomain' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}
	
	private function settings_fields() {
		$settings['standard'] = array(
			'title'					=> __( 'Kies je opties...', 'fwstextdomain' ),
			'description'			=> __( 'De verschillende opties voor de Custom WooCommerce plugin.', 'fwstextdomain' ),
			'fields'				=> array(
				array(
					'id' 			=> 'search_products',
					'label'			=> __( 'Zoek producten', 'fwstextdomain' ),
					'description'	=> __( 'Gebruik de zoekfunctie alleen voor producten.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'hide_shipments_when_free',
					'label'			=> __( 'Verzendmethode beperken', 'fwstextdomain' ),
					'description'	=> __( 'Toon geen betaalde verzendmethode wanneer dezelfde methode ook gratis wordt aangeboden.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'hide_shipment_info_cartpage',
					'label'			=> __( 'Verberg verzendinformatie', 'fwstextdomain' ),
					'description'	=> __( 'Toon alleen de verzendmethoden op de winkelmand pagina (verberg reeds bekende verzendgegevens).', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'hide_coupon_checkout',
					'label'			=> __( 'Verberg veld voor kortingscode', 'fwstextdomain' ),
					'description'	=> __( 'Het kortingscode veld verbergen op de afrekenpagina.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'hide_titels_for_tabs',
					'label'			=> __( 'Verberg titels in tabjes', 'fwstextdomain' ),
					'description'	=> __( 'Overbodige kopjes of titels verwijderen bij de tabjes op de productpagina.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'move_categorie_descriptions',
					'label'			=> __( 'Verplaats categorieomschrijvingen', 'fwstextdomain' ),
					'description'	=> __( 'De omschrijvingen voor categorieën onderaan plaatsen (i.p.v. bovenaan).', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'move_coupon_cart_page',
					'label'			=> __( 'Kortingscode verplaatsen', 'fwstextdomain' ),
					'description'	=> __( 'Verplaats het velde voor de kortingscode op winkelmand pagina (boven de subtotalen).', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'two_col_checkout',
					'label'			=> __( 'Afrekenpagina verbeteren', 'fwstextdomain' ),
					'description'	=> __( 'De afrekenpagina verdelen over twee kolommen.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'hide_sku',
					'label'			=> __( 'SKU verbergen', 'fwstextdomain' ),
					'description'	=> __( 'De artikelnummer (SKU) verbergen op de productdetailpagina.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'alt_stock_messages',
					'label'			=> __( 'Wijzig voorraadmeldingen', 'fwstextdomain' ),
					'description'	=> __( 'Toon en gebruik alternatieve benamingen voor de voorraad informatie op de productpagina\'s.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'new_product_badge',
					'label'			=> __( 'Nieuw product badge', 'fwstextdomain' ),
					'description'	=> __( 'Toon een badge met de tekst "Nieuw" voor X dagen nadat een nieuw product is toegevoegd. Zet het aantal dagen op "0" om de optie uit te zetten.', 'fwstextdomain' ),
					'type'			=> 'text',
					'default'		=> '0',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'remove_breadcrumbs',
					'label'			=> __( 'Verwijder broodkruimelpad', 'fwstextdomain' ),
					'description'	=> __( 'De broodkruimel pad van WooCommerce verwijderen. Wil je deze tonen? Gebruik dan de functie uit Yoast SEO.', 'fwstextdomain' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				)
			)
		);
		$settings = apply_filters( 'plugin_settings_fields', $settings );
		return $settings;
	}

	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'fws_custom_woo_Settings' );
				foreach( $data['fields'] as $field ) {
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'fws_custom_woo_Settings', $option_name );
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'fws_custom_woo_Settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	public function display_field( $args ) {
		$field = $args['field'];
		$html = '';
		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );
		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}
		switch( $field['type'] ) {
			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;
			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;
			default:
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";


		}
		$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
		echo $html;
	}

	public function settings_page() {
		$html = '<div class="wrap" id="plugin_settings">' . "\n";
			$html .= '<h2>' . __( 'Custom WooCommerce' , 'fwstextdomain' ) . '</h2>' . "\n";
			$html .= '<p>'.__( 'Kies hieronder welke aanpassingen je wil toepassen. Controleer elke aanpassingen of deze het gewenste resultaat oplevert. Werkt een optie niet zoals gewenst, zet deze dan uit of overleg deze met je webbouwer.', 'fwstextdomain' ).'</p>' . "\n";
			$html .= '<form method="post" action="options.php">' . "\n";
				/*$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					$html .= '<li><a class="tab all current" href="#all">' . __( 'All' , 'fwstextdomain' ) . '</a></li>' . "\n";
					foreach( $this->settings as $section => $data ) {
						$html .= '<li>| <a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
					}
				$html .= '</ul>' . "\n";
				$html .= '<div class="clear"></div>' . "\n";*/
				ob_start();
				settings_fields( 'fws_custom_woo_Settings' );
				do_settings_sections( 'fws_custom_woo_Settings' );
				$html .= ob_get_clean();
				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'fwstextdomain' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
			$html .= '<p>'.__( 'Afhankelijk van het thema dat je gebruikt zullen niet alle aanpassingen even "mooi" uitzien. Dit kan jij of je webbouwer verhelpen met een beetje CSS style code.<br>Wil je de teksten van de plugin aanpassen, dan kan dat eenvoudig met plugins zoals <em>Say What</em> of <em>Loco Translate</em>.', 'fwstextdomain' ).'</p>' . "\n";
		$html .= '</div>' . "\n";
		echo $html;
	}
}

$custom_woo_settings = new Custom_Woo_Plugin_Settings( __FILE__ );
