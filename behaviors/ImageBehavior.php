<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 17.10.2015
 * Time: 12:45
 */
namespace phpnt\cropper\behaviors;

use phpnt\cropper\models\Photo;
use yii\base\Behavior;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;
use yii\helpers\Json;
use phpnt\cropper\models\ImageForm;
use yii\db\Exception;

/* @var \phpnt\cropper\models\Photo */

class ImageBehavior extends Behavior
{
    private $imageData;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->imageData = \Yii::$app->request->post('imageData');
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            ImageForm::EVENT_CREATE_IMAGE => 'createImage',
            ImageForm::EVENT_UPDATE_IMAGE => 'updateImage',
            ImageForm::EVENT_DELETE_IMAGE => 'deleteImage',
        ];
    }

    public function createImage()
    {
        $md5_1 = \Yii::$app->security->generateRandomString(2);
        $md5_2 = \Yii::$app->security->generateRandomString(2);
        /* @var $modelPhoto Photo */
        $paramsCrop = Json::decode($this->imageData['imageCrop']);
        $model = new ImageForm();
        $model->image = UploadedFile::getInstance($model, 'image');

        if($model->validate()):
            $smallFileName = time().'_'.\Yii::$app->user->id.'_small.'.$model->image->extension;
            $fileName = time().'_'.\Yii::$app->user->id.'.'.$model->image->extension;

            $modelPhoto = new Photo();
            $modelPhoto->file       = $this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName;
            $modelPhoto->file_small = $this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$smallFileName;
            $modelPhoto->type       = $this->imageData['images_label'];
            $modelPhoto->object_id  = $this->imageData['object_id'];
            $modelPhoto->user_id    = \Yii::$app->user->id;

            $commit = true;

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if($modelPhoto->save() && $commit):
                    FileHelper::createDirectory(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/', $mode = 509);
                    if($model->image->saveAs(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName)):
                        $image = Image::getImagine();
                        $newImage = $image->open(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName);
                        $newImage->rotate($paramsCrop['rotate']);

                        $paramsCrop['x'] = ($paramsCrop['x'] > 0) ? $paramsCrop['x'] : 0;
                        $paramsCrop['y'] = ($paramsCrop['y'] > 0) ? $paramsCrop['y'] : 0;
                        if($newImage->crop(
                            new Point($paramsCrop['x'], $paramsCrop['y']),
                            new Box($paramsCrop['width'], $paramsCrop['height']))
                            ->save(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName)):

                            $newImage = $image->open(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName);
                            $newImage->thumbnail(new Box($this->imageData['imageSmallWidth'], $this->imageData['imageSmallHeight']))
                                ->save(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$smallFileName);
                            $transaction->commit();
                        endif;
                    endif;
                    \Yii::$app->session->set('image', $modelPhoto->id);
                    \Yii::$app->session->remove('error');
                else:
                    \Yii::$app->session->set('error', 'Изображение не добавлено.');
                    \Yii::$app->session->remove('image');
                endif;
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        else:
            \Yii::$app->session->set('error', $model->errors['image']['0']);
            \Yii::$app->session->remove('image');
        endif;
    }

    public function updateImage()
    {
        $md5_1 = \Yii::$app->security->generateRandomString(2);
        $md5_2 = \Yii::$app->security->generateRandomString(2);
        /* @var $modelPhoto \phpnt\cropper\models\Photo */
        $paramsCrop = Json::decode($this->imageData['imageCrop']);
        $model = new ImageForm();
        $model->image = UploadedFile::getInstance($model, 'image');

        if($model->validate()):
            $smallFileName = time().'_'.\Yii::$app->user->id.'_small.'.$model->image->extension;
            $fileName = time().'_'.\Yii::$app->user->id.'.'.$model->image->extension;

            $modelPhoto = Photo::findOne($this->imageData['image_id']);
            $modelDeletePhoto = new Photo();
            $modelDeletePhoto->file       = $modelPhoto->file;
            $modelDeletePhoto->file_small = $modelPhoto->file_small;
            $modelDeletePhoto->type       = $modelPhoto->type;
            $modelDeletePhoto->object_id  = $modelPhoto->object_id;
            $modelDeletePhoto->user_id    = $modelPhoto->user_id;
            $modelDeletePhoto->deleted = 1;
            $modelDeletePhoto->save();

            $deleteFile = $modelPhoto->file;
            $deleteSmallFile = $modelPhoto->file_small;

            $modelPhoto->file       = $this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName;
            $modelPhoto->file_small = $this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$smallFileName;
            $modelPhoto->type       = $this->imageData['images_label'];
            $modelPhoto->object_id  = $this->imageData['object_id'];
            $modelPhoto->user_id    = \Yii::$app->user->id;

            $commit = true;

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if($modelPhoto->save() && $commit):
                    FileHelper::createDirectory(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/', $mode = 509);
                    if($model->image->saveAs(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName)):
                        $image = Image::getImagine();
                        $newImage = $image->open(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName);
                        $newImage->rotate($paramsCrop['rotate']);

                        $paramsCrop['x'] = ($paramsCrop['x'] > 0) ? $paramsCrop['x'] : 0;
                        $paramsCrop['y'] = ($paramsCrop['y'] > 0) ? $paramsCrop['y'] : 0;
                        if($newImage->crop(
                            new Point($paramsCrop['x'], $paramsCrop['y']),
                            new Box($paramsCrop['width'], $paramsCrop['height']))
                            ->save(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName)):

                            $newImage = $image->open(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName);
                            $newImage->thumbnail(new Box($this->imageData['imageSmallWidth'], $this->imageData['imageSmallHeight']))
                                ->save(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$smallFileName);

                            if($this->deleteImageFile($deleteFile) && $this->deleteImageFile($deleteSmallFile))
                                $transaction->commit();
                        endif;
                    endif;
                    \Yii::$app->session->set('image', $modelPhoto->id);
                    \Yii::$app->session->remove('error');
                else:
                    \Yii::$app->session->set('error', 'Изображение не добавлено.');
                    \Yii::$app->session->remove('image');
                endif;
            } catch (Exception $e) {
                $this->deleteImageFile(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$fileName);
                $this->deleteImageFile(\Yii::getAlias($this->imageData['baseUrl']).$this->imageData['imagePath'].$md5_1.'/'.$md5_2.'/'.$smallFileName);
                $transaction->rollBack();
            }
        else:
            \Yii::$app->session->set('error', $model->errors['image']['0']);
            \Yii::$app->session->remove('image');
        endif;
    }

    public function deleteImage()
    {
        /* @var $modelPhoto \phpnt\cropper\models\Photo */
        $paramsImageDeleteData = Json::decode($this->imageData);
        $modelPhoto = Photo::findOne($paramsImageDeleteData['image_id']);
        $modelPhoto->deleted = 1;
        $modelPhoto->save();
    }

    public function deleteImageFile($image_file) {
        if (!file_exists(\Yii::getAlias($this->imageData['baseUrl']).$image_file)) {
            return false;
        }

        if (!unlink(\Yii::getAlias($this->imageData['baseUrl']).$image_file)) {
            return false;
        }
        return true;
    }
}