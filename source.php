<?php
/**
 * Created by PhpStorm.
 * User: henrygc
 * Date: 04/04/16
 * Time: 20:59
 */

/* This will take the following parameters

    ref a string [Mandatory]
    w [Optional] default 100
    h [Optional] default 100

*/


foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        define('PHPUNIT_COMPOSER_INSTALL', $file);
        break;
    }
}

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    die(
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'wget http://getcomposer.org/composer.phar' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
}

require PHPUNIT_COMPOSER_INSTALL;

$timer=new HGC\Timer();
$timer->start();
$parameterHandler=HGC\ParameterHandler::getInstance();

$parameterHandler->setSource(HGC\ParameterHandler::SOURCE_GET);

// this is the client parameter
$parameterHandler->setMandatory('c');
$parameterHandler->setValidationRegex('c', '/^(lch|test)*$/');

// this is the Reference for the asset
$parameterHandler->setMandatory('r');
$parameterHandler->setValidationRegex('r', '/^[a-zA-Z0-9-]*$/');

// this is the desired width parameter
$parameterHandler->setDefault('w', 100);
$parameterHandler->setValidationRegex('w', '/^\d*$/');

// this is the desired height parameter
$parameterHandler->setDefault('h', 100);
$parameterHandler->setValidationRegex('h', '/^\d*$/');

// get ready to return an image
$requestUtils=new HGC\RequestUtils();
$requestUtils->set_header('JPEG');

// check that the parameters satisfy the required constraints
try {
    $parameterHandler->assertOK();

    // find out if you have a cached image and do not need to resize
    $parameters=$parameterHandler->getParameters();
    ksort($parameters);
    $rawCacheKey=http_build_query($parameters);
    $cacheKey=md5($rawCacheKey);

    $cacheImageFileName=$cacheKey.'.jpg';
    $cacheFullFileName=__DIR__.'/cache/'.$cacheImageFileName;

    // does a cache file with this name exist?
    $needToGenerate=!file_exists($cacheFullFileName);

    if ($needToGenerate) {
        // find the source image
        $matches=glob(__DIR__.'/sourceImages/'.$parameterHandler->getValue('c').'/'.$parameterHandler->getValue('r').'*.jpg');

        var_dump($matches);
        if (count($matches)!=1) {
            header('XDebug: '.$matches[0]);
            $sourceImageFileName=$matches[0];
        } else {
            throw new \Exception('The r parameter found multiple results.');
        }

        // check that the source image exists
        if (!file_exists($sourceImageFileName)) {
            throw new \Exception('Image not found, '.$sourceImageFileName);
        }
        // proceed to render the image at the required size
        $image=StackOverflow\ResizeImage::generate(
            __DIR__.'/sourceImages/'.$parameterHandler->getValue('c').'/'.$parameterHandler->getValue('r').'.jpg',
            $parameterHandler->getValue('w'),
            $parameterHandler->getValue('h')
        );

        // write the image to the cache file for future use
        imagejpeg($image, $cacheFullFileName, 100);
    }


    $timer->stop();

    header('XImageTimeToRender: '.$timer->getAccumulatedTime());
    header('XImageCacheFileName: '.$cacheImageFileName);

    if ($needToGenerate) {
        header('XImageSource: generated');
        imagejpeg($image, null, 100);
    } else {
        header('XImageSource: from cache');
        readfile($cacheFullFileName);
    }

} catch(\Exception $e) {
    $image=StackOverflow\ResizeImage::generate(
        __DIR__.'/assets/triangular-warning-sign.jpg',
        $parameterHandler->getValue('w'),
        $parameterHandler->getValue('h')
    );

    $timer->stop();

    header('XImageTimeToRender: '.$timer->getAccumulatedTime());
    header('XImageCacheFileName: '.$cacheImageFileName);

    header('XImageSource: from assets resized');
    header('XImageException: '.$e->getMessage());

    imagejpeg($image, null, 100);
    exit();
}


