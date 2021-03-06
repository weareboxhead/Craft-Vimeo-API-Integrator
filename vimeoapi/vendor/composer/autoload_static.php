<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5f7b1963471ac95f69397b1b127d6572
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Vimeo\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Vimeo\\' => 
        array (
            0 => __DIR__ . '/..' . '/vimeo/vimeo-api/src/Vimeo',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5f7b1963471ac95f69397b1b127d6572::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5f7b1963471ac95f69397b1b127d6572::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
