<?php
    class Test extends CI_Controller{

        private $_api_wrapper;
        private $_default_embed_code = "EMBED_CODE";
        private $_default_player_id = "PLAYER_ID";
        private $_google_ima_player_id = "IMA_PLAYER_ID";
        private $_sample_email = "alice@example.com";

        function __construct() {
            parent::__construct();
            $this->load->library("ooyala");
            $this->_api_wrapper = new Ooyala();
        }

        public function index(){
            echo "string";
            $this->_api_wrapper->test();
        }

        public function example_one(){
            // Main thing missing: Google Analytics

            $data['uses_google_ima'] = true;
            $data['player_id'] = $this->_google_ima_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $data['adTagUrl'] = "AdTag";
            $user_id = $this->_sample_email;
            $embed_token_url = $this->_api_wrapper->get_embed_token($embed_code);
            $data['embed_token_url'] = $embed_token_url;
            $playhead_time =  $this->_api_wrapper->get_playhead_time($embed_code, $user_id);
            $data['playhead_time'] = $playhead_time;
            $this->load->view('example-one', $data);
        }


        public function cross_resume(){
            $data['player_id'] = $this->_default_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $user_id = $this->_sample_email;
            $embed_token_url = $this->_api_wrapper->get_embed_token($embed_code);
            $data['embed_token_url'] = $embed_token_url;
            $playhead_time =  $this->_api_wrapper->get_playhead_time($embed_code, $user_id);
            $data['playhead_time'] = $playhead_time;
            $this->load->view('cross_resume', $data);

        }

        public function simple(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;
            $this->load->view('simple', $data);
        }

        public function token(){
            $data['player_id'] = $this->_default_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $embed_token_url = $this->_api_wrapper->get_embed_token($embed_code, $this->_sample_email);
            $data['embed_token_url'] = $embed_token_url;
            $this->load->view('token', $data);
        }

        public function google_ima(){
            $data['player_id'] = $this->_google_ima_player_id;
            $embed_code = $this->_default_embed_code;
            $data['embed_code'] = $embed_code;
            $data['adTagUrl'] = "adTag";
            $this->load->view('google_ima');
        }

    }
?>