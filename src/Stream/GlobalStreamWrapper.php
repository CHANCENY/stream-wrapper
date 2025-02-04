<?php

namespace Simp\StreamWrapper\Stream;

use Simp\StreamWrapper\WrapperRegister\WrapperInterface;
use Simp\StreamWrapper\WrapperRegister\WrapperRegister;

class GlobalStreamWrapper implements WrapperInterface
{
    protected string $basePath = 'sites/files';
    protected string $stream_name = 'global';
    private string $realPath = '';
    private $handle;
    private $dirHandle;

    public $context;

    public function __construct()
    {
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getStreamName(): string
    {
        return $this->stream_name;
    }

    private function translatePath(string $path): string
    {
        $streams = WrapperRegister::getWrappers();
        $found_stream = null;
        foreach ($streams as $stream) {
            if ( $this->stream_name === $stream) {
                $found_stream = $stream;
            }
        }

        if($found_stream === null) {
            throw new \Exception("Cannot find stream '{$this->stream_name}'");
        }

        if(str_starts_with($path, $this->stream_name)) {

            $clear_stream = trim($this->stream_name, '/');
            $clear_stream = trim($clear_stream, ':');

            $basePath = str_ends_with($this->basePath, '/') ? $this->basePath : $this->basePath . '/';

            $clean_path = str_replace($clear_stream . '://', $basePath, $path);
            return str_replace('//', '/', $clean_path);
        }

        throw new \Exception("Cannot find stream '{$this->stream_name}'");
    }

    /**
     * @throws \Exception
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        $this->realPath = $this->translatePath($path);
        $this->handle = fopen($this->realPath, $mode);
        return $this->handle !== false;
    }

    public function stream_read(int $count): string|false
    {
        return fread($this->handle, $count);
    }

    public function stream_write(string $data): int
    {
        return fwrite($this->handle, $data);
    }

    public function stream_close(): void
    {
        fclose($this->handle);
    }

    public function stream_eof(): bool
    {
        return feof($this->handle);
    }

    public function stream_stat(): array|false
    {
        return stat($this->realPath);
    }

    public function url_stat(string $path, int $flags): array|false
    {
        $realPath = $this->translatePath($path);
        return @stat($realPath) ?: false;
    }

    public function mkdir(string $path, int $mode, int $options): bool
    {
        $realPath = $this->translatePath($path);
        return mkdir($realPath, $mode, ($options & STREAM_MKDIR_RECURSIVE) !== 0);
    }

    public function unlink(string $path): bool
    {
        return unlink($this->translatePath($path));
    }

    public function rename(string $path_from, string $path_to): bool
    {
        return rename($this->translatePath($path_from), $this->translatePath($path_to));
    }

    public function rmdir(string $path, int $options): bool
    {
        return rmdir($this->translatePath($path));
    }

    public function dir_closedir(): bool
    {
        if (is_resource($this->dirHandle)) {
            closedir($this->dirHandle);
            return true;
        }
        return false;
    }

    public function dir_opendir(string $path, int $options): bool
    {
        $realPath = $this->translatePath($path);
        $this->dirHandle = opendir($realPath);
        return $this->dirHandle !== false;
    }

    public function dir_readdir(): string|false
    {
        return readdir($this->dirHandle);
    }

    public function dir_rewinddir(): bool
    {
        if (is_resource($this->dirHandle)) {
            rewinddir($this->dirHandle);
            return true;
        }
        return false;
    }

    public function stream_cast(int $cast_as): mixed
    {
        return $this->handle;
    }

    public function stream_flush(): bool
    {
        return fflush($this->handle);
    }

    public function stream_lock(int $operation): bool
    {
        return flock($this->handle, $operation);
    }

    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        $realPath = $this->translatePath($path);
        switch ($option) {
            case STREAM_META_TOUCH:
                return touch($realPath);
            case STREAM_META_OWNER_NAME:
            case STREAM_META_OWNER:
                return chown($realPath, $value);
            case STREAM_META_GROUP_NAME:
            case STREAM_META_GROUP:
                return chgrp($realPath, $value);
            case STREAM_META_ACCESS:
                return chmod($realPath, $value);
            default:
                return false;
        }
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return fseek($this->handle, $offset, $whence) === 0;
    }

    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        return false;
    }

    public function stream_tell(): int
    {
        return ftell($this->handle);
    }

    public function stream_truncate(int $new_size): bool
    {
        return ftruncate($this->handle, $new_size);
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
        if (is_resource($this->dirHandle)) {
            closedir($this->dirHandle);
        }
    }
}