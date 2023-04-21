<?php

class SnPostToSocialNetwork {

    private $connections;

    public function __construct(array $connections = []) {
        $this->connections = $connections;
    }

    public function executePosts() {
        foreach ($this->connections as $connection) {
            $connection->executePost();
        }
    }

}
