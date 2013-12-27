<?php
require_once('application/third_party/php-v2-sdk/OoyalaApi.php');

/**
 * Library to use as a wrapper for the API.
 */
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

        // Then we get each item.
        $this->_api_key = $this->CI->config->item('api_key');
        $this->_api_secret = $this->CI->config->item('api_secret');
        $this->_p_code = $this->CI->config->item('p_code');
        $this->_api = new OoyalaApi($this->_api_key, $this->_api_secret);
        $this->http_client = new OoyalaHttpRequest();
    }

    /**
     * This is the test given with the Ooyala v2 php sdk.
     * @see https://github.com/ooyala/php-v2-sdk
     */
    public function test(){
        // This is the test that is given on the _api. Used to know that everything is working
        $parameters = array("where" => "labels INCLUDES 'Funny dogs'");
        $results = $this->_api->get("assets", $parameters);
        $assets = $results->items;
        return $assets;
    }

    /**
     * Gets the Ooyala player token for content protection. The token is embedded in the
     * page to avoid copy-paste of the embedded player. It is generated with your
     * API-KEY and secret.
     * It has as an optional parameter a user id (which can be anything) for working
     * with entitlements or device registration.
     * @see http://support.ooyala.com/developers/documentation/concepts/player_v3dev_authoverview.html
     */
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

    /**
     * Returns the playhead time for a given user. If none is given it
     * uses a default one.
     * The URL request looks like
     * http://api.ooyala.com/v2/cross_device_resume/accounts/account_id/viewed_assets/embed_code(identifier for the asset)/playhead_info
     *
     * @see http://support.ooyala.com/developers/documentation/concepts/chapter_xdr.html#chapter_xdr
     */
    public function get_playhead_time($embed_code, $user_id = 'test'){
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

    /**
     * Makes a Discovery call to get related videos given an embed code
     * @param string $embed_code The embed code to get the related videos from
     * @return array Videos related to the give embed code. The response is limited to 5 videos.
     *               If an expection is thrown it will return an empty array.
     * @see http://support.ooyala.com/developers/documentation/tasks/cd_api_get_related.html
     */
    public function get_related_videos($embed_code){
        $parameters = array("limit" => "5");
        try {
            $results = $this->_api->get("discover/similar/assets/", $parameters);
        } catch (OoyalaRequestErrorException $e) {
            $results = array();
        }
        return $results;
    }

    /**
     * Makes a Discovery call to get trending videos worldwide for a given account.
     *
     * @return array Trending videos worldwide limited to 5 videos, an empty array if an error
     *               is found.
     * @see http://support.ooyala.com/developers/documentation/tasks/cd_api_get_trending.html
     */
    public function get_trending_videos(){
        $parameters = array('countries' => 'all',
                            'time' => 'now',
                            'window' => 'day',
                            "limit" => 5 );
        try {
            $results = $this->_api->get("discover/trending/top", $parameters);
        } catch (OoyalaRequestErrorException $e) {
            $results = array();
        }

        return $results;
    }

    /**
     * Generates the necessary meta tags to create a Twitter card.
     *
     * @param string $player_id An Ooyala player id
     * @param string $content_id An Ooyala content id
     *
     * @return the HTML meta tags required for a Twitter card
     *
     * @see http://support.ooyala.com/developers/documentation/tasks/twitter_metadata_request.html
     * @see https://dev.twitter.com/docs/cards
     */
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

    /**
     * Encodes a user id to avoid exposing plain text ids
     * @param string $user_id A user id
     * @return string A base_64 encoded user id
     */
    private function encode_user_id($user_id){
        // In production you'll want to salt and add more complex encoding mechanism
        return base64_encode(hash('sha256', $user_id, true));
    }

    /**
     * Constructs a url given a base path and query params.
     *
     * @param string $requestPath The base path to make the request
     * @param string $queryParams The query params to append to the request
     *
     * @return string The base path with the query params attached
     */
    private function buildURL($requestPath, $queryParams = array())
    {
        $params = array();
        $url   = $requestPath . '?';
        foreach($queryParams as $key => $value) {
            $key = urlencode($key);
            $value = urlencode($value);
            $params[] = "$key=$value";
        }
        return $url . implode('&', $params);
    }

    /**
     * Makes a get request to a given path. Notice that given the way PHP handles errors,
     * this function can set an E_WARNING that can't be caught with a try-catch block.
     * To avoid this from showing on your page, you can change the CodeIgniter enviroment
     * to 'production' or change the error reporting level to ignore E_WARNING.
     * Both of this changes are made in the file index.php at the top of the
     * CodeIgniter installation
     *
     * @param string $requestPath The path to make the request.
     * @return string The raw result from the GET request, an empty string if an expection
     *                was thrown
     */
    private function generic_get_request($requestPath){

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'GET',
            ),
        );
        $context  = stream_context_create($options);
        // file_get_contents can fail setting the E_WARNING flag instead
        // of throwing an exception.
        try {
            $result = file_get_contents($requestPath, false, $context);
        } catch (Exception $e) {
            $result = "";
        }

        return $result;
    }

}
?>