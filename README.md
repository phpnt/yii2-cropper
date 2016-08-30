phpNT - Cropper
================================
[![Latest Stable Version](https://poser.pugx.org/phpnt/yii2-cropper/v/stable)](https://packagist.org/packages/phpnt/yii2-cropper) [![Total Downloads](https://poser.pugx.org/phpnt/yii2-cropper/downloads)](https://packagist.org/packages/phpnt/yii2-cropper) [![Latest Unstable Version](https://poser.pugx.org/phpnt/yii2-cropper/v/unstable)](https://packagist.org/packages/phpnt/yii2-cropper) [![License](https://poser.pugx.org/phpnt/yii2-cropper/license)](https://packagist.org/packages/phpnt/yii2-cropper)
### Описание:
#### Вырезание фрагмента изображения с сохранением его в БД и на сервере.

### [DEMO](http://phpnt.com/widget/cropper)

------------
[![Donate button](https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif)](http://phpnt.com/donate/index)
------------

### Социальные сети:
 - [Канал YouTube](https://www.youtube.com/c/phpnt)
 - [Группа VK](https://vk.com/phpnt)
 - [Группа facebook](https://www.facebook.com/Phpnt-595851240515413/)

------------

Установка:

------------

```
php composer.phar require "phpnt/yii2-cropper" "*"
```
или

```
composer require phpnt/yii2-cropper "*"
```

или добавить в composer.json файл

```
"phpnt/yii2-cropper": "*"
```
после загрузки, выполнить миграцию
```
yii migrate --migrationPath=@vendor/phpnt/yii2-cropper/migrations
```
## Использование:
### Подключение:
------------
```php
// в файле настройки приложения (main.php - Advanced или web.php - Basic) 
// в controllerMap
...
'controllerMap' => [
        'images' => [
            'class'         => 'phpnt\cropper\controllers\ImagesController',
        ],
    ],
```

### В представлении, где нужно вырезать и сохранять изображения:
------------
```php
use phpnt\cropper\ImageLoadWidget;

<div class="col-md-12">
        <?= ImageLoadWidget::widget([
            'id' => 'load-user-avatar',                                     // суффикс ID
            'object_id' => $user->id,                                       // ID объекта
            'imagesObject' => $user->photos,                                // уже загруженные изображения
            'images_num' => 1,                                              // максимальное количество изображений
            'images_label' => $user->avatar_label,                          // метка для изображения
            'imageSmallWidth' => 750,                                       // ширина миниатюры
            'imageSmallHeight' => 750,                                      // высота миниатюры
            'imagePath' => '/uploads/avatars/',                             // путь, куда будут записыватся изображения относительно алиаса
            'noImage' => 2,                                                 // 1 - no-logo, 2 - no-avatar, 3 - no-img или путь к другой картинке
            'buttonClass'=> 'btm btn-info',                                 // класс кнопки "обновить аватар"/"загрузить аватар" / по умолчанию btm btn-info
            'previewSize'=> 'file',                                         // размер изображения для превью(либо file_small, либо просто file)
            'pluginOptions' => [                                            // настройки плагина
                'aspectRatio' => 1/1,                                       // установите соотношение сторон рамки обрезки. По умолчанию свободное отношение.
                'strict' => false,                                          // true - рамка не может вызодить за холст, false - может
                'guides' => true,                                           // показывать пунктирные линии в рамке
                'center' => true,                                           // показывать центр в рамке изображения изображения
                'autoCrop' => true,                                         // показывать рамку обрезки при загрузке
                'autoCropArea' => 0.5,                                      // площидь рамки на холсте изображения при autoCrop (1 = 100% - 0 - 0%)
                'dragCrop' => true,                                         // создание новой рамки при клики в свободное место хоста (false - нельзя)
                'movable' => true,                                          // перемещать изображение холста (false - нельзя)
                'rotatable' => true,                                        // позволяет вращать изображение
                'scalable' => true,                                         // мастабирование изображения
                'zoomable' => false,
            ]]);
        ?>
    </div>
    <div class="col-md-12">
        <?= ImageLoadWidget::widget([
            'id' => 'load-user-photos',                                     // суффикс ID
            'object_id' => $user->id,                                       // ID объекта
            'imagesObject' => $user->photos,                                // уже загруженные изображения
            'images_num' => 3,                                              // максимальное количество изображений
            'images_label' => 'userPhotos',                                 // метка для изображения
            'imageSmallWidth' => 750,                                       // ширина миниатюры
            'imageSmallHeight' => 750,                                      // высота миниатюры
            'imagePath' => '/uploads/avatars/',                             // путь, куда будут записыватся изображения относительно алиаса
            'noImage' => 3,                                                 // 1 - no-logo, 2 - no-avatar или путь к другой картинке
            'pluginOptions' => [                                            // настройки плагина
                'aspectRatio' => 16/9,                                      // установите соотношение сторон рамки обрезки. По умолчанию свободное отношение.
                'strict' => false,                                          // true - рамка не может вызодить за холст, false - может
                'guides' => true,                                           // показывать пунктирные линии в рамке
                'center' => true,                                           // показывать центр в рамке изображения изображения
                'autoCrop' => true,                                         // показывать рамку обрезки при загрузке
                'autoCropArea' => 0.5,                                      // площидь рамки на холсте изображения при autoCrop (1 = 100% - 0 - 0%)
                'dragCrop' => true,                                         // создание новой рамки при клики в свободное место хоста (false - нельзя)
                'movable' => true,                                          // перемещать изображение холста (false - нельзя)
                'rotatable' => true,                                        // позволяет вращать изображение
                'scalable' => true,                                         // мастабирование изображения
                'zoomable' => false,
            ]]);
        ?>
    </div>
```
### Пример связей из модели:
------------
```php
public function getPhotos()
    {
        return $this->hasMany(Photo::className(),
            [
                'object_id' => 'id',
                'type' => 'avatar_label',
            ])->andWhere(['deleted' => 0]);
    }
// или
public function getPhotosSome()
    {
        return Photo::find()->where([
            'object_id' => Yii::$app->user->id,
            'type' => 'userPhotoes',
            'deleted' => 0
        ])->all();
    }
```
### Удаление изображений кроном:
# http://phpnt.com/images/delete?alias=@frontend/web
###### , где alias - алиас к папке с изображениями.
# Документация (примеры):
## [Cropper](https://fengyuanchen.github.io/cropper/)
------------
### Версия:
### 0.0.1
------------
### Лицензия:
### [MIT](https://ru.wikipedia.org/wiki/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F_MIT)
------------
