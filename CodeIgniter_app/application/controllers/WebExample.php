<?php
    class WebExample extends CI_Controller{
        private $_api_wrapper;
        private $_default_embed_code;
        private $_default_player_id;

        // Reference to the CodeIgniter app
        protected $CI;

        function __construct() {
            parent::__construct();
            // We load the configuration file
            $this->CI =& get_instance();
            $this->CI->load->config('ooyala_config');
            // We load Ooyala wrapper
            $this->load->library('ooyala');
            $this->load->helper('url');
            $this->_default_embed_code = $this->CI->config->item('default_embed_code');
            $this->_default_player_id = $this->CI->config->item('default_player_id');
            $this->_api_wrapper = new Ooyala();
        }

        public function index(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;

            // This will be abstracted in an API
            $result = $this->_api_wrapper->get_related_videos($this->_default_embed_code);
            $result = $result->results;

            // Pass related videos as json to use in the view later
            $data['related_videos'] = json_encode($result);
            $trending_videos = $this->_api_wrapper->get_trending_videos();
            $trending_videos = $trending_videos->results;
            $data['trending_videos'] = json_encode($trending_videos);

            // Get tags to allow embedding video in Twitter
            $data['twitter_meta_tags'] = $this->_api_wrapper->get_twitter_card_info($data['player_id'], $data['embed_code']);

            $this->load->view('web-example', $data);
        }

        public function mobile(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;
            // This will be abstracted in an API
            $result = $this->_api_wrapper->get_related_videos($this->_default_embed_code);
            $result = $result->results;

            // Pass related videos as json to use in the view later
            $data['related_videos'] = json_encode($result);
            $trending_videos = $this->_api_wrapper->get_trending_videos();
            $trending_videos = $trending_videos->results;
            $data['trending_videos'] = json_encode($trending_videos);
            $this->load->view('web-example-mobile', $data);
        }


    }
?>