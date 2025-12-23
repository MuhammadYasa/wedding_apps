<?php

namespace app\controllers;

use Yii;
use app\models\Guest;
use app\models\Invitation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AdminGuestController implements the CRUD actions for Guest model.
 */
class AdminGuestController extends Controller
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
     * Lists all Guest models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $guests = Guest::find()
            ->with('invitation')
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        // Get all invitations for dropdown if needed
        $invitations = Invitation::find()->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('index', [
            'guests' => $guests,
            'invitations' => $invitations,
        ]);
    }

    /**
     * Displays a single Guest model.
     *
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Guest model.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Guest();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Tamu berhasil ditambahkan.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Guest model.
     *
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Data tamu berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Guest model.
     *
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Tamu berhasil dihapus.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Guest model based on its primary key value.
     *
     * @param int $id ID
     * @return Guest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Guest::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
