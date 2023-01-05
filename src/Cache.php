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

    public function __construct(Settings $settings)
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
        if ($this->cacheData && ! $this->chek($this->cacheFile)) {
            $file = File::set($this->getFileName($this->cacheFile));
            $file->content = serialize($this->cacheData);
            $file->create();
        }
        return $this->get();
    }

    /**
     * Проверка необходимости перезаписи файла
     * @param string $file
     * @return bool true если файл срок кеша файла не вышел, false в противном случае
     */
    private function chek(string $file): bool
    {
        $time = time() - $this->settings->getTimeout();
        $fileTime = $this->getTimeCacheFile($this->getFileName($file));
        if ($fileTime >= $time) {
            return true;
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
        $file = File::set($this->getFileName($this->cacheFile));
        if ($file->has() AND $this->chek($this->cacheFile)) {
            $this->cacheData = $file->info()->content;
        }
        return unserialize($this->cacheData);
    }

    /**
     * Обновить или создать кеш
     * @param string $cacheFile Файл кеша
     * @param mixed $cacheData Данные файла кеша
     * @return mixed Данные файла кеша
     */
    public function createOrUpdate(string $cacheFile, mixed $cacheData): mixed
    {
        $this->cacheFile = $cacheFile;
        $this->cacheData = $cacheData;
        $getCache = $this->get();
        if ( ! $getCache) {
            return $this->save();
        }
        return $getCache;
    }

}
