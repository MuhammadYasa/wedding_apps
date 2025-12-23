<?php

namespace app\controllers;

use Yii;
use app\models\Gallery;
use app\models\Invitation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * GalleryController handles public gallery display
 */
class GalleryController extends Controller
{
    /**
     * Display gallery photos for public view
     * @param int $id Invitation ID (optional)
     * @return string
     */
    public function actionIndex($id = null)
    {
        $invitation = null;
        $query = Gallery::find()->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]);

        if ($id !== null) {
            $invitation = $this->findInvitation($id);
            $query->where(['invitation_id' => $id]);
        }

        $galleries = $query->all();

        return $this->render('index', [
            'galleries' => $galleries,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Display single photo with details
     * @param int $id Gallery ID
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Invitation model based on its primary key value.
     * @param int $id
     * @return Invitation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findInvitation($id)
    {
        if (($model = Invitation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Undangan tidak ditemukan.');
    }

    /**
     * Finds the Gallery model based on its primary key value.
     * @param int $id
     * @return Gallery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gallery::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Foto tidak ditemukan.');
    }
}
