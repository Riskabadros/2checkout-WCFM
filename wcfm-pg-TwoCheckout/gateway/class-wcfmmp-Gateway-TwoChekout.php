<?php
    
    if (!defined('ABSPATH')) {
    exit;
}

class WCFMmp_Gateway_TwoCheckout extends  {
    public $id;
    public $message = array();
    public $gateway_title;
    public $payment_gateway;
    public $withdrawal_id;
    public $vendor_id;
    public $withdraw_amount = 0;
    public $currency;
    public $transaction_mode;
    private $receiver_email;
    public $test_mode = false;
    public $merchant_code;
    public $secret_key;
    public $secret_word;

    public function __construct() {
        $this->id = WCFMpgmp_GATEWAY ;
        $this->gateway_title = __(WCFMpgmp_GATEWAY_LABEL, 'wc-multivendor-marketplace');
        $this->payment_gateway = $this->id;
    }

    public function gateway_logo() { 
        global $WCFMmp; 
        return $WCFMmp->plugin_url . 'assets/images/'.$this->id.'.png'; 
    }

    public function process_payment( $withdrawal_id, $vendor_id, $withdraw_amount, $withdraw_charges, $transaction_mode = 'auto' ) {
        global $WCFM, $WCFMmp;
        
        $this->withdrawal_id = $withdrawal_id;
        $this->vendor_id = $vendor_id;
        $this->withdraw_amount = $withdraw_amount;
        $this->currency = get_woocommerce_currency();
        $this->transaction_mode = $transaction_mode;
        $this->receiver_email = $WCFMmp->wcfmmp_vendor->get_vendor_payment_account( $this->vendor_id, $this->id );
        
        $withdrawal_test_mode = isset( $WCFMmp->wcfmmp_withdrawal_options['test_mode'] ) ? 'yes' : 'no';
        
        $this->merchant_code = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_merchant_code'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_merchant_code'] : '';
        
        $this->secret_key = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secret_key'] : '';
        
        $this->secret_word = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secret_word'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secret_word'] : '';

       if ( $withdrawal_test_mode == 'yes') {
        
            $this->test_mode = true;
        
            $this->merchant_code = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_merchant_code'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_merchant_code'] : '';
           $this->secret_key = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_key'] : '';
           $this->secret_word = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_word'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_word'] : '';
        }

        if ( $this->validate_request() ) {
            // Updating withdrawal meta
            $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'withdraw_amount', $this->withdraw_amount );
            $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'currency', $this->currency );
            $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'receiver_email', $this->receiver_email );
            return array( 'status' => true, 'message' => __('New transaction has been initiated', 'wc-multivendor-marketplace') );
        } else {
            return $this->message;
        }
    }

    public function validate_request() {
        // Your validation logic here
        global $WCFMmp;
        return true;
    }
}
