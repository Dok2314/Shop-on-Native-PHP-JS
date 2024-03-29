<?php

namespace core\base\settings;

use core\base\controllers\traits\Singleton;

class Settings
{
    use Singleton;

    private array $routes = [
        'admin' => [
            'alias' => 'admin',
            'path' => 'core/admin/controllers/',
            'hrUrl' => false,
            'routes' => [],
        ],
        'settings' => [
            'path' => 'core/base/settings/'
        ],
        'plugins' => [
            'path' => 'core/plugins/',
            'hrUrl' => false,
            'dir' => false,
        ],
        'user' => [
            'path' => 'core/user/controllers/',
            'hrUrl' => true,
            'routes' => [
            ],
        ],
        'default' => [
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
            'outputMethod' => 'outputData',
        ],
    ];

    private array $templateArr = [
        'text' => ['name', 'phone', 'address'],
        'textarea' => ['content', 'keywords'],
    ];

    private string $defaultTable = 'teachers';

    public static function getSettingsByPropName($propName)
    {
        $obj = self::getInstance();
        if(property_exists($obj, $propName)) {
            return $obj->$propName;
        }
    }

    public function clueProperties($childClassName): array
    {
        $props = [];

        foreach ($this as $basePropName => $basePropVal) {
            $childPropVal = $childClassName::getSettingsByPropName($basePropName);

            if(is_array($basePropVal) && is_array($childPropVal)) {
                $props[$basePropName] = $this->arrayMergeRecursive($basePropVal, $childPropVal);
            }

            if(!$childPropVal) $props[$basePropName] = $basePropVal;
        }

        return $props;
    }

    public function arrayMergeRecursive(...$props)
    {
        $baseProps = array_shift($props);

        foreach ($props as $childProp) {
            foreach ($childProp as $childPropKey => $childPropVal) {
                if(isset($baseProps[$childPropKey]) && is_array($childPropVal) && is_array($baseProps[$childPropKey])) {
                    $baseProps[$childPropKey] = $this->arrayMergeRecursive($baseProps[$childPropKey], $childPropVal);
                } else {
                    if(is_int($childPropKey)) {
                        if(!in_array($childPropVal, $baseProps)) $baseProps[] = $childPropVal;
                        continue;
                    }

                    $baseProps[$childPropKey] = $childPropVal;
                }
            }
        }

        return $baseProps;
    }
}