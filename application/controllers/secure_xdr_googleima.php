<?php
    /**
     * This controller handles Cross-device resume, Google IMA, player token
     * and message bus handling.
     * The name of the class is the name of the general route, and each function
     * (expect __construct) is a route. So, if we wanted to see multi_feature
     * we will go to insert_your_server_name.com/Secure_XDR_GoogleIMA/multi_feature
     * The views are loaded as in
     * $this->load->view('Secure_XDR_GoogleIMA/file-name', $data)
     * where $data is an optional array that has useful
     * variables for the view
     *
     * Be sure to check the views also in order to get
     * the full picture of what is happening in each route
     */
    class Secure_XDR_GoogleIMA extends CI_Controller{

        private $_api_wrapper;
        private $_default_embed_code;
        private $_default_player_id;
        private $_google_ima_player_id;
        private $_sample_email;
        private $_google_ima_ad_tag;

        /**
         * Reference to the CodeIgniter app. Used to load the ooyala library
         */
        protected $CI;

        function __construct() {
            parent::__construct();
            // Load the Ooyala configuration file
            $this->CI =& get_instance();
            $this->CI->load->config('ooyala_config');

            // Load Ooyala wrapper
            $this->load->library('ooyala');

            $this->load->helper('url');

            // Load variables defined in the library
            $this->_default_embed_code = $this->CI->config->item('default_embed_code');
            $this->_default_player_id = $this->CI->config->item('default_player_id');
            $this->_google_ima_player_id = $this->CI->config->item('google_ima_player_id');
            $this->_sample_email = $this->CI->config->item('sample_email');
            $this->_google_ima_ad_tag = $this->CI->config->item('google_ima_ad_tag');

            // Finally, create a new instance
            $this->_api_wrapper = new Ooyala();

        }

        /**
         * Barebones function that executes the example in Ooyala V2 PHP SDK
         * @see https://github.com/ooyala/php-v2-sdk
         */
        public function index(){
            $data['assets'] = $this->_api_wrapper->test();
            $this->load->view('Secure_XDR_GoogleIMA/test', $data);
        }

        /**
         * Complete example that shows:
         * + Google IMA
         * + Player Token
         * + Cross-device resume
         * + Message bus handling
         */
        public function multi_feature(){
            $data['uses_google_ima'] = true;
            $data['player_id'] = $this->_google_ima_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $data['adTagUrl'] = $this->_google_ima_ad_tag;
            $user_id = $this->_sample_email;
            $embed_token_url = $this->_api_wrapper->get_embed_token($embed_code, $user_id);
            $data['embed_token_url'] = $embed_token_url;
            $playhead_time =  $this->_api_wrapper->get_playhead_time($embed_code, $user_id);
            $data['playhead_time'] = $playhead_time;
            $this->load->view('Secure_XDR_GoogleIMA/multi-feature', $data);
        }

        /**
         * Handles cross device resume. You need to have it activated
         * in your account in order for it to work.
         * @see http://support.ooyala.com/developers/documentation/concepts/chapter_xdr.html#chapter_xdr
         */
        public function cross_resume(){
            $data['player_id'] = $this->_default_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $user_id = $this->_sample_email;
            $embed_token_url = $this->_api_wrapper->get_embed_token($embed_code, $user_id);
            $data['embed_token_url'] = $embed_token_url;
            $playhead_time =  $this->_api_wrapper->get_playhead_time($embed_code, $user_id);
            $data['playhead_time'] = $playhead_time;
            $this->load->view('Secure_XDR_GoogleIMA/cross_resume', $data);
        }

        /**
         * The simplest example you can get, shows a player with the default embed code
         */
        public function simple(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;
            $this->load->view('Secure_XDR_GoogleIMA/simple', $data);
        }

        /**
         * Shows a player with a video and logs the events to a log area.
         */
        public function message_bus(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;
            $this->load->view('Secure_XDR_GoogleIMA/message-bus', $data);
        }

        /**
         * An extension to message_bus, shows all the events in the log area
         * and handles events such as milestones
         */
        public function message_bus_advanced(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;
            $this->load->view('Secure_XDR_GoogleIMA/message-bus-advanced', $data);
        }

        /**
         * Handles Ooyala player token.
         * @see http://support.ooyala.com/developers/documentation/concepts/player_v3dev_authoverview.html
         */
        public function player_token(){
            $data['player_id'] = $this->_default_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $embed_token_url = $this->_api_wrapper->get_embed_token($embed_code, $this->_sample_email);
            $data['embed_token_url'] = $embed_token_url;
            $this->load->view('Secure_XDR_GoogleIMA/token', $data);
        }

        /**
         * Handle Google IMA integration
         */
        public function google_ima(){
            $data['player_id'] = $this->_google_ima_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $data['adTagUrl'] = $this->_google_ima_ad_tag;
            $this->load->view('Secure_XDR_GoogleIMA/google_ima', $data);
        }

    }
?>