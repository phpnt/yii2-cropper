<?php
/**
 * Created by PhpStorm.
 * User: phpNT
 * Date: 24.07.2016
 * Time: 21:57
 */

namespace phpnt\cropper\assets;

use yii\web\AssetBundle;

class DistAsset extends AssetBundle
{
    public $sourcePath = '@vendor/phpnt/yii2-cropper';

    public $css = [
        'css/crop.css'
    ];

    public $images = [
        'images/'
    ];
}
