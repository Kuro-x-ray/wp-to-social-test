<?php

class SnFacebookConnection extends SnConnectionBase
{

    public function __construct($post, $oldStatus, $newStatus)
    {
        parent::__construct($post, $oldStatus, $newStatus);
    }

    public function executePost()
    {

        if (!PostToSocialNetworkConfig::getInstance()->get('authorize_facebook')) {
            return;
        }

        $url = PostToSocialNetworkConfig::getInstance()->get('fb_feed_url');
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->constructMessage()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $errors = curl_error($ch);

        curl_close($ch);

        SnLogger::getInstance()->alert(print_r($response, true));
        SnLogger::getInstance()->error(print_r($errors, true));

    }

    private function constructMessage()
    {
        return [
            'message' => get_post_meta($this->post->ID, 'texte_facebook', true),
            'transport' => 'cors',
            'access_token' => PostToSocialNetworkConfig::getInstance()->get('fb_access_token'),
            'link' => get_permalink($this->post->ID)
        ];
    }

}