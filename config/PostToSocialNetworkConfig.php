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
            'tw_account_id' => 885144630869753856,
            'tw_consumer_key' => 'VhyP6cSbau1oVEJbg9mWfFDJF',
            'tw_consumer_secret' => 'wqCvFPl3hQ8EWM5gZvaITscCcX1J0mek4oNjVSN1WTMJKp3bEZ',
            'tw_bearer_token' => 'AAAAAAAAAAAAAAAAAAAAAHZjmwEAAAAA8GRXfAN5VwD1Molm1Bkvrd7XNdo%3DBRrH2qArk1KP4rsVucu3SQ6fGbyebUhIK9ywC52U5imn9bPkdZ',
            'tw_access_token' => '885144630869753856-csMw82KYWDACVHT6NX9SWGjo8mFlR8z',
            'tw_access_token_secret' => '9otVvqTtk45PQlwfLlnu0s0G9oxZYDX94i1HaZiDGcsap',
            'fb_access_token' => 'EAADhwhpJD2YBANPcJxSecfvaBtWaebylDVa3ZAntVCNgZCF0Wqee7sx1nqLCrbMV4PO7Dfb6fWsJiZCS4otRngJOmWMBoxPyVHIbLZCvCnarwqSPvhNtT7U9kcDsbL2NHkOE82Wg5GmpujJVAW3oev5Oxe59wyX2pfGztrfJqByt5Vijv20vBxsPZALO4oukkC1z48ZCxZCTgZDZD',
            'dc_webhook_url' => 'https://discordapp.com/api/webhooks/1067013273301561354/Tfz4izjJG1MtRWejr4lt6iG7KUloO_KhJM4op54QBRvTk5QHVZZa8YHiUjVS3yYPBiCs',
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