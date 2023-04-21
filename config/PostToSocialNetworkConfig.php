<?php

class PostToSocialNetworkConfig
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key)
    {

        $config = [

            // API KEYS
            'tw_account_id' => get_option('tw_account_id'),
            'tw_consumer_key' => get_option('tw_consumer_key'),
            'tw_consumer_secret' => get_option('tw_consumer_secret'),
            'tw_bearer_token' => get_option('tw_bearer_token'),
            'tw_access_token' => get_option('tw_access_token'),
            'tw_access_token_secret' => get_option('tw_access_token_secret'),
            'fb_access_token' => get_option('fb_access_token'),
            'fb_feed_url' => get_option('fb_feed_url'),
            'dc_webhook_url' => get_option('dc_webhook_url'),

            // SWITCH
            'authorize_discord' => true,
            'authorize_facebook' => true,
            'authorize_twitter' => true,

            // CLASS LOADIND
            'required_classes' => [
                'vendor/autoload.php',
                'core/SnLogger.php',
                'core/SnConnectionInterface.php',
                'core/SnConnectionBase.php',
                'SnTwitterConnection.php',
                'SnFacebookConnection.php',
                'SnDiscordConnection.php',
                'core/SnPostToSocialNetwork.php'
            ],

            // OTHER
            'log_file' => plugin_dir_path(__FILE__) . '../logs/debug.log'
        ];

        return $config[$key];
    }
}