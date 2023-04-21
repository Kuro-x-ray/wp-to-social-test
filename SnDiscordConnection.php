<?php

class SnDiscordConnection extends SnConnectionBase
{

    public function __construct($post, $oldStatus, $newStatus)
    {
        parent::__construct($post, $oldStatus, $newStatus);
    }

    public function executePost()
    {
        if (!PostToSocialNetworkConfig::getInstance()->get('authorize_discord')) {
            return;
        }

        $this->executePostCurl(
            PostToSocialNetworkConfig::getInstance()->get('dc_webhook_url'),
            ['content' => $this->constructMessage()]
        );

    }

    private function constructMessage()
    {

        $message = '[Nouveau] ' . get_post_meta($this->post->ID, 'texte_discord', true) . ' ';
        $message .= get_permalink($this->post->ID);

        return $message;
    }

}