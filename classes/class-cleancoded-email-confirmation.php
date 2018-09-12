<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Cleancoded_Email_Confirmation {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;

	public function __construct( $file ) {
		$this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );

		// Handle localisation
		add_action( 'plugins_loaded', array( $this, 'cleancoded_load_localisation' ) );

		// Add second email address field to checkout
		add_filter( 'woocommerce_billing_fields', array( $this, 'cleancoded_add_billing_field' ) );

		// Add default value to second email address field (for WC 2.2+)
		add_filter( 'default_checkout_billing_email-2', array( $this, 'cleancoded_default_field_value' ), 10, 2 );

		// Ensure email addresses match
		add_filter( 'woocommerce_process_checkout_field_billing_email-2', array( $this, 'cleancoded_validate_email_address' ) );

	}

	public function cleancoded_add_billing_field( $fields = array() ) {

		$return_fields = array();

		foreach( $fields as $field_key => $field_data ) {

			$return_fields[ $field_key ] = $field_data;

			if( 'billing_email' == $field_key ) {

				$return_fields['billing_email']['class'] = apply_filters( 'woocommerce_original_email_field_class', array( 'form-row-first' ) );

				$return_fields['billing_email-2'] = array(
					'label' 			=> __( 'Confirm Email Address', 'cleancoded-email-confirmation' ),
					'placeholder' 			=> _x( 'Email Address', 'placeholder', 'cleancoded-email-confirmation' ),
					'required' 			=> true,
					'class' 			=> apply_filters( 'woocommerce_confirm_email_field_class', array( 'form-row-last' ) ),
					'clear'				=> true,
					'validate'			=> array( 'email' ),
				);
			}

		}

		if( apply_filters( 'woocommerce_confirm_email_wide_phone_field', true ) ) {
			$return_fields['billing_phone']['class'] = array( 'form-row-wide' );
		}

		return $return_fields;

	}

	public function cleancoded_default_field_value( $value = null, $field = 'billing_email-2' ) {
		if ( is_user_logged_in() ) {
			global $current_user;
			$value = $current_user->user_email;
		}
		return $value;
	}

	public function cleancoded_validate_email_address( $confirm_email = '' ) {
		global $woocommerce;

		// Use new checkout object for WC 3.0+
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$billing_email = $woocommerce->checkout->posted['billing_email'];
		} else {
			$checkout = new WC_Checkout;
			$billing_email = $checkout->get_value('billing_email');	
		}

		if( strtolower( $confirm_email ) != strtolower( $billing_email ) ) {

			$notice = sprintf( __( '%1$sEmail addresses%2$s do not match.' , 'cleancoded-email-confirmation' ) , '<strong>' , '</strong>' );

			if ( version_compare( WC_VERSION, '2.3', '<' ) ) {
				$woocommerce->add_error( $notice );
			} else {
				wc_add_notice( $notice, 'error' );
			}

		}

		return $confirm_email;
	}

	public function cleancoded_load_localisation () {
		load_plugin_textdomain( 'cleancoded-email-confirmation', false, basename( dirname( $this->file ) ) . '/languages/' );
	}

}
