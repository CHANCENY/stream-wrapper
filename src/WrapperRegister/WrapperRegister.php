<?php

namespace Simp\StreamWrapper\WrapperRegister;

/**
 * Stream wrapper class need to be used before any other php script in order to have
 * your wrapper registered.
 */
class WrapperRegister
{
    private array $wrapper_list;

    public function __construct()
    {
        $this->wrapper_list = stream_get_wrappers();
    }
    public function addWrapper($wrapper, $stream_wrapper_class): void {

        if (!in_array($wrapper, $this->wrapper_list)) {
            if (class_exists($stream_wrapper_class) && (new $stream_wrapper_class) instanceof WrapperInterface) {
                $this->wrapper_list[$wrapper] = $stream_wrapper_class;
                stream_wrapper_register($wrapper, $stream_wrapper_class);
            }
        }
    }

    public function removeWrapper($wrapper): bool
    {
        if (in_array($wrapper, array_keys($this->wrapper_list))) {
            unset($this->wrapper_list[$wrapper]);
            stream_wrapper_unregister($wrapper);
            return true;
        }
        return false;
    }

    public function isWrapperRegistered($wrapper): bool
    {
        return in_array($wrapper, array_keys($this->wrapper_list));
    }

    public function getStreamWrappers(): array
    {
        return $this->wrapper_list;
    }

    public static function wrapperRegister(): WrapperRegister
    {
        return new WrapperRegister();
    }

    public static function register(string $wrapper, string $stream_wrapper_class): void
    {
        $wrapper_object = new self();
        $wrapper_object->addWrapper($wrapper, $stream_wrapper_class);
    }

    public static function remove(string $wrapper): bool
    {
        $wrapper = new self();
        return $wrapper->removeWrapper($wrapper);
    }

    public static function has(string $wrapper): bool
    {
        $wrapper = new self();
        return $wrapper->isWrapperRegistered($wrapper);
    }

    public static function getWrappers(): array
    {
        return new self()->getStreamWrappers();
    }
}