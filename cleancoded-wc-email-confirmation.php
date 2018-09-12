<?php
/*
 * Plugin Name: Cleancoded Email Confirmation
 * Version: 1.0
 * Plugin URI: http://CLEANCODED.com
 * Description: Confirmation Email for Checkout page of Woocommerce Setting.
 * Author: CLEANCODED
 * Author URI: https://CLEANCODED.com/
 * Requires at least: 4.0
 *
 * Text domain: Cleancoded-Email-Confirmation
 * Domain path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'classes/class-cleancoded-email-confirmation.php' );

global $wcceav;
$wcceav = new Cleancoded_Email_Confirmation( __FILE__ );
