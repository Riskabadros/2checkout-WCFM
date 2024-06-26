<?php

/**
 * WCFM TwoCheckout plugin core
 *
 * Plugin initiate
 *
 * @author      Ayoub Rachchad
 * @package     wcfm-pg-TwoCheckout
 * @version     1.0.1
 */

class WCFM_PG_TwoCheckout {

    public $plugin_base_name;
    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;

    public function __construct($file) {

        $this->file = $file;
        $this->plugin_base_name = plugin_basename($file);
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WCFMpgmp_TOKEN;//'wcfm-twoco';
        $this->text_domain = WCFMpgmp_TEXT_DOMAIN;//'wcfm-twoco';
        $this->version =WCFMpgmp_VERSION; //'1.0.0';

        add_action('wcfm_init', array(&$this, 'init'), 10);
    }

    function init() {
        global $WCFM, $WCFMre;

        // Init Text Domain
        $this->load_plugin_textdomain();

        add_filter('wcfm_marketplace_withdrwal_payment_methods', array(&$this, 'wcfmmp_custom_pg'));

        add_filter('wcfm_marketplace_settings_fields_withdrawal_payment_keys', array(&$this, 'wcfmmp_custom_pg_api_keys'), 50, 2);

        add_filter('wcfm_marketplace_settings_fields_withdrawal_payment_test_keys', array(&$this, 'wcfmmp_custom_pg_api_test_keys'), 50, 2);

        add_filter('wcfm_marketplace_settings_fields_withdrawal_charges', array(&$this, 'wcfmmp_custom_pg_withdrawal_charges'), 50, 3);

        add_filter('wcfm_marketplace_settings_fields_billing', array(&$this, 'wcfmmp_custom_pg_vendor_setting'), 50, 2);

        // Load Gateway Class
        require_once $this->plugin_path . 'gateway/class-wcfmmp-Gateway-TwoChekout.php';
    }

    function wcfmmp_custom_pg($payment_methods) {
        $payment_methods[WCFMpgmp_GATEWAY] = __(WCFMpgmp_GATEWAY_LABEL, 'wcfm-twoco');
        return $payment_methods;
    }

    function wcfmmp_custom_pg_api_keys($payment_keys, $wcfm_withdrawal_options) {
        $gateway_slug = WCFMpgmp_GATEWAY;
        $gateway_label = __(WCFMpgmp_GATEWAY_LABEL, $this->text_domain) . ' ';

        // Your TwoCheckout API key fields here
    $withdrawal_2checkout_merchant_code = isset( $wcfm_withdrawal_options[$gateway_slug.'_merchant_code'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_merchant_code'] : '';
    $withdrawal_2checkout_secret_key = isset( $wcfm_withdrawal_options[$gateway_slug.'_secret_key'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_secret_key'] : '';
    $withdrawal_2checkout_secret_word = isset( $wcfm_withdrawal_options[$gateway_slug.'_secret_word'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_secret_word'] : '';

    $payment_2checkout_keys = array(
        "withdrawal_".$gateway_slug."_merchant_code" => array(
            'label' => __('2Checkout Merchant Code','wc-multivendor-marketplace'),
            'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_merchant_code]',
            'type' => 'text',
            'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
            'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
            'value' => $withdrawal_2checkout_merchant_code
        ),
        "withdrawal_".$gateway_slug."_secret_key" => array(
            'label' => __('2Checkout Secret Key', 'wc-multivendor-marketplace'),
            'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_secret_key]',
            'type' => 'text',
            'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
            'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
            'value' => $withdrawal_2checkout_secret_key
        ),
        "withdrawal_".$gateway_slug."_secret_word" => array(
            'label' => __('2Checkout Secret Word', 'wc-multivendor-marketplace'),
            'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_secret_word]',
            'type' => 'text',
            'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
            'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug,
            'value' => $withdrawal_2checkout_secret_word
        )
    );

    $payment_keys = array_merge( $payment_keys, $payment_2checkout_keys );
   



        return $payment_keys;
    }

    function wcfmmp_custom_pg_api_test_keys($payment_test_keys, $wcfm_withdrawal_options) {
        $gateway_slug = WCFMpgmp_GATEWAY;
        $gateway_label = __(WCFMpgmp_GATEWAY_LABEL, $this->text_domain). ' ';

        // Your TwoCheckout test key fields here

    $withdrawal_2checkout_test_merchant_code = isset( $wcfm_withdrawal_options[$gateway_slug.'_test_merchant_code'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_test_merchant_code'] : '';
    $withdrawal_2checkout_test_secret_key = isset( $wcfm_withdrawal_options[$gateway_slug.'_test_secret_key'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_test_secret_key'] : '';
    $withdrawal_2checkout_test_secret_word = isset( $wcfm_withdrawal_options[$gateway_slug.'_test_secret_word'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_test_secret_word'] : '';

    $payment_2checkout_test_keys = array(
        "withdrawal_".$gateway_slug."_test_merchant_code" => array(
            'label' => __('2Checkout Test Merchant Code', 'wc-multivendor-marketplace'),
            'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_test_merchant_code]',
            'type' => 'text',
            'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
            'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
            'value' => $withdrawal_2checkout_test_merchant_code
        ),
        "withdrawal_".$gateway_slug."_test_secret_key" => array(
            'label' => __('2Checkout Test Secret Key', 'wc-multivendor-marketplace'),
            'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_test_secret_key]',
            'type' => 'text',
            'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
            'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
            'value' => $withdrawal_2checkout_test_secret_key
        ),
        "withdrawal_".$gateway_slug."_test_secret_word" => array(
            'label' => __('2Checkout Test Secret Word', 'wc-multivendor-marketplace'),
            'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_test_secret_word]',
            'type' => 'text',
            'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
            'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_test withdrawal_mode_'.$gateway_slug,
            'value' => $withdrawal_2checkout_test_secret_word
        )
    );

    $payment_test_keys = array_merge( $payment_test_keys, $payment_2checkout_test_keys );



        return $payment_test_keys;
    }

    function wcfmmp_custom_pg_withdrawal_charges($withdrawal_charges, $wcfm_withdrawal_options, $withdrawal_charge) {
        $gateway_slug = WCFMpgmp_GATEWAY;
        $gateway_label = __(WCFMpgmp_GATEWAY_LABEL, $this->text_domain) . ' ';

        // Your TwoCheckout withdrawal charge fields here
    $withdrawal_charge_2checkout = isset( $withdrawal_charge[$gateway_slug] ) ? $withdrawal_charge[$gateway_slug] : array();

    $payment_withdrawal_charges = array(
        "withdrawal_charge_".$gateway_slug => array(
            'label' => __('2Checkout Charge', 'wc-multivendor-marketplace'),
            'type' => 'multiinput',
            'name' => 'wcfm_withdrawal_options[withdrawal_charge]['.$gateway_slug.']',
            'class' => 'withdraw_charge_block withdraw_charge_'.$gateway_slug,
            'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_'.$gateway_slug,
            'value' => $withdrawal_charge_2checkout,
            'custom_attributes' => array( 'limit' => 1 ),
            'options' => array(
                "percent" => array(
                    'label' => __('Percent Charge(%)', 'wc-multivendor-marketplace'),
                    'type' => 'number',
                    'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed',
                    'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed',
                    'attributes' => array( 'min' => '0.1', 'step' => '0.1')
                ),
                "fixed" => array(
                    'label' => __('Fixed Charge', 'wc-multivendor-marketplace'),
                    'type' => 'number',
                    'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed',
                    'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed',
                    'attributes' => array( 'min' => '0.1', 'step' => '0.1')
                ),
                "tax" => array(
                    'label' => __('Charge Tax', 'wc-multivendor-marketplace'),
                    'type' => 'number',
                    'class' => 'wcfm-text wcfm_ele',
                    'label_class' => 'wcfm_title wcfm_ele',
                    'attributes' => array( 'min' => '0.1', 'step' => '0.1'),
                    'hints' => __( 'Tax for withdrawal charge, calculate in percent.', 'wc-multivendor-marketplace' )
                )
            )
        )
    );

    $withdrawal_charges = array_merge( $withdrawal_charges, $payment_withdrawal_charges );

        return $withdrawal_charges;
    }

   function wcfmmp_custom_pg_vendor_setting($vendor_billing_fields, $vendor_id) {
    $gateway_slug = WCFMpgmp_GATEWAY;
    $gateway_label = __(WCFMpgmp_GATEWAY_LABEL, $this->text_domain) . ' ';

    // Your TwoCheckout vendor setting fields here
    $vendor_data = get_user_meta($vendor_id, 'wcfmmp_profile_settings', true);
    if (!$vendor_data) $vendor_data = array();
    $twocheckout = isset($vendor_data['payment'][$gateway_slug]['email']) ? esc_attr($vendor_data['payment'][$gateway_slug]['email']) : '';
    $vendor_twocheckout_billing_fields = array(
        $gateway_slug => array(
            'label' => $gateway_label . __('Account', 'wcfm-pg-twocheckout'),
            'name' => 'payment[' . $gateway_slug . '][email]',
            'type' => 'text',
            'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value' => $twocheckout
        )
    );
    $vendor_billing_fields = array_merge($vendor_billing_fields, $vendor_twocheckout_billing_fields);
    return $vendor_billing_fields;
}

   
    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        $locale = function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, $this->text_domain);

        load_textdomain($this->text_domain, $this->plugin_path . "lang/{$this->text_domain}-{$locale}.mo");
        load_textdomain($this->text_domain, ABSPATH . "wp-content/languages/plugins/{$this->text_domain}-{$locale}.mo");
    }

    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }
}
