<?php
/**
 * Created by PhpStorm.
 * User: phpNT
 * Date: 18.10.2015
 * Time: 12:07
 */

namespace phpnt\cropper\models;

use yii\base\Model;
use phpnt\cropper\behaviors\ImageBehavior;

class ImageForm extends Model
{
    const EVENT_CREATE_IMAGE = 'createImage';
    const EVENT_UPDATE_IMAGE = 'updateImage';
    const EVENT_DELETE_IMAGE = 'deleteImage';

    public $image;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image'], 'file',
                'skipOnEmpty' => false,
                'extensions' => 'gif, jpeg, jpg, png',
                'mimeTypes'=>'image/gif, image/jpeg, image/jpg, image/png',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'image' => 'Изображение',
        ];
    }

    public function behaviors(){
        return [
            [
                'class' => ImageBehavior::className(),
            ],
        ];
    }

    public function getPhotosByLabel($label, $objectId)
    {
        return Photo::find()
            ->where([
                'type'      => $label,
                'object_id' => $objectId,
                'user_id'   => \Yii::$app->user->id,
                'deleted'   => 0
            ])
            ->orderBy('id')
            ->all();
    }

    public function getDeletedPhotos()
    {
        return Photo::find()
            ->where([
                'deleted'  => 1
            ])->all();
    }

    public  function  createImage() {
        $this->trigger(self::EVENT_CREATE_IMAGE);
    }

    public  function  updateImage() {
        $this->trigger(self::EVENT_UPDATE_IMAGE);
    }

    public  function  deleteImage() {
        $this->trigger(self::EVENT_DELETE_IMAGE);
    }

    public function deleteImageFile($alias, $image_file) {
        if (!file_exists(\Yii::getAlias($alias).$image_file)) {
            return true;
        }
        if (!unlink(\Yii::getAlias($alias).$image_file))
            return false;
        return true;
    }
}