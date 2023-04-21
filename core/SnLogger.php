<?php

class SnLogger
{
    const LEVEL_DEBUG = 'debug';
    const LEVEL_ALERT = 'alert';
    const LEVEL_ERROR = 'error';

    private static $instance;
    private $log_file;

    private function __construct()
    {
        $this->log_file = PostToSocialNetworkConfig::getInstance()->get('log_file');
        // Créer le fichier de journalisation s'il n'existe pas
        if (!file_exists($this->log_file)) {
            touch($this->log_file);
            chmod($this->log_file, 0666);
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function log($level, $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $formatted_message = "[$timestamp][$level] $message\n";
        file_put_contents($this->log_file, $formatted_message, FILE_APPEND);
    }

    public function debug($message)
    {
        $this->log(self::LEVEL_DEBUG, $message);
    }

    public function alert($message)
    {
        $this->log(self::LEVEL_ALERT, $message);
    }

    public function error($message)
    {
        $this->log(self::LEVEL_ERROR, $message);
    }
}