<?php



use Composer\Autoload\ClassLoader;
use Composer\Autoload\ComposerStaticInit47586601d24367a469d805c3a4e51312;

// autoload_real.php @generated by Composer

class autoload_real
{
    private static $loader;

    public static function loadClassLoader($class): void
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__.'/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(['ComposerAutoloaderInit47586601d24367a469d805c3a4e51312', 'loadClassLoader'], true, true);
        self::$loader = $loader = new ClassLoader();
        spl_autoload_unregister(['ComposerAutoloaderInit47586601d24367a469d805c3a4e51312', 'loadClassLoader']);

        $useStaticLoader = PHP_VERSION_ID >= 50_600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require_once __DIR__.'/autoload_static.php';

            call_user_func(ComposerStaticInit47586601d24367a469d805c3a4e51312::getInitializer($loader));
        } else {
            $map = require __DIR__.'/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                $loader->set($namespace, $path);
            }

            $map = require __DIR__.'/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                $loader->setPsr4($namespace, $path);
            }

            $classMap = require __DIR__.'/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->register(true);

        if ($useStaticLoader) {
            $includeFiles = ComposerStaticInit47586601d24367a469d805c3a4e51312::$files;
        } else {
            $includeFiles = require __DIR__.'/autoload_files.php';
        }
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequire47586601d24367a469d805c3a4e51312($fileIdentifier, $file);
        }

        return $loader;
    }
}

function composerRequire47586601d24367a469d805c3a4e51312($fileIdentifier, $file): void
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        require $file;

        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
    }
}
