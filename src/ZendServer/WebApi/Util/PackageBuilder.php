<?php

namespace FooBarFighters\ZendServer\WebApi\Util;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use ZipArchive;

class PackageBuilder
{
    /**
     * Create a dummy package for testing purposes.
     *
     * @param string $zsAppName
     * @param string $rootDir
     * @param string $versionPrefix
     * @param string $zipName
     *
     * @return string|null
     */
    public static function createDummy(string $zsAppName, string $rootDir, string $versionPrefix = '', string $zipName = 'package.zip'): ?string
    {
        $resourcesDir = dirname(__DIR__, 4) . '/resources/package/dummy';

        //== create a random version
        $version = $versionPrefix . substr(uniqid(null, false), -6);
        $configFile = realpath("$resourcesDir/deployment.xml");
        $phpFile = realpath("$resourcesDir/data/public/index.php");
        $xml = preg_replace(
            [
                '/<name>(.*)<\/name>/',
                '/<release>(.*)<\/release>/',
            ],
            [
                "<name>$zsAppName</name>",
                "<release>$version</release>"
            ],
            file_get_contents($configFile)
        );

        $php = preg_replace('/version = \'(.*)\';/', "version = '$version';", file_get_contents($phpFile));

        file_put_contents($configFile, $xml);
        file_put_contents($phpFile, $php);

        return self::zipDir($resourcesDir, realpath("$rootDir/build") . DIRECTORY_SEPARATOR . $zipName);
    }

    /**
     * @param string $sourceDir   /folder/to/copy
     * @param string $destination /my/destination/folder/package.zip
     *
     * @return string|null
     */
    public static function zipDir(string $sourceDir, string $destination): ?string
    {
        $zip = new ZipArchive();
        if (($code = $zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) !== true) {
            throw new RuntimeException("Failed to create $destination", $code);
        }

        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir), RecursiveIteratorIterator::LEAVES_ONLY);

        //== offset for creating relative paths
        $offset = strlen($sourceDir) + 1;

        foreach ($files as $name => $file) {
            //== skip directories (they get added automatically)
            if ($file->isDir()) {
                continue;
            }
            $filePath = $file->getRealPath();

            //== backslashes seem to make the archive unreadable on Linux based systems
            $pathInZip =  str_replace('\\', '/', substr($filePath, $offset));

            //== add current file to archive
            $zip->addFile($filePath, $pathInZip);
        }

        //== return the path of the zipfile
        return $zip->close() ? realpath($destination) : null;
    }
}