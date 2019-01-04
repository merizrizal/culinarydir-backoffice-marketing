<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\RegistryBusiness;
use core\models\RegistryBusinessDelivery;
use core\models\search\RegistryBusinessDeliverySearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * RegistryBusinessDeliveryController implements the CRUD actions for RegistryBusinessDelivery model.
 */
class RegistryBusinessDeliveryController extends \backoffice\controllers\BaseController
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
     * Lists all RegistryBusinessDelivery models.
     * @return mixed
     */
    public function actionIndex($id, $statusApproval)
    {
        $searchModel = new RegistryBusinessDeliverySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['registry_business_id' => $id]);
        
        $modelRegistryBusiness = RegistryBusiness::find()
            ->andWhere(['id' => $id])
            ->asArray()->one();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statusApproval' => $statusApproval,
            'modelRegistryBusiness' => $modelRegistryBusiness,
        ]);
    }

    /**
     * Displays a single RegistryBusinessDelivery model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $statusApproval)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'statusApproval' => $statusApproval,
        ]);
    }

    /**
     * Creates a new RegistryBusinessDelivery model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id, $save = null, $statusApproval)
    {
        $render = 'create';

        $model = new RegistryBusinessDelivery();

        if ($model->load(Yii::$app->request->post())) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                
                $model->registry_business_id = $id;
                $model->unique_id = $id . '-' . Yii::$app->request->post('RegistryBusinessDelivery')['delivery_method_id'];

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
        
        $modelRegistryBusiness = RegistryBusiness::find()
            ->andWhere(['id' => $id])
            ->asArray()->one();

        return $this->render($render, [
            'model' => $model,
            'modelRegistryBusiness' => $modelRegistryBusiness,
            'statusApproval' => $statusApproval
        ]);
    }

    /**
     * Updates an existing RegistryBusinessDelivery model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null, $statusApproval)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

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
            'modelRegistryBusiness' => $model->registryBusiness->toArray(),
            'statusApproval' => $statusApproval
        ]);
    }

    /**
     * Deletes an existing RegistryBusinessDelivery model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $statusApproval)
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

        $return['url'] = Yii::$app->urlManager->createUrl([$this->module->id . '/registry-business-delivery/index', 'id' => $model->registry_business_id, 'statusApproval' => $statusApproval]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    /**
     * Finds the RegistryBusinessDelivery model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RegistryBusinessDelivery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RegistryBusinessDelivery::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
