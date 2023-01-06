<?php

declare(strict_types = 1);

namespace Liberty\Cache;

use Liberty\Cache\Settings;
use Liberty\FileSystem\File;

/**
 * Класс Cache
 * @version 0.0.1
 * @package Liberty\Cache
 * @generated Зорин Алексей, please DO NOT EDIT!
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @copyright 2022 разработчик Зорин Алексей Евгеньевич.
 */
final class Cache
{

    private Settings $settings;

    /**
     * Наименование файла кеша
     * @var string|null
     */
    public ?string $cacheFile = null;

    /**
     * Данные для сохранения в файл кеша
     * @var mixed
     */
    public mixed $cacheData = null;
    private static array $instance = [];

    private function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Поолучить наименование файла кеша
     * @param string $file
     * @return type
     */
    private function getFileName(string $file)
    {
        return $this->settings->getPath() . DIRECTORY_SEPARATOR . md5($file);
    }

    /**
     * Сохранить данные в кеш
     * @return mixed
     */
    public function save(): mixed
    {
        if ($this->settings->cache) {
            if ($this->cacheFile && $this->cacheData) {
                $file = File::set($this->getFileName($this->cacheFile));
                $file->content = serialize($this->cacheData);
                if ($file->rewrite()) {
                    return $this->cacheData;
                }
            }
        }
        return false;
    }

    /**
     * Получить время последнего изменения файла
     * @param string $path
     * @return int
     */
    private function getTimeCacheFile(string $path): int
    {
        $time = 0;
        $info = File::set($path)->info();
        if ($info) {
            $time = $info->mTime;
        }
        return (int)$time;
    }

    /**
     * Получить данные из кеша
     * @return mixed
     */
    public function get(): mixed
    {
        if ($this->settings->cache) {
            $cacheFile = $this->getFileName($this->cacheFile);
            $time = $this->getTimeCacheFile($cacheFile);
            $cacheTime = time() - $this->settings->getTimeout();
            if ($this->cacheFile && $time >= $cacheTime) {
                $file = File::set($cacheFile)->info();
                return unserialize($file->content);
            }
        }
        return false;
    }

    public static function instance(string $cacheClassName = self::class): self
    {
        $instanceName = md5($cacheClassName);
        if ( ! isset(self::$instance[$instanceName])) {
            $settings = Settings::instance($cacheClassName);
            self::$instance[$instanceName] = new self($settings);
        }
        return self::$instance[$instanceName];
    }

}
