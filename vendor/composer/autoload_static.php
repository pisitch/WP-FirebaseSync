<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2533abb1ae522b3900aaa591df514f52
{
    public static $files = array (
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        '5255c38a0faeba867671b61dfda6d864' => __DIR__ . '/..' . '/paragonie/random_compat/lib/random.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'J' => 
        array (
            'JC\\' => 3,
            'JCFirebase\\' => 11,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'D' => 
        array (
            'Defuse\\Crypto\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'JC\\' => 
        array (
            0 => __DIR__ . '/..' . '/jaredchu/jc-request/src',
            1 => __DIR__ . '/..' . '/jaredchu/simple-cache/src',
        ),
        'JCFirebase\\' => 
        array (
            0 => __DIR__ . '/..' . '/jaredchu/jc-firebase-php/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Defuse\\Crypto\\' => 
        array (
            0 => __DIR__ . '/..' . '/defuse/php-encryption/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Purl' => 
            array (
                0 => __DIR__ . '/..' . '/jwage/purl/src',
            ),
            'Pdp\\' => 
            array (
                0 => __DIR__ . '/..' . '/jeremykendall/php-domain-parser/library',
            ),
        ),
        'J' => 
        array (
            'JsonMapper' => 
            array (
                0 => __DIR__ . '/..' . '/netresearch/jsonmapper/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2533abb1ae522b3900aaa591df514f52::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2533abb1ae522b3900aaa591df514f52::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit2533abb1ae522b3900aaa591df514f52::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
