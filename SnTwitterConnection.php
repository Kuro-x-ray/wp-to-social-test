<?php

class SnTwitterConnection extends SnConnectionBase
{

    public function __construct($post, $oldStatus, $newStatus)
    {
        parent::__construct($post, $oldStatus, $newStatus);
    }

    public function executePost()
    {
        if (!PostToSocialNetworkConfig::getInstance()->get('authorize_twitter')) {
            return;
        }

        $client = new \Noweh\TwitterApi\Client($this->getSettings());
        $body = $client->tweet()->performRequest('POST', ['text' => $this->constructMessage()]);


        SnLogger::getInstance()->alert(print_r($body, true));

    }

    private function constructMessage()
    {
        $tweet_text = get_post_meta($this->post->ID, 'texte_twitter', true) . ' ';
        $tweet_text .= get_permalink($this->post->ID);

        return $tweet_text;
    }

    private function getSettings()
    {
        return [
            'account_id' => PostToSocialNetworkConfig::getInstance()->get('tw_account_id'),
            'consumer_key' => PostToSocialNetworkConfig::getInstance()->get('tw_consumer_key'),
            'consumer_secret' => PostToSocialNetworkConfig::getInstance()->get('tw_consumer_secret'),
            'bearer_token' => PostToSocialNetworkConfig::getInstance()->get('tw_bearer_token'),
            'access_token' => PostToSocialNetworkConfig::getInstance()->get('tw_access_token'),
            'access_token_secret' => PostToSocialNetworkConfig::getInstance()->get('tw_access_token_secret')
        ];
    }
}