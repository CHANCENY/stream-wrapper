<?php

require_once "vendor/autoload.php";
class PublicWrapper extends \Simp\StreamWrapper\Stream\StreamWrapper
{
    protected string $stream_name = "public";

    protected string $basePath = "sites/public";
}