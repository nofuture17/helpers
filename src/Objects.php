<?php
/**
 * Created by PhpStorm.
 * User: nofuture17
 * Date: 22.04.2017
 * Time: 12:21
 */

namespace nofuture17\helpers;


class Objects
{
    /**
     * Конфигурирует объект из массива
     * @param $object объект
     * @param array $config ассоциотивный массив
     * @param bool $force насильно устанавливать значения свойствам
     * @return void
     */
    public static function configure($object, array $config, $force = true)
    {
        if (is_array($config) && !empty($config) && is_object($object)) {
            foreach ($config as $property => $value) {
                if(property_exists($object, $property)) {
                    static::setPropery($object, $property, $value, $force);
                }
            }
        }
    }

    /**
     * Устанавливает значение свойству объекта
     * @param $object объект
     * @param $propery имя свойства
     * @param $value устанавливаемое значение
     * @param bool $force насильно устанавливать значения свойствам
     */
    public static function setPropery($object, $propery, $value, $force = false)
    {
        $reflection = new \ReflectionProperty($object, $propery);
        $modifiers = $reflection->getModifiers();

        if ($modifiers == \ReflectionProperty::IS_PUBLIC) {
            $reflection->setValue($value);
        } elseif ($force) {
            $reflection->setAccessible(\ReflectionProperty::IS_PUBLIC);
            $reflection->setValue($value);
            $reflection->setAccessible($modifiers);
        }
    }

    /**
     * Создаёт объект из указанных данных
     * В качестве данных может быть строка с именем класса или
     * массив конфигурации с ключами class и config
     * @param string|array $data
     * @return mixed
     */
    public static function createObject($data)
    {
        if (is_string($data)) {
            $object = new $data();
        } elseif (is_array($data) && !empty($data['class']) && class_exists($data['class'])) {
            $object = new $data['class']();
            if (!empty($data['config']) && is_array($data['config'])) {
                static::configure($object, $data['config']);
            }
        } else {
            return null;
        }

        return $object;
    }
}