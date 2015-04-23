<?php
    /**
     * This controller handles a sample webpage showing how to interact
     * with the Discovery API and getting Twitter card information.
     * The name of the class is the name of the general route, and each function
     * (expect __construct) is a route. So, if we wanted to see multi_feature
     * we will go to insert_your_server_name.com/Secure_XDR_GoogleIMA/multi_feature
     * The views are loaded as in
     * $this->load->view('Web_example/file-name', $data)
     * where $data is an optional array that has useful
     * variables for the view
     *
     * Be sure to check the views also in order to get
     * the full picture of what is happening in each route
     */
    class Web_example extends CI_Controller{
        private $_api_wrapper;
        private $_default_embed_code;
        private $_default_player_id;

        // Reference to the CodeIgniter app
        protected $CI;

        function __construct() {
            parent::__construct();
            // Load the configuration file
            $this->CI =& get_instance();
            $this->CI->load->config('ooyala_config');
            // Load Ooyala wrapper
            $this->load->library('ooyala');

            $this->load->helper('url');

            // Load variables defined in the library
            $this->_default_embed_code = $this->CI->config->item('default_embed_code');
            $this->_default_player_id = $this->CI->config->item('default_player_id');

            // Finally, create a new instance
            $this->_api_wrapper = new Ooyala();
        }

        /**
         * Default route, makes a request to Discovery API to get related videos
         * to the default embed code and another to get trending videos worldwide.
         * It also makes a request to get the necessary meta-tags for Twitter cards
         *
         * @see libraries/ooyala.php
         * @see https://dev.twitter.com/cards
         */
        public function index(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;
			
            $result = $this->_api_wrapper->get_related_videos($this->_default_embed_code);

            // Pass related videos as json to use in the view later
            $data['related_videos'] = json_encode($result);
			
            $trending_videos = $this->_api_wrapper->get_trending_videos();

            // Pass trending videos as json to use in the view later
            $data['trending_videos'] = json_encode($trending_videos);

            // Get tags to allow embedding video in Twitter
            $data['twitter_meta_tags'] = $this->_api_wrapper->get_twitter_card_info($data['player_id'], $data['embed_code']);

            $this->load->view('Web_example/web-example', $data);
        }

        /**
         * Very much like index, the main difference is in the view and the resizing page
         */
        public function mobile(){
            $data['embed_code'] = $this->_default_embed_code;
            $data['player_id'] = $this->_default_player_id;
            // This will be abstracted in an API
            $result = $this->_api_wrapper->get_related_videos($this->_default_embed_code);

            // Pass related and trending videos as json to use in the view later
            $data['related_videos'] = json_encode($result);
            $trending_videos = $this->_api_wrapper->get_trending_videos();
            $data['trending_videos'] = json_encode($trending_videos);
            $this->load->view('Web_example/web-example-mobile', $data);
        }


    }
?>