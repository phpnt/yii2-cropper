<?php

namespace phpnt\cropper\assets;

use yii\web\AssetBundle;

class CropperAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $css = [
        'cropper/dist/cropper.css',
    ];
    public $js = [
        'cropper/dist/cropper.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
