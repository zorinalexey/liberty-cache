<?php

declare(strict_types = 1);

namespace Liberty\Cache;

use Liberty\FileSystem\Dir;

/**
 * Класс Settings
 * @version 0.0.1
 * @package Liberty\Cache
 * @generated Зорин Алексей, please DO NOT EDIT!
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @copyright 2022 разработчик Зорин Алексей Евгеньевич.
 */
final class Settings
{

    /**
     * Установка синглтон
     * @var array
     */
    private static array $instance = [];

    /**
     * Время хранения кешированых файлов в минутах
     * @var int
     */
    public int $timeout = 15;

    /**
     * true - кеширование файлов включено или falsae - выключено
     * @var bool
     */
    public bool $cache = true;

    /**
     * Дирректория хранения кеш-файлов
     * @var string|null
     */
    public ?string $path = null;

    /**
     * Получить время хранения кешированых файлов в секундах
     * @return int
     */
    public function getTimeout(): int
    {
        return ((int)$this->timeout * 60);
    }

    /**
     * Получить директорию для хранения файлов
     * @return string|false
     */
    public function getPath(): string|false
    {
        return Dir::set($this->path)->create()->realPath;
    }

    /**
     * Получить синглтон инстанс
     * @param string|null $class Наименование родительского класса запроса инстанса
     * @return self
     */
    public static function instance(?string $class = null): self
    {
        if ( ! $class) {
            $class = get_called_class();
        }
        if ( ! isset(self::$instance[$class])) {
            self::$instance[$class] = new self();
        }
        return self::$instance[$class];
    }

}
