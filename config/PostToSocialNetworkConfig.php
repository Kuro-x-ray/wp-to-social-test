<?php

class PostToSocialNetworkConfig {
    private static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key) {
        
        $config = [
            'tw_account_id' => get_option('tw_account_id'),
            'tw_consumer_key' => get_option('tw_consumer_key'),
            'tw_consumer_secret' => get_option('tw_consumer_secret'),
            'tw_bearer_token' => get_option('tw_bearer_token'),
            'tw_access_token' => get_option('tw_access_token'),
            'tw_access_token_secret' => get_option('tw_access_token_secret'),
            'fb_access_token' => get_option('fb_access_token'),
            'dc_webhook_url' => get_option('dc_webhook_url'),
            'required_classes' => [
                'core/SnConnectionInterface.php',
                'core/SnConnectionBase.php',
                'SnTwitterConnection.php',
                'SnFacebookConnection.php',
                'SnDiscordConnection.php',
                'core/SnPostToSocialNetwork.php'
            ]
        ];
        
        return $config[$key];
    }
}