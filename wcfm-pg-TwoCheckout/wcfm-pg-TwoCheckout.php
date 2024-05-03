<?php
/**
 * Plugin Name: WCFM Marketplace Vendor Payment - 2checkout
 * Plugin URI: https://github.com/Riskabadros
 * Description: WCFM Marketplace 2checkout vendor payment gateway 
 * Author: Ayoub Rachchad
 * Version: 1.0.0
 * Author URI: https://github.com/Riskabadros
 *
 * Text Domain: wcfm-pg-TwoCheckout 
 * Domain Path: /lang/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 3.4.0
 *
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(!defined('WCFM_TOKEN')) return;
if(!defined('WCFM_TEXT_DOMAIN')) return;

if ( ! class_exists( 'WCFMpgmp_Dependencies' ) )
	require_once 'helpers/class-wcfm-pg-TwoCheckout-dependencies.php';

if( !WCFMpgmp_Dependencies::woocommerce_plugin_active_check() )
	return;

if( !WCFMpgmp_Dependencies::wcfm_plugin_active_check() )
	return;

if( !WCFMpgmp_Dependencies::wcfmmp_plugin_active_check() )
	return;

require_once 'helpers/wcfm-pg-TwoCheckout-core-functions.php';
require_once 'wcfm-pg-TwoCheckout-config.php';

if(!class_exists('WCFM_PG_TwoCheckout')) {
	include_once( 'core/class-wcfm-pg-TwoCheckout.php' );
	global $WCFM, $WCFMpgmp, $WCFM_Query;
	$WCFMpgmp = new WCFM_PG_TwoCheckout( __FILE__ );
	$GLOBALS['WCFMpgmp'] = $TwoCheckout;
}