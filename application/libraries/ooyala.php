<?php
require_once('application/third_party/php-v2-sdk/OoyalaApi.php');

class Ooyala{

    private $_api;
    private $_api_key = "YOUR KEY";
    private $_api_secret = "YOUR SECRET";
    private $_p_code = "YOUR P_CODE";

    // Helper class included in Ooyala PHP SDK
    private $http_client;

    function __construct() {
        $this->_api = new OoyalaApi($this->_api_key, $this->_api_secret);
        $this->http_client = new OoyalaHttpRequest();
    }

    public function test(){
        // This is the test that is given on the _api. Used to know that everything is working
        $parameters = array("where" => "labels INCLUDES 'Funny dogs'");
        $results = $this->_api->get("assets", $parameters);
        $assets = $results->items;
        echo "Printing assets in the 'Funny dogs' label...";
        foreach($assets as $asset) {
            echo $asset->embed_code . " - " . $asset->name . "\n";
        }
    }

    public function get_embed_token($embed_code, $user_id = null){
        $request = "http://player.ooyala.com/sas/embed_token/";
        $request .= $this->_p_code . "/";
        $request .= $embed_code;
        if (!is_null($user_id)){
             $query_params['account_id'] = $this->encode_user_id($user_id);
        }
        $query_params['api_key'] = $this->_api_key;
        $query_params['expires'] = time() + 600;
        $signature = $this->_api->generateSignature('', $request, $query_params);
        $query_params['signature'] = $signature;
        $url = $this->buildURL($request, $query_params);
        return $url;
    }

    public function get_playhead_time($embed_code, $user_id = null){
         // http://_api.ooyala.com/v2/cross_device_resume/accounts/account_id/viewed_assets/embed_code(identifier for the asset)/playhead_info
        $request = "cross_device_resume/accounts";
        $request .= "/" . urlencode($this->encode_user_id($user_id));
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
        if ($response['status'] != 200){
            return 0;
        }
        return $response['body']['playhead_seconds'];
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

}
?>