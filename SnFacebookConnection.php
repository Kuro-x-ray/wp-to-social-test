<?php

class SnFacebookConnection extends SnConnectionBase {

    public function __construct($post, $oldStatus, $newStatus) {
        parent::__construct($post, $oldStatus, $newStatus);
    }

    public function executePost() {
        
        $url = 'https://graph.facebook.com/v16.0/jrpgfrance/feed/';
        
        $params = array(
            'message' => get_post_meta($this->post->ID, 'texte_facebook', true),
            'transport' => 'cors',
            'access_token' => PostToSocialNetworkConfig::getInstance()->get('fb_access_token'),
            'link' => get_permalink($this->post->ID)
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

    }

}
