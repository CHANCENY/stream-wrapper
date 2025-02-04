<?php


use Simp\StreamWrapper\Stream\GlobalStreamWrapper;
use Simp\StreamWrapper\WrapperRegister\WrapperRegister;

require_once "vendor/autoload.php";
require_once "PublicWrapper.php";


// You can register any wrapper you
// NOTE: make sure your Wrapper class extends abstract class StreamWrapper
WrapperRegister::register("global", GlobalStreamWrapper::class);

// Check PublicWrapper example
// You will notice that i have override $stream_name, $basePath only
WrapperRegister::register('public',PublicWrapper::class);


// Here we are using global wrapper to write to file and read file.
file_put_contents("global://example.txt", "\nLorem ipsum dolor sit amet consectetur\n", FILE_APPEND);
print_r(file_get_contents("global://example.txt"));


// Here we are using public wrapper to write to file and read file.
file_put_contents("public://example.txt", "\nHello World!\n", FILE_APPEND);
print_r(file_get_contents("public://example.txt"));

