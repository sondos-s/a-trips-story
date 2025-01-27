<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7e984921ba1416bc5392a52919a82a02
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7e984921ba1416bc5392a52919a82a02::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7e984921ba1416bc5392a52919a82a02::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7e984921ba1416bc5392a52919a82a02::$classMap;

        }, null, ClassLoader::class);
    }
}