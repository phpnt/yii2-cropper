<?php
/**
 * Created by PhpStorm.
 * User: phpNT
 * Date: 06.10.2015
 * Time: 19:29
 */

namespace phpnt\cropper;

use yii\web\View;
use yii\base\Widget;
use yii\helpers\Json;
use phpnt\cropper\assets\CropperAsset;
use phpnt\cropper\assets\DistAsset;
use phpnt\cropper\models\ImageForm;
use yii\helpers\Url;

class ImageLoadWidget extends Widget
{
    public $modelName;
    public $id;
    public $object_id;
    public $imagesObject;
    public $images_num;
    public $images_label;
    public $images_temp;
    public $imageSmallWidth;
    public $imageSmallHeight;
    public $deleteUrl;
    public $autoloadUrl;
    public $createImageText     = 'Загрузить фото';
    public $updateImageText     = 'Изменить фото';
    public $deleteImageText     = 'Удалить фото';
    public $headerModal         = 'Загрузить аватар';   // заголовок в модальном окне
    public $buttonClass         = 'btm btn-info';   // класс для кнопок - загрузить/обновить
    public $sizeModal           = 'modal-lg';           // размер модального окна
    public $frontendUrl         = '';
    public $baseUrl             = '@webroot';           // алиас к изображениям
    public $imagePath;
    public $noImage             = 1;                    // 1 - no-logo, 2 - no-avatar или свое значение
    public $loaderImage         = 1;                    // 1 - 1x1, 2 - 3x4
    public $backend             = false;
    public $classesWidget       = [
        'imageClass' => 'imageLoaderClass',
        'buttonDeleteClass' => 'btn btn-xs btn-danger btn-imageDelete glyphicon glyphicon-trash glyphicon',
        'imageContainerClass' => 'col-md-3',
        'formImagesContainerClass' => 'formImageContainer',
    ];
    public $options = [];
    public $pluginOptions = [];
    public $cropBoxData = [
        'left' => 10,                                   // смещение слева
        'top' => 10,                                    // смещение вниз
    ];
    public $canvasData = [                              // начальные настройки холста
        //'width' => 500,                               // ширина
        //'height' => 500                               // высота
    ];

    private $modelImageForm;

    public function init()
    {
        parent::init();
        $this->modelImageForm = new ImageForm();
        $this->deleteUrl = Url::to(['/images/delete-image']);
        $this->autoloadUrl = Url::to(['/images/autoload-image']);

        if ($this->images_num == 1) {
            $this->registerClientScriptOne();
        } else {
            $this->registerClientScriptMany();
        }
    }

    public function run()
    {
        return $this->render(
            'view',
            [
                'widget' => $this,
                'modelImageForm' => $this->modelImageForm,
            ]);
    }

    public function registerClientScript()
    {
        $view = $this->getView();
        CropperAsset::register($view);
        $assets = DistAsset::register($view);
        if ($this->noImage == 1) {
            $this->noImage = $assets->baseUrl.'/images/no-logo.png';
        } elseif ($this->noImage == 2) {
            $this->noImage = $assets->baseUrl.'/images/no-avatar.png';
        } elseif ($this->noImage == 3) {
            $ratio = round($this->pluginOptions['aspectRatio'], 2);
            switch ($ratio) {
                case 1:
                    $this->noImage = $assets->baseUrl.'/images/no-img-1x1.png';
                    break;
                case 1.33:
                    $this->noImage = $assets->baseUrl.'/images/no-img-4x3.png';
                    break;
                case 0.75:
                    $this->noImage = $assets->baseUrl.'/images/no-img-3x4.png';
                    break;
                case 1.78:
                    $this->noImage = $assets->baseUrl.'/images/no-img-16x9.png';
                    break;
                case 0.56:
                    $this->noImage = $assets->baseUrl.'/images/no-img-9x16.png';
                    break;
            }
        }

        if ($this->loaderImage == 1) {
            $this->loaderImage = $assets->baseUrl.'/images/loader_1x1.gif';
        } elseif ($this->noImage == 2) {
            $this->loaderImage = $assets->baseUrl.'/images/loader_3x4.gif';
        }
    }

    public function registerClientScriptMany()
    {
        $this->registerClientScript();
        $view = $this->getView();
        // Пользовательские настройки переводим в JSON
        $options = Json::encode($this->pluginOptions);
        $cropBoxData = Json::encode($this->cropBoxData);
        $canvasData = Json::encode($this->canvasData);

        $imageClass = $this->classesWidget['imageClass'];
        $buttonDeleteClass = $this->classesWidget['buttonDeleteClass'];
        $imageContainerClass = $this->classesWidget['imageContainerClass'];
        $formImagesContainerClass = $this->classesWidget['formImagesContainerClass'];

        $js = <<< JS
            var loadFileMany = function(event) {                               
                var outputMany = document.getElementById("previewImg-$this->id");        
                outputMany.src = URL.createObjectURL(event.target.files[0]);  
                $("#modal-$this->id").modal('show');             
            };
JS;
        $view->registerJs($js, View::POS_HEAD);

        $js = <<< JS
            var deleteImageMany = function(event) {                        
                if (confirm("Удалить изображение?")) {                                  
                    var imageDataMany = JSON.stringify({
                        modelName: "$this->modelName",
                        id: "$this->id",
                        object_id: "$this->object_id",
                        image_id: window.idImage,
                        images_num: "$this->images_num",
                        images_label: "$this->images_label",
                        images_temp: "$this->images_temp",
                        imageSmallWidth: "$this->imageSmallWidth",
                        imageSmallHeight: "$this->imageSmallHeight",
                        createImageText:  "$this->createImageText",
                        updateImageText:  "$this->updateImageText",
                        deleteImageText:  "$this->deleteImageText",
                        deleteUrl: "$this->deleteUrl",
                        frontendUrl: "$this->frontendUrl",
                        baseUrl: "$this->baseUrl",
                        imagePath: "$this->imagePath",
                        noImage: "$this->noImage",
                        loaderImage: "$this->loaderImage",
                        backend: "$this->backend",
                        imageClass: "$imageClass",
                        buttonDeleteClass: "$buttonDeleteClass",
                        imageContainerClass: "$imageContainerClass",
                        formImagesContainerClass: "$formImagesContainerClass"
                    });
                    $.pjax({
                        type: "POST",
                        url: "$this->deleteUrl",
                        data: {imageData: imageDataMany},
                        container: "#images-widget-$this->id",
                        scrollTo: false,
                        push: false
                    });
                } else {
                return false;
                }
            };
JS;
        $view->registerJs($js, View::POS_HEAD);

        $js = <<< JS
            var modalBoxMany = $("#modal-$this->id"),                                
                imageMany = $("#modal-$this->id .crop-image-container-$this->id > img"),
                cropBoxData = $cropBoxData,
                canvasData = $canvasData,
                cropUrl;                                                   

            modalBoxMany.on("shown.bs.modal", function (event) {              
                cropUrl = $("#crop-url-$this->id").attr("$this->autoloadUrl");
                imageMany.cropper($.extend({                                
                    built: function () {                                    
                        imageMany.cropper('setCropBoxData', cropBoxData);
                        imageMany.cropper('setCanvasData', canvasData);
                    },
                    dragend: function() {                                   
                        cropBoxData = imageMany.cropper('getCropBoxData');  
                        canvasData = imageMany.cropper('getCanvasData');    
                    }
                }, $options));                                              

            }).on('hidden.bs.modal', function () {                          
                cropBoxData = imageMany.cropper('getCropBoxData');          
                canvasData = imageMany.cropper('getCanvasData');            
                imageMany.cropper('destroy');                               
            });
JS;
        $view->registerJs($js);

        $js = <<< JS
                $(document).on("click", "#modal-$this->id .crop-submit", function(e) {
                    e.preventDefault();                                           
                   
                    var form = $("#image-form-$this->id");

                    cropBoxData = imageMany.cropper('getCropBoxData');            
                    canvasData = imageMany.cropper('getCanvasData');              

                    var cropData = JSON.stringify(imageMany.cropper("getData"));
                    form.trigger('submit');

                    form.on("beforeSubmit", function(e) {

                        $("#image_id-$this->id").attr("value", window.idImage);    
                        var cropData = JSON.stringify(imageMany.cropper("getData"));
                        $("#imageCrop-$this->id").attr("value", cropData);
                    });
                    modalBoxMany.modal("hide");                                     
                });
JS;
        $view->registerJs($js);
    }

    public function registerClientScriptOne()
    {
        $this->registerClientScript();
        $view = $this->getView();

        $options = Json::encode($this->pluginOptions);
        $cropBoxData = Json::encode($this->cropBoxData);
        $canvasData = Json::encode($this->canvasData);

        $imageClass = $this->classesWidget['imageClass'];
        $buttonDeleteClass = $this->classesWidget['buttonDeleteClass'];
        $imageContainerClass = $this->classesWidget['imageContainerClass'];
        $formImagesContainerClass = $this->classesWidget['formImagesContainerClass'];

        $js = <<< JS
            var loadFile = function(event) {                                
                var output = document.getElementById("previewImg-$this->id");
                output.src = URL.createObjectURL(event.target.files[0]);  
                $("#modal-$this->id").modal('show');                
            };
JS;
        $view->registerJs($js, View::POS_HEAD);

        $js = <<< JS
            var deleteImage = function(event) {                     
                if (confirm("Удалить изображение?")) {              
                    var imageData = JSON.stringify({
                        modelName: "$this->modelName",
                        id: "$this->id",
                        object_id: "$this->object_id",
                        image_id: window.idImage,
                        images_num: "$this->images_num",
                        images_label: "$this->images_label",
                        images_temp: "$this->images_temp",
                        imageSmallWidth: "$this->imageSmallWidth",
                        imageSmallHeight: "$this->imageSmallHeight",
                        createImageText:  "$this->createImageText",
                        updateImageText:  "$this->updateImageText",
                        deleteImageText:  "$this->deleteImageText",
                        deleteUrl: "$this->deleteUrl",
                        frontendUrl: "$this->frontendUrl",
                        baseUrl: "$this->baseUrl",
                        imagePath: "$this->imagePath",
                        noImage: "$this->noImage",
                        loaderImage: "$this->loaderImage",
                        backend: "$this->backend",
                        imageClass: "$imageClass",
                        buttonDeleteClass: "$buttonDeleteClass",
                        imageContainerClass: "$imageContainerClass",
                        formImagesContainerClass: "$formImagesContainerClass"
                    });
                    $.pjax({
                        type: "POST",
                        url: "$this->deleteUrl",
                        data: {imageData: imageData},
                        container: "#images-widget-$this->id",
                        scrollTo: false,
                        push: false
                    });
                } else {
                return false;
                }
            };
JS;
        $view->registerJs($js, View::POS_HEAD);

        $js = <<< JS
            var modalBox = $("#modal-$this->id"),                                 
                image = $("#modal-$this->id .crop-image-container-$this->id > img"),       
                cropBoxData = $cropBoxData,
                canvasData = $canvasData,
                cropUrl;                                                    

            modalBox.on("shown.bs.modal", function (event) {                
                cropUrl = $("#crop-url-$this->id").attr("$this->autoloadUrl");   
                image.cropper($.extend({                                   
                    built: function () {                                   
                        // Начальные настройки изображения
                        image.cropper('setCropBoxData', cropBoxData);
                        image.cropper('setCanvasData', canvasData);
                    },
                    dragend: function() {                                   
                        cropBoxData = image.cropper('getCropBoxData');      
                        canvasData = image.cropper('getCanvasData');       
                    }
                }, $options));                                           

            }).on('hidden.bs.modal', function () {                          
                cropBoxData = image.cropper('getCropBoxData');              
                canvasData = image.cropper('getCanvasData');               
                image.cropper('destroy');                                   
            });
JS;
        $view->registerJs($js);

        $js = <<< JS
                $(document).on("click", "#modal-$this->id .crop-submit", function(e) {   
                    e.preventDefault();                                            
                    //console.log(image.cropper("getData"));                      
                    var form = $("#image-form-$this->id");

                    cropBoxData = image.cropper('getCropBoxData');              
                    canvasData = image.cropper('getCanvasData');               

                    var cropData = JSON.stringify(image.cropper("getData"));
                    form.trigger('submit');

                    form.on("beforeSubmit", function(e) {

                        $("#image_id-$this->id").attr("value", window.idImage);     
                        var cropData = JSON.stringify(image.cropper("getData"));
                        $("#imageCrop-$this->id").attr("value", cropData);
                    });
                    modalBox.modal("hide");                                    
                });
JS;
        $view->registerJs($js);
    }
}