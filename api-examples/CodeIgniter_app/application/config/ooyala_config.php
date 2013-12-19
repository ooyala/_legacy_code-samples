<?php
// You can find your api_key and secret in Backlot. See
// http://support.ooyala.com/developers/documentation/concepts/api_keys.html
$config['api_key'] = "YOUR_API_KEY";
$config['api_secret'] = "YOUR_API_SECRET";
$config['p_code'] = "YOUR_P_CODE";

// Default embed code and player id to use on the examples.
// You can grab anyone from your Backlot account
$config['default_embed_code'] = "YOUR_DEFAULT_EMBED_CODE";
$config['default_player_id'] = "YOUR_DEFAULT_PLAYER_ID";

// Since Google IMA is set on a player basis, you need to specify
// a player that has Google IMA enabled in order for the examples to work
$config['google_ima_player_id'] = "YOUR_GOOGLE_IMA_PLAYER_ID";
$config['google_ima_ad_tag'] = "YOUR_GOOGLE_IMA_AD_TAG";

// These are optional parameters. You can choose to leave the defaults
// or change them to your convinience
$config['sample_email'] = "alice@ooyala.com";

?>