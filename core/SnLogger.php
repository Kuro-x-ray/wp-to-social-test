<?php 
class SnLogger
{
    private $file;
    public function __construct()
    {
        $this->file = plugin_dir_url(__FILE__).'../logs/debug.log';
    }
}