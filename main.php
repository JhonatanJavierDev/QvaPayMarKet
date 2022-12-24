<?php
/*
Plugin Name: QvaPayMarket
Plugin URI: https://github.com/jhoncorelladev
Description: QvaPayMarket is a plugin that lets you create invoices with payment details for each store in Dokan.
Version: 1.0
Author: Jhon
Author URI: https://github.com/jhoncorelladev
License: GPLv2 or later
Text Domain: qvapay-market
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Check if Woocommerce and Dokan are activated
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && in_array( 'dokan-lite/dokan.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
   
    class QvaPayMarket {

        public function __construct() {
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ) );
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            add_action( 'wp_ajax_qvapay_market_get_invoice_data', array( $this, 'get_invoice_data' ) );
            add_action( 'wp_ajax_qvapay_market_generate_invoice', array( $this, 'generate_invoice' ) );
        }

        //Add Settings link
        public function plugin_action_links( $links ) {
            $plugin_links = array(
                '<a href="' . admin_url( 'admin.php?page=qvapay-market-settings' ) . '">' . __( 'Settings', 'qvapay-market' ) . '</a>',
            );
            return array_merge( $plugin_links, $links );
        }

        //Admin Menu
        public function admin_menu() {
            add_menu_page( 'QvaPayMarket', 'QvaPayMarket', 'manage_options', 'qvapay-market-settings', array( $this, 'settings_page' ), 'dashicons-cart', 25 );
            add_submenu_page( 'qvapay-market-settings', 'Forms', 'Forms', 'manage_options', 'qvapay-market-forms', array( $this, 'forms_page' ) );
            add_submenu_page( 'qvapay-market-settings', 'Upgrade to Pro', 'Upgrade to Pro', 'manage_options', 'qvapay-market-pro', array( $this, 'pro_page' ) );
        }

        //Settings Page
        public function settings_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <form action="options.php" method="post">
                    <?php
                    settings_fields( 'qvapay_market_settings' );
                    do_settings_sections( 'qvapay_market_settings' );
                    submit_button( 'Save Settings' );
                    ?>
                </form>
            </div>
            <?php
        }

        //Forms Page
        public function forms_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <form action="options.php" method="post">
                    <?php
                    settings_fields( 'qvapay_market_forms' );
                    do_settings_sections( 'qvapay_market_forms' );
                    submit_button( 'Guardar Formulario' );
                    ?>
                </form>
            </div>
            <?php
        }

        //Pro Page
        public function pro_page() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <p>
                    This is the pro version of QvaPayMarket.
                    Please visit <a href="https://jhon.dev.com">https://jhon.dev.com</a> to purchase the pro version.
                </p>
            </div>
            <?php
        }

        //Enqueue Scripts
        public function admin_enqueue_scripts( $hook ) {
            if ( 'toplevel_page_qvapay-market-settings' == $hook || 'qvapay-market_page_qvapay-market-forms' == $hook ) {
                wp_enqueue_script( 'qvapay-market', plugins_url( 'assets/js/qvapay-market.js', __FILE__ ), array( 'jquery' ), '1.0', true );
                wp_localize_script( 'qvapay-market', 'qvapay_market_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
            }
        }

        //Ajax Callback - Get Invoice Data
        public function get_invoice_data() {
            //Get the data from Dokan
            //Generate the invoice data
            $invoice_data = array(
                'store_name' => 'Test Store',
                'products' => array(
                    array(
                        'name' => 'Test Product 1',
                        'description' => 'Test Product 1 Description',
                        'price' => 10
                    ),
                    array(
                        'name' => 'Test Product 2',
                        'description' => 'Test Product 2 Description',
                        'price' => 15
                    )
                ),
                'total' => 25,
                'account_number' => '123456789',
                'phone_number' => '123456789',
                'email' => 'test@example.com',
                'courier_services' => array(
                    'localities' => 60,
                    'zones' => 150,
                    'zones_la_lisa' => 250
                )
            );

            echo json_encode( $invoice_data );
            die();
        }

        //Ajax Callback - Generate Invoice
        public function generate_invoice() {
            $data = $_POST['data'];

            //Generate the invoice
            //Save the invoice

            echo json_encode( array( 'status' => 'success' ) );
            die();
        }

    }

    new QvaPayMarket();

}

//Register Settings
add_action( 'admin_init', 'qvapay_market_register_settings' );

function qvapay_market_register_settings() {
    //Settings
    register_setting( 'qvapay_market_settings', 'qvapay_market_settings', 'qvapay_market_settings_validate' );

    add_settings_section( 'qvapay_market_settings', '', 'qvapay_market_settings_section_callback', 'qvapay_market_settings' );

    add_settings_field( 'qvapay_market_settings_merchant_id', 'ID del Vendedor', 'qvapay_market_settings_merchant_id_callback', 'qvapay_market_settings', 'qvapay_market_settings' );
    add_settings_field( 'qvapay_market_settings_notify_url', 'URL de la Pagina', 'qvapay_market_settings_notify_url_callback', 'qvapay_market_settings', 'qvapay_market_settings' );
    add_settings_field( 'qvapay_market_settings_currency', 'Moneda', 'qvapay_market_settings_currency_callback', 'qvapay_market_settings', 'qvapay_market_settings' );

    //Forms
    register_setting( 'qvapay_market_forms', 'qvapay_market_forms', 'qvapay_market_forms_validate' );

    add_settings_section( 'qvapay_market_forms', '', 'qvapay_market_forms_section_callback', 'qvapay_market_forms' );

    add_settings_field( 'qvapay_market_forms_invoice_form', 'Formulario de Factura', 'qvapay_market_forms_invoice_form_callback', 'qvapay_market_forms', 'qvapay_market_forms' );
}

//Settings Section Callback
function qvapay_market_settings_section_callback() {
    echo '<p>QvaPayMarket sesion de Ajustes.</p>';
}

//Settings Merchant ID Callback
function qvapay_market_settings_merchant_id_callback() {
    $settings = get_option( 'qvapay_market_settings' );
    ?>
    <input type="text" name="qvapay_market_settings[merchant_id]" value="<?php echo isset( $settings['merchant_id'] ) ? esc_attr( $settings['merchant_id'] ) : ''; ?>">
    <?php
}

//Settings Notify URL Callback
function qvapay_market_settings_notify_url_callback() {
    $settings = get_option( 'qvapay_market_settings' );
    ?>
    <input type="text" name="qvapay_market_settings[notify_url]" value="<?php echo isset( $settings['notify_url'] ) ? esc_attr( $settings['notify_url'] ) : ''; ?>">
    <?php
}

//Settings Currency Callback
function qvapay_market_settings_currency_callback() {
    $settings = get_option( 'qvapay_market_settings' );
    ?>
    <input type="text" name="qvapay_market_settings[currency]" value="<?php echo isset( $settings['currency'] ) ? esc_attr( $settings['currency'] ) : ''; ?>">
    <?php
}

//Settings Validate
function qvapay_market_settings_validate( $input ) {
    $output = array();

    if ( isset( $input['merchant_id'] ) && ! empty( $input['merchant_id'] ) ) {
        $output['merchant_id'] = sanitize_text_field( $input['merchant_id'] );
    }

    if ( isset( $input['notify_url'] ) && ! empty( $input['notify_url'] ) ) {
        $output['notify_url'] = sanitize_text_field( $input['notify_url'] );
    }

    if ( isset( $input['currency'] ) && ! empty( $input['currency'] ) ) {
        $output['currency'] = sanitize_text_field( $input['currency'] );
    }

    return $output;
}

//Forms Section Callback
function qvapay_market_forms_section_callback() {
    echo '<p>QvaPayMarket Sesion de formulario.</p>';
}

//Forms Invoice Form Callback
function qvapay_market_forms_invoice_form_callback() {
    $forms = get_option( 'qvapay_market_forms' );
    echo wp_editor( isset( $forms['invoice_form'] ) ? wp_kses_post( wpautop( $forms['invoice_form'] ) ) : '', 'qvapay_market_forms_invoice_form', array( 'textarea_name' => 'qvapay_market_forms[invoice_form]' ) );
}

//Forms Validate
function qvapay_market_forms_validate( $input ) {
    $output = array();

    if ( isset( $input['invoice_form'] ) && ! empty( $input['invoice_form'] ) ) {
        $output['invoice_form'] = wp_kses_post( $input['invoice_form'] );
    }

    return $output;
}
//Forms Page
function qvapay_market_forms_page() {
	$title = __( 'Forms', 'qvapay-market' );
	?>
	<div class="wrap">
		<h2><?php echo esc_html( $title ); ?></h2>
		<form method="post" action="options.php">
			<?php
				settings_fields( 'qvapay_market_forms' );
				do_settings_sections( 'qvapay_market_forms' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

//Settings
function qvapay_market_forms_settings() {
	//Forms
	add_settings_section(
		'qvapay_market_forms_section',
		'',
		'qvapay_market_forms_section_callback',
		'qvapay_market_forms'
	);
	
	add_settings_field(
		'qvapay_market_forms_invoice_form',
		__( 'Invoice Form', 'qvapay-market' ),
		'qvapay_market_forms_invoice_form_render',
		'qvapay_market_forms',
		'qvapay_market_forms_section'
	);
	
	register_setting(
		'qvapay_market_forms',
		'qvapay_market_forms',
		'qvapay_market_forms_validate'
	);
}
add_action( 'admin_init', 'qvapay_market_forms_settings' );

//Forms Invoice Form Render
function qvapay_market_forms_invoice_form_render() {
	$forms = qvapay_market_forms();
	$settings = (array) get_option( 'qvapay_market_forms' );
	$invoice_form = ( isset( $settings['invoice_form'] ) && ! empty( $settings['invoice_form'] ) ) ? $settings['invoice_form'] : '';
	?>
	<select name="qvapay_market_forms[invoice_form]">
		<option value=""><?php _e( 'Select Form', 'qvapay-market' ); ?></option>
		<?php foreach ( $forms as $form ) : ?>
		<option value="<?php echo $form['id']; ?>"<?php selected( $invoice_form, $form['id'] ); ?>><?php echo $form['title']; ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
