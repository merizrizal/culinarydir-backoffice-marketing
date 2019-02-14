<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\Business;
use core\models\BusinessProductCategory;
use core\models\search\BusinessProductCategorySearch;
use backoffice\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BusinessProductCategoryController implements the CRUD actions for BusinessProductCategory model.
 */
class BusinessProductCategoryController extends BaseController
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
                        
                    ],
                ],
            ]);
    }

    /**
     * Lists all BusinessProductCategory models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $searchModel = new BusinessProductCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['business_id' => $id])->andWhere(['OR', ['product_category.type' => 'Menu'], ['product_category.type' => 'Specific-Menu']]);
        
        $modelBusiness = Business::find()
            ->andWhere(['id' => $id])
            ->asArray()->one();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelBusiness' => $modelBusiness
        ]);
    }

    /**
     * Displays a single BusinessProductCategory model.
     * @param string $id
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
     * Creates a new BusinessProductCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id, $save = null)
    {
        $render = 'create';

        $model = new BusinessProductCategory();

        if ($model->load(($post = Yii::$app->request->post()))) {
            
            if (!empty($save)) {
                
                $model->business_id = $id;
                $model->unique_id = $id . '-' . $post['BusinessProductCategory']['product_category_id'];
                $model->product_category_id = $post['BusinessProductCategory']['product_category_id'];

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
            'modelBusiness' => $modelBusiness
        ]);
    }
    
    public function actionUpdateOrder($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessProductCategories' => function ($query) {
                
                    $query->orderBy(['order' => SORT_ASC]);
                },
                'businessProductCategories.productCategory' => function ($query) {
                    
                    $query->andOnCondition(['OR', ['product_category.type' => 'Menu'], ['product_category.type' => 'Specific-Menu']]);
                },
            ])
            ->andWhere(['business.id' => $id])
            ->one();
            
        $modelBusinessProductCategory = new BusinessProductCategory();
        $dataBusinessProductCategory = [];
        
        if (!empty(($post = Yii::$app->request->post()))) {
            
            if (!empty($save)) {
                
                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                foreach ($model->businessProductCategories as $dataProductCategories) {
                    
                    if (!empty($dataProductCategories->productCategory)) {
                        
                        $dataProductCategories->order = $post['order'][$dataProductCategories['id']];
                        $dataProductCategories->is_active = !empty($post['is_active'][$dataProductCategories['id']]) ? true : false;
                        
                        if (!($flag = $dataProductCategories->save())) {
                            
                            break;
                        } else {
                            
                            $modelProductCategory = [];
                            $modelProductCategory['productCategory'] = $dataProductCategories->productCategory->toArray();
                            array_push($dataBusinessProductCategory, array_merge($dataProductCategories->toArray(), $modelProductCategory));
                        }
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
        
        if (empty($dataBusinessProductCategory)) {
            
            foreach ($model['businessProductCategories'] as $productCategory) {
                
                if (!empty($productCategory['productCategory'])) {
                    
                    array_push($dataBusinessProductCategory, $productCategory);
                }
            }
        }
        
        return $this->render('update_order', [
            'model' => $model,
            'modelBusinessProductCategory' => $modelBusinessProductCategory,
            'dataBusinessProductCategory' => $dataBusinessProductCategory
        ]);
    }

    /**
     * Finds the BusinessProductCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return BusinessProductCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BusinessProductCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
