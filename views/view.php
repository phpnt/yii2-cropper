<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $widget \phpnt\cropper\ImageLoadWidget */
/* @var $modelImageForm \phpnt\cropper\models\ImageForm */
/* @var $attribute string */
/* @var $imagePath string */

$render = ($widget->images_num == 1) ? '_image' : '_image-many';

echo $this->render(
    $render,
    [
        'modelName'                 => $widget->modelName,
        'id'                        => $widget->id,
        'object_id'                 => $widget->object_id,
        'images_num'                => $widget->images_num,
        'images_label'              => $widget->images_label,
        'images_temp'               => $widget->images_temp,
        'imageSmallWidth'           => $widget->imageSmallWidth,
        'buttonClass'               => $widget->buttonClass,
        'previewSize'               => $widget->previewSize,
        'imageSmallHeight'          => $widget->imageSmallHeight,
        'imagesObject'              => $widget->imagesObject,
        'modelImageForm'            => $modelImageForm,
        'createImageText'           => $widget->createImageText,
        'updateImageText'           => $widget->updateImageText,
        'deleteImageText'           => $widget->deleteImageText,
        'frontendUrl'               => $widget->frontendUrl,
        'baseUrl'                   => $widget->baseUrl,
        'imagePath'                 => $widget->imagePath,
        'noImage'                   => $widget->noImage,
        'loaderImage'               => $widget->loaderImage,
        'backend'                   => $widget->backend,
        'imageClass'                => $widget->classesWidget['imageClass'],
        'buttonDeleteClass'         => $widget->classesWidget['buttonDeleteClass'],
        'imageContainerClass'       => $widget->classesWidget['imageContainerClass'],
        'formImagesContainerClass'  => $widget->classesWidget['formImagesContainerClass'],
    ]);

Modal::begin([
    'size' => $widget->sizeModal,
    'header' => '<h2>'.$widget->headerModal.'</h2>',
    'footer' => '
        <button type="button" class="btn btn-primary crop-submit">Применить</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
    ',
    'toggleButton' => false,
    'options' => [
        'id' => 'modal-'.$widget->id,
    ]
]);
?>
    <div class="crop-image-container-<?= $widget->id ?>">

        <?= Html::img('', [
            'id' => 'previewImg-'.$widget->id,
            'class' => 'cropper-image img-responsive',
            'alt' => 'crop-image',
            'style' => 'width: 100%'
        ]) ?>
    </div>
<?php
Modal::end();

