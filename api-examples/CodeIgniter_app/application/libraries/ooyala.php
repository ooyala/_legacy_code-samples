<?php
require_once('application/third_party/php-v2-sdk/OoyalaApi.php');

class Ooyala{

    private $_api;
    private $_api_key;
    private $_api_secret;
    private $_p_code;

    // Reference to the CodeIgniter app
    protected $CI;

    // Helper class included in Ooyala PHP SDK
    private $http_client;

    function __construct() {
        // We load the configuration file
        $this->CI =& get_instance();
        $this->CI->load->config('ooyala_config');
        // Then we get each item. This is CodeIgniter specific
        $this->_api_key = $this->CI->config->item('api_key');
        $this->_api_secret = $this->CI->config->item('api_secret');
        $this->_p_code = $this->CI->config->item('p_code');
        $this->_api = new OoyalaApi($this->_api_key, $this->_api_secret);
        $this->http_client = new OoyalaHttpRequest();
    }

    public function test(){
        // This is the test that is given on the _api. Used to know that everything is working
        $parameters = array("where" => "labels INCLUDES 'Funny dogs'");
        $results = $this->_api->get("assets", $parameters);
        $assets = $results->items;
        return $assets;
    }

    public function get_embed_token($embed_code, $user_id = null){
        $request = "http://player.ooyala.com/sas/embed_token/";
        $request .= $this->_p_code . "/";
        $request .= $embed_code;
        $query_params['api_key'] = $this->_api_key;
        $query_params['expires'] = time() + 605;
        if (!is_null($user_id)){
            $query_params['account_id'] = urlencode($user_id);
        }
        $signature = $this->_api->generateSignature('', $request, $query_params);
        $query_params['signature'] = $signature;

        $url = $this->buildURL($request, $query_params);
        return $url;
    }

    public function get_playhead_time($embed_code, $user_id = null){
         // http://_api.ooyala.com/v2/cross_device_resume/accounts/account_id/viewed_assets/embed_code(identifier for the asset)/playhead_info
        $request = "cross_device_resume/accounts";
        $request .= "/" . urlencode($user_id);
        $request .= "/" . "viewed_assets";
        $request .= "/" . $embed_code;
        $request .= "/" . "playhead_info";
        try {
            // The SDK already appends the signature, expires and relevant parameters
            $response = $this->_api->get($request);
        } catch (OoyalaRequestErrorException $e) {
            // This could give a 404 if the user hasn't seen the embed_code
            return 0;
        }
        return $response->playhead_seconds;
    }

    public function get_related_videos($embed_code){
        $parameters = array("limit" => "5");
        $results = $this->_api->get("discover/similar/assets/", $parameters);
        return $results;
    }

    public function get_trending_videos(){
        $parameters = array('countries' => 'all',
                            'time' => 'now',
                            'window' => 'day',
                            "limit" => 5 );
        $results = $this->_api->get("discover/trending/top", $parameters);
        return $results;
    }

    public function get_twitter_card_info($player_id, $content_id){
        // The request URL is of the form
        // http://player.ooyala.com/twitter/meta/player_id/content_id
        $requestPath = "http://player.ooyala.com/twitter/meta";
        $requestPath .= "/" .$player_id;
        $requestPath .= "/" .$content_id;
        $result = $this->generic_get_request($requestPath);
        $twitter_user = $this->CI->config->item('twitter_user');
        $result .= "\n" . "<meta name=\"twitter:site\" content=\"@" .
                urlencode($twitter_user) . "\">" . "\n";
        return $result;


    }

    // Helper functions

    private function encode_user_id($user_id){
        // In production you'll want to salt and add more complex encoding mechanism
        return base64_encode(hash('sha256', $user_id, true));
    }

    private function buildURL($requestPath, $queryParams = array())
    {
        $params = array();
        $url   = $requestPath . '?';
        foreach($queryParams as $key => $value) {
            $params[] = "$key=$value";
        }
        return $url . implode('&', $params);
    }

    private function generic_get_request($requestPath){

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'GET',
            ),
        );
        $context  = stream_context_create($options);
        // file_get_contents can fail setting the E_WARNING flag instead
        // of throwing an exception. To avoid this from showing on your
        // page, change the enviroment or the error reporting level at index.php
        // at the top of the CodeIgniter installation
        try {
            $result = file_get_contents($requestPath, false, $context);
        } catch (Exception $e) {
            $result = "";
        }

        return $result;
    }

}
?>