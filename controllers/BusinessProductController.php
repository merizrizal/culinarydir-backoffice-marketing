<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\BusinessProduct;
use core\models\search\BusinessProductSearch;
use core\models\Business;
use sycomponent\AjaxRequest;
use sycomponent\Tools;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BusinessProductController implements the CRUD actions for BusinessProduct model.
 */
class BusinessProductController extends \backoffice\controllers\BaseController
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
     * Lists all BusinessProduct models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $searchModel = new BusinessProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['business_product.business_id' => $id]);

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
     * Displays a single BusinessProduct model.
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
     * Creates a new BusinessProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id, $save = null)
    {
        $render = 'create';

        $model = new BusinessProduct();

        if ($model->load(Yii::$app->request->post())) {

            if (!empty($save)) {
                
                $last = BusinessProduct::find()
                    ->andWhere(['business_id' => $id])
                    ->orderBy(['order' => SORT_DESC])
                    ->asArray()->one();

                $model->business_id = $id;
                $model->image = Tools::uploadFile('/img/business_product/', $model, 'image', 'id', $model->business_id);
                $model->order = $last['order'] + 1;
                
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
     * Updates an existing BusinessProduct model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if (!empty($save)) {
                
                $image = Tools::uploadFile('/img/business_product/', $model, 'image', 'id', $model->business_id);

                $model->image = !empty($image) ? $image : $model->oldAttributes['image'];
                
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
     * Deletes an existing BusinessProduct model.
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

        $return['url'] = Yii::$app->urlManager->createUrl([$this->module->id . '/business-product/index', 'id' => $model->business_id]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionUpdateOrder($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessProducts' => function ($query) {
            
                    $query->orderBy(['order' => SORT_ASC]);
                },
            ])
            ->andWhere(['business.id' => $id])
            ->one();
            
        $dataBusinessProduct = [];
        
        if (!empty(($post = Yii::$app->request->post()))) {
            
            if (!empty($save)) {
                
                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;
                
                foreach ($model->businessProducts as $dataProduct) {
                    
                    $dataProduct->order = $post['order'][$dataProduct['id']];
                    $dataProduct->not_active = !empty($post['not_active'][$dataProduct['id']]) ? true : false; 
                    
                    if (!($flag = $dataProduct->save())) {
                        
                        break;
                    } else {
                        
                        array_push($dataBusinessProduct, $dataProduct->toArray());
                    }
                }
                
                if ($flag) {
                    
                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is success. Data has been saved'));
                    
                    $transaction->commit();
                } else {
                    
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                    
                    $transaction->rollBack();
                }
            }
        }
        
        if (empty($dataBusinessProduct)) {
            
            foreach ($model['businessProducts'] as $businessProduct) {
                
                array_push($dataBusinessProduct, $businessProduct);
            }
        }
        
        return $this->render('update_order', [
            'model' => $model,
            'dataBusinessProduct' => $dataBusinessProduct
        ]);
    }
    
    public function actionAddMultipleMenu($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessProducts'
            ])
            ->andWhere(['business.id' => $id])
            ->one();
            
        $modelBusinessProduct = new BusinessProduct();
        $dataBusinessProduct = [];
        
        if (!empty(($post = Yii::$app->request->post()))) {
            
            if (!empty($save)) {
                
                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;
                
                if (!empty($post['BusinessProduct'])) {
                    
                    $menuCount = count($model->businessProducts);
                    
                    foreach ($post['BusinessProduct'] as $businessProduct) {
                        
                        $newModelBusinessProduct = new BusinessProduct();
                        
                        $newModelBusinessProduct->business_id = $id;
                        $newModelBusinessProduct->name = $businessProduct['name'];
                        $newModelBusinessProduct->description = $businessProduct['description'];
                        $newModelBusinessProduct->price = $businessProduct['price'];
                        $newModelBusinessProduct->business_product_category_id = $businessProduct['business_product_category_id'];
                        $newModelBusinessProduct->not_active = false;
                        $newModelBusinessProduct->order = $menuCount + 1;
                        
                        if (!($flag = $newModelBusinessProduct->save())) {
                            
                            break;
                        } else {
                            
                            array_push($dataBusinessProduct, $newModelBusinessProduct);
                            
                            $menuCount++;
                        }
                    }
                }
                
                if ($flag) {
                    
                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is success. Data has been saved'));
                    
                    $transaction->commit();
                    
                    return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business-product/index', 'id' => $id]));
                } else {
                    
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                    
                    $transaction->rollBack();
                }
            }
        }
        
        return $this->render('add_multiple_menu', [
            'model' => $model,
            'modelBusinessProduct' => $modelBusinessProduct,
            'dataBusinessProduct' => $dataBusinessProduct,
        ]);
    }
    
    public function actionUpdateSelectedMenu($id, $selected, $save = null)
    {
        if (empty($selected)) {
            
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', Yii::t('app', 'No Data Selected'));
            Yii::$app->session->setFlash('message2', Yii::t('app', 'Please select the data that you want to update first'));
            
            return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business-product/index', 'id' => $id]));
        }
        
        $dataSelected = explode(',', trim($selected, ','));
        
        $model = Business::find()
            ->joinWith([
                'businessProducts' => function ($query) use ($dataSelected) {
                    
                    $query->andOnCondition(['business_product.id' => $dataSelected]);
                }
            ])
            ->andWhere(['business.id' => $id])
            ->one();
            
        $modelBusinessProduct = new BusinessProduct();
        $productCategoryId = '';
        
        if (!empty(($post = Yii::$app->request->post()))) {
            
            if (!empty($save)) {
                
                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;
                
                if (!empty($post['BusinessProduct']['business_product_category_id'])) {
                    
                    $productCategoryId = $post['BusinessProduct']['business_product_category_id'];
                        
                    foreach ($model['businessProducts'] as $businessProduct) {

                        $businessProduct->business_product_category_id = $post['BusinessProduct']['business_product_category_id'];
                        
                        if (!($flag = $businessProduct->save())) {
                            
                            break;
                        }
                    }
                }
                
                if ($flag) {
                    
                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is success. Data has been saved'));
                    
                    $transaction->commit();
                    
                    return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business-product/index', 'id' => $id]));
                } else {
                    
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                    
                    $transaction->rollBack();
                }
            }
        }
        
        return $this->render('update_selected_menu', [
            'model' => $model,
            'modelBusinessProduct' => $modelBusinessProduct,
            'productCategoryId' => $productCategoryId,
            'selected' => $selected
        ]);
    }

    /**
     * Finds the BusinessProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BusinessProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BusinessProduct::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
