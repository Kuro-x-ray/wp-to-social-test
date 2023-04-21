<?php

class SnTwitterConnection extends SnConnectionBase
{

    public function __construct($post, $oldStatus, $newStatus)
    {
        parent::__construct($post, $oldStatus, $newStatus);
    }

    public function executePost()
    {

        $settings = array(
            'account_id' => PostToSocialNetworkConfig::getInstance()->get('tw_account_id'),
            'consumer_key' => PostToSocialNetworkConfig::getInstance()->get('tw_consumer_key'),
            'consumer_secret' => PostToSocialNetworkConfig::getInstance()->get('tw_consumer_secret'),
            'bearer_token' => PostToSocialNetworkConfig::getInstance()->get('tw_bearer_token'),
            'access_token' => PostToSocialNetworkConfig::getInstance()->get('tw_access_token'),
            'access_token_secret' => PostToSocialNetworkConfig::getInstance()->get('tw_access_token_secret')
        );

        $client = new \Noweh\TwitterApi\Client($settings);
        $tweet_text = get_post_meta($this->post->ID, 'texte_twitter', true) . ' ';
        $tweet_text .= get_permalink($this->post->ID);
        $client->tweet()->performRequest('POST', ['text' => $tweet_text]);
    }

    private function get_wordpress_post_hashtags($post_id)
    {
        // Get post categories
        $categories = wp_get_post_categories($post_id);

        // Generate hashtags from categories
        $hashtags = array();
        foreach ($categories as $category_id) {
            $category = get_category($category_id);
            $words = explode(' ', ucwords($category->name));
            $hashtag = '#' . implode('', $words);
            $hashtags[] = $hashtag;
        }

        // Concatenate hashtags into a string
        $hashtags_str = implode(' ', $hashtags);

        return $hashtags_str;
    }

}