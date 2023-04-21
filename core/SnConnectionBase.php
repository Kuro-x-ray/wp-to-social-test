<?php

abstract class SnConnectionBase implements SnConnectionInterface {

    protected $newStatus;
    protected $oldStatus;
    protected $post;
    protected $config;

    public function __construct($post, $oldStatus, $newStatus) {

        $this->post = $post;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function canPost() {
        
        if ($this->newStatus != 'publish' || $this->oldStatus == 'publish' || $this->post->post_type != 'post'){
            return false; 
        }else{
            return true;
        }
    }

    public function executePostCurl($url, $postData) {

        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        $errors = curl_error($curl);

        curl_close($curl);
    }

}
