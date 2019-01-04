<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\BusinessPayment;
use core\models\search\BusinessPaymentSearch;
use core\models\Business;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * BusinessPaymentController implements the CRUD actions for BusinessPayment model.
 */
class BusinessPaymentController extends \backoffice\controllers\BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]);
    }

    /**
     * Lists all BusinessPayment models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $searchModel = new BusinessPaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['business_id' => $id]);

        $modelBusiness = Business::find()
            ->andWhere(['id' => $id])
            ->asArray()->one();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelBusiness' => $modelBusiness,
        ]);
    }

    /**
     * Displays a single BusinessPayment model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BusinessPayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id, $save = null)
    {
        $render = 'create';

        $model = new BusinessPayment();
        
        if ($model->load(Yii::$app->request->post())) {

            if (!empty($save)) {
                
                $model->business_id = $id;
                
                if ($model->save()) {

                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Create Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Create data process is success. Data has been saved'));

                    $render = 'view';
                } else {

                    $model->setIsNewRecord(true);

                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Create Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Create data process is fail. Data fail to save'));
                }
            }
        }
        
        $modelBusiness = Business::find()
            ->andWhere(['id' => $id])
            ->asArray()->one();
        
        return $this->render($render, [
            'model' => $model,
            'modelBusiness' => $modelBusiness,
        ]);
    }

    /**
     * Updates an existing BusinessPayment model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if (!empty($save)) {

                if ($model->save()) {

                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is success. Data has been saved'));
                } else {

                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelBusiness' => $model->business->toArray(),
        ]);
    }

    /**
     * Deletes an existing BusinessPayment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (($model = $this->findModel($id)) !== false) {

            $flag = false;
            $error = '';

            try {
                $flag = $model->delete();
            } catch (yii\db\Exception $exc) {
                $error = Yii::$app->params['errMysql'][$exc->errorInfo[1]];
            }
        }

        if ($flag) {

            Yii::$app->session->setFlash('status', 'success');
            Yii::$app->session->setFlash('message1', Yii::t('app', 'Delete Is Success'));
            Yii::$app->session->setFlash('message2', Yii::t('app', 'Delete process is success. Data has been deleted'));
        } else {

            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', Yii::t('app', 'Delete Is Fail'));
            Yii::$app->session->setFlash('message2', Yii::t('app', 'Delete process is fail. Data fail to delete' . $error));
        }

        $return = [];

        $return['url'] = Yii::$app->urlManager->createUrl([$this->module->id . '/business-payment/index', 'id' => $model->business_id]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    /**
     * Finds the BusinessPayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BusinessPayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BusinessPayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
