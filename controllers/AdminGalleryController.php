<?php

namespace app\controllers;

use Yii;
use app\models\Gallery;
use app\models\Invitation;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\web\Response;

/**
 * AdminGalleryController implements the CRUD actions for Gallery model.
 */
class AdminGalleryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Gallery models for a specific invitation.
     * 
     * @param int $invitation_id
     * @return string
     */
    public function actionIndex($invitation_id)
    {
        $invitation = $this->findInvitation($invitation_id);
        
        $dataProvider = new ActiveDataProvider([
            'query' => Gallery::find()
                ->where(['invitation_id' => $invitation_id])
                ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'invitation' => $invitation,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates new Gallery models (multiple upload).
     * 
     * @param int $invitation_id
     * @return string|\yii\web\Response
     */
    public function actionCreate($invitation_id)
    {
        $invitation = $this->findInvitation($invitation_id);
        $model = new Gallery();
        $model->invitation_id = $invitation_id;

        if ($model->load(Yii::$app->request->post())) {
            $imageFiles = UploadedFile::getInstances($model, 'imageFile');
            
            if (!empty($imageFiles)) {
                // Get current max order
                $maxOrder = Gallery::find()
                    ->where(['invitation_id' => $invitation_id])
                    ->max('sort_order');
                $currentOrder = $maxOrder ? $maxOrder : 0;
                
                $successCount = 0;
                $failCount = 0;
                
                // Upload each file
                foreach ($imageFiles as $file) {
                    $galleryModel = new Gallery();
                    $galleryModel->invitation_id = $invitation_id;
                    $galleryModel->caption = $model->caption; // Use same caption for all
                    $galleryModel->imageFile = $file;
                    $galleryModel->sort_order = ++$currentOrder;
                    
                    if ($this->uploadImage($galleryModel)) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                }
                
                // Show result message
                if ($successCount > 0) {
                    Yii::$app->session->setFlash('success', "{$successCount} foto berhasil ditambahkan.");
                }
                if ($failCount > 0) {
                    Yii::$app->session->setFlash('warning', "{$failCount} foto gagal diupload.");
                }
                
                return $this->redirect(['index', 'invitation_id' => $invitation_id]);
            } else {
                Yii::$app->session->setFlash('error', 'Silakan pilih minimal 1 file gambar.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Updates an existing Gallery model.
     * 
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $invitation = $model->invitation;
        $oldFilename = $model->filename;

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            // If new image uploaded
            if ($model->imageFile) {
                // Delete old image
                $this->deleteImage($model->invitation_id, $oldFilename);
                
                // Upload new image
                if ($this->uploadImage($model)) {
                    Yii::$app->session->setFlash('success', 'Foto berhasil diperbarui.');
                    return $this->redirect(['index', 'invitation_id' => $model->invitation_id]);
                }
            } else {
                // Only update caption/sort_order
                $model->filename = $oldFilename;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Caption berhasil diperbarui.');
                    return $this->redirect(['index', 'invitation_id' => $model->invitation_id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Deletes an existing Gallery model.
     * 
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $invitation_id = $model->invitation_id;
        
        // Delete will trigger afterDelete() which removes files
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Foto berhasil dihapus.');
        return $this->redirect(['index', 'invitation_id' => $invitation_id]);
    }

    /**
     * AJAX action to update sort order
     * 
     * @return array
     */
    public function actionSort()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $order = Yii::$app->request->post('order', []);
        
        foreach ($order as $index => $id) {
            Gallery::updateAll(['sort_order' => $index + 1], ['id' => $id]);
        }
        
        return ['success' => true];
    }

    /**
     * Upload image and create thumbnail
     * 
     * @param Gallery $model
     * @return bool
     */
    protected function uploadImage($model)
    {
        if (!$model->imageFile) {
            return false;
        }

        // Create upload directory
        $uploadPath = Yii::getAlias('@webroot/uploads/invitations/' . $model->invitation_id . '/gallery');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $filename = uniqid() . '.' . $model->imageFile->extension;
        $filePath = $uploadPath . '/' . $filename;

        // Save original image
        if ($model->imageFile->saveAs($filePath)) {
            // Create thumbnail (300x300) using GD library
            $pathInfo = pathinfo($filename);
            $thumbName = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            $thumbPath = $uploadPath . '/' . $thumbName;
            
            try {
                $this->createThumbnail($filePath, $thumbPath, 300, 300);
            } catch (\Exception $e) {
                Yii::error('Failed to create thumbnail: ' . $e->getMessage());
            }

            // Save filename to model
            $model->filename = $filename;
            return $model->save(false);
        }

        return false;
    }

    /**
     * Create thumbnail using GD library
     * 
     * @param string $source
     * @param string $destination
     * @param int $width
     * @param int $height
     */
    protected function createThumbnail($source, $destination, $width, $height)
    {
        list($origWidth, $origHeight, $type) = getimagesize($source);
        
        // Create source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($source);
                break;
            default:
                throw new \Exception('Unsupported image type');
        }
        
        // Calculate dimensions
        $ratio = min($width / $origWidth, $height / $origHeight);
        $newWidth = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);
        
        // Create thumbnail
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }
        
        imagecopyresampled($thumb, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        
        // Save thumbnail
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $destination, 80);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $destination, 8);
                break;
        }
        
        imagedestroy($srcImage);
        imagedestroy($thumb);
    }

    /**
     * Delete image files
     * 
     * @param int $invitation_id
     * @param string $filename
     */
    protected function deleteImage($invitation_id, $filename)
    {
        $imagePath = Yii::getAlias('@webroot/uploads/invitations/' . $invitation_id . '/gallery/' . $filename);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete thumbnail
        $pathInfo = pathinfo($filename);
        $thumbName = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        $thumbPath = Yii::getAlias('@webroot/uploads/invitations/' . $invitation_id . '/gallery/' . $thumbName);
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
    }

    /**
     * Finds the Gallery model based on its primary key value.
     * 
     * @param int $id
     * @return Gallery
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Gallery::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Foto yang Anda cari tidak ditemukan.');
    }

    /**
     * Finds the Invitation model based on its primary key value.
     * 
     * @param int $id
     * @return Invitation
     * @throws NotFoundHttpException
     */
    protected function findInvitation($id)
    {
        if (($model = Invitation::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Undangan tidak ditemukan.');
    }
}
