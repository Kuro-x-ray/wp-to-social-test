<?php

abstract class SnConnectionBase implements SnConnectionInterface
{

    protected $newStatus;
    protected $oldStatus;
    protected $post;
    protected $config;

    public function __construct($post, $oldStatus, $newStatus)
    {
        if (null === $post) {
            SnLogger::getInstance()->error('The post can\'t be null');
            throw new InvalidArgumentException('The post can\'t be null');
        }

        $this->post = $post;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function canPost()
    {
        return $this->newStatus === 'publish'
            && $this->oldStatus !== 'publish'
            && $this->post->post_type === 'post';
    }

    public function executePostCurl($url, $postData)
    {

        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ]);

        $response = curl_exec($curl);
        $errors = curl_error($curl);

        SnLogger::getInstance()->alert(print_r($response, true));
        SnLogger::getInstance()->error(print_r($errors, true));

        curl_close($curl);
    }

}