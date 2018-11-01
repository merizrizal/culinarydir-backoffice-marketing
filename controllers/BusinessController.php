<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\Business;
use core\models\search\BusinessSearch;
use core\models\BusinessCategory;
use core\models\BusinessProductCategory;
use core\models\BusinessHour;
use core\models\BusinessFacility;
use core\models\BusinessImage;
use sycomponent\Tools;
use sycomponent\AjaxRequest;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * BusinessController
 */
class BusinessController extends \backoffice\controllers\BaseController
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

    public function actionMember() {

        $searchModel = new BusinessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//         $dataProvider->query
//             ->andWhere(['user_in_charge' => Yii::$app->user->getIdentity()->id]);

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('member', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionViewMember($id) {

        $model = Business::find()
                ->joinWith([
                    'membershipType',
                    'businessLocation.city',
                    'businessLocation.district',
                    'businessLocation.village',
                    'userInCharge',
                    'businessCategories' => function($query) {
                        
                        $query->andOnCondition(['business_category.is_active' => true]);
                    },
                    'businessCategories.category',
                    'businessProductCategories' => function($query) {
                        
                        $query->andOnCondition(['business_product_category.is_active' => true]);
                    },
                    'businessHours' => function($query) {
                        
                        $query->andOnCondition(['business_hour.is_open' => true])
                            ->orderBy(['business_hour.day' => SORT_ASC]);
                    },
                    'businessProductCategories.productCategory',
                    'businessFacilities' => function($query) {
                        
                        $query->andOnCondition(['business_facility.is_active' => true]);
                    },
                    'businessFacilities.facility',
                    'businessDetail',
                    'businessImages',
                    'applicationBusiness',
                    'applicationBusiness.logStatusApprovals' => function($query) {
                        
                        $query->andOnCondition(['log_status_approval.is_actual' => true]);
                    },
                    'applicationBusiness.logStatusApprovals.statusApproval',
                ])
                ->andWhere(['business.id' => $id])
                ->asArray()->one();

        return $this->render('view_member', [
            'model' => $model,
        ]);
    }

    public function actionUpdateBusinessInfo($id, $save = null)
    {
        $model = $this->findModel($id);
        $modelBusinessLocation = $model->businessLocation;

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post) && $modelBusinessLocation->load($post)) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(ActiveForm::validate($model), ActiveForm::validate($modelBusinessLocation));
            } else {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                if (($flag = $model->save())) {

                    $modelBusinessLocation->setCoordinate();

                    $flag = $modelBusinessLocation->save();
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

        return $this->render('update_business_info', [
            'model' => $model,
            'modelBusinessLocation' => $modelBusinessLocation,
        ]);
    }

    public function actionUpdateMarketingInfo($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessCategories' => function($query) {
                    
                    $query->andOnCondition(['business_category.is_active' => true]);
                },
                'businessCategories.category',
                'businessProductCategories' => function($query) {
                    
                    $query->andOnCondition(['business_product_category.is_active' => true]);
                },
                'businessProductCategories.productCategory',
                'businessFacilities' => function($query) {
                    
                    $query->andOnCondition(['business_facility.is_active' => true]);
                },
                'businessFacilities.facility',
                'businessHours' => function($query) {
                    
                    $query->orderBy(['business_hour.day' => SORT_ASC]);
                },
                'businessDetail',
            ])
            ->andWhere(['business.id' => $id])
            ->one();

        $modelBusinessCategory = new BusinessCategory();
        $dataBusinessCategory = [];

        $modelBusinessProductCategory = new BusinessProductCategory();
        $dataBusinessProductCategoryParent = [];
        $dataBusinessProductCategoryChild = [];

        $modelBusinessFacility = new BusinessFacility();
        $dataBusinessFacility = [];

        $modelBusinessHour = new BusinessHour();
        $dataBusinessHour = [];

        $modelBusinessDetail = $model->businessDetail;

        if ($modelBusinessDetail->load(($post = Yii::$app->request->post()))) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                if (($flag = $model->save())) {

                    if (!empty($post['BusinessCategory']['category_id'])) {

                        foreach ($post['BusinessCategory']['category_id'] as $value) {

                            $newModelBusinessCategory = BusinessCategory::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelBusinessCategory)) {

                                $newModelBusinessCategory->is_active = true;
                            } else {

                                $newModelBusinessCategory = new BusinessCategory();

                                $newModelBusinessCategory->unique_id = $model->id . '-' . $value;
                                $newModelBusinessCategory->business_id = $model->id;
                                $newModelBusinessCategory->category_id = $value;
                                $newModelBusinessCategory->is_active = true;
                            }

                            if (!($flag = $newModelBusinessCategory->save())) {
                                break;
                            } else {
                                array_push($dataBusinessCategory, $newModelBusinessCategory->toArray());
                            }
                        }
                    } else {

                        foreach ($model->businessCategories as $valueBusinessCategory) {

                            $valueBusinessCategory->is_active = false;

                            if (!($flag = $valueBusinessCategory->save())) {
                                break;
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessCategory']['category_id'])) {

                        foreach ($model->businessCategories as $valueBusinessCategory) {

                            $exist = false;

                            foreach ($post['BusinessCategory']['category_id'] as $value) {

                                if ($valueBusinessCategory['category_id'] == $value) {

                                    $exist = true;
                                    break;
                                }
                            }

                            if (!$exist) {

                                $valueBusinessCategory->is_active = false;

                                if (!($flag = $valueBusinessCategory->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessProductCategory']['product_category_id']['parent'])) {

                        foreach ($post['BusinessProductCategory']['product_category_id']['parent'] as $value) {

                            $newModelBusinessProductCategory = BusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelBusinessProductCategory)) {

                                $newModelBusinessProductCategory->is_active = true;
                            } else {

                                $newModelBusinessProductCategory = new BusinessProductCategory();

                                $newModelBusinessProductCategory->unique_id = $model->id . '-' . $value;
                                $newModelBusinessProductCategory->business_id = $model->id;
                                $newModelBusinessProductCategory->product_category_id = $value;
                                $newModelBusinessProductCategory->is_active = true;
                            }

                            if(!($flag = $newModelBusinessProductCategory->save())) {
                                break;
                            } else {
                                array_push($dataBusinessProductCategoryParent, $newModelBusinessProductCategory->toArray());
                            }
                        }
                    } else {

                        foreach ($model->businessProductCategories as $valueBusinessProductCategory) {

                            if (empty($valueBusinessProductCategory->productCategory->parent_id)) {

                                $valueBusinessProductCategory->is_active = false;

                                if (!($flag = $valueBusinessProductCategory->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($post['BusinessProductCategory']['product_category_id']['child'] as $value) {

                            $newModelBusinessProductCategory = BusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelBusinessProductCategory)) {

                                $newModelBusinessProductCategory->is_active = true;
                            } else {

                                $newModelBusinessProductCategory = new BusinessProductCategory();

                                $newModelBusinessProductCategory->unique_id = $model->id . '-' . $value;
                                $newModelBusinessProductCategory->business_id = $model->id;
                                $newModelBusinessProductCategory->product_category_id = $value;
                                $newModelBusinessProductCategory->is_active = true;
                            }

                            if(!($flag = $newModelBusinessProductCategory->save())) {
                                break;
                            } else {
                                array_push($dataBusinessProductCategoryChild, $newModelBusinessProductCategory->toArray());
                            }
                        }
                    } else {

                        foreach ($model->businessProductCategories as $valueBusinessProductCategory) {

                            if (!empty($valueBusinessProductCategory->productCategory->parent_id)) {

                                $valueBusinessProductCategory->is_active = false;

                                if (!($flag = $valueBusinessProductCategory->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessProductCategory']['product_category_id']) && !empty($post['BusinessProductCategory']['product_category_id']['parent']) && !empty($post['BusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($model->businessProductCategories as $valueBusinessProductCategory) {

                            $exist = false;

                            foreach ($post['BusinessProductCategory']['product_category_id'] as $data) {

                                foreach ($data as $value) {

                                    if ($valueBusinessProductCategory['product_category_id'] == $value) {

                                        $exist = true;
                                        break 2;
                                    }
                                }
                            }

                            if (!$exist) {

                                $valueBusinessProductCategory->is_active = false;

                                if (!($flag = $valueBusinessProductCategory->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessFacility']['facility_id'])) {

                        foreach ($post['BusinessFacility']['facility_id'] as $value) {

                            $newModelBusinessFacility = BusinessFacility::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelBusinessFacility)) {

                                $newModelBusinessFacility->is_active = true;
                            } else {

                                $newModelBusinessFacility = new BusinessFacility();

                                $newModelBusinessFacility->unique_id = $model->id . '-' . $value;
                                $newModelBusinessFacility->business_id = $model->id;
                                $newModelBusinessFacility->facility_id = $value;
                                $newModelBusinessFacility->is_active = true;
                            }

                            if (!($flag = $newModelBusinessFacility->save())) {
                                break;
                            } else {
                                array_push($dataBusinessFacility, $newModelBusinessFacility->toArray());
                            }
                        }
                    } else {

                        foreach ($model->businessFacilities as $valueBusinessFacility) {

                            $valueBusinessFacility->is_active = false;

                            if (!($flag = $valueBusinessFacility->save())) {
                                break;
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessFacility']['facility_id'])) {

                        foreach ($model->businessFacilities as $valueBusinessFacility) {

                            $exist = false;

                            foreach ($post['BusinessFacility']['facility_id'] as $value) {

                                if ($valueBusinessFacility['facility_id'] == $value) {

                                    $exist = true;
                                    break;
                                }
                            }

                            if (!$exist) {

                                $valueBusinessFacility->is_active = false;

                                if (!($flag = $valueBusinessFacility->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    $loopDays = ['1', '2', '3', '4', '5', '6', '7'];

                    foreach ($loopDays as $day) {

                        $dayName = 'day' . $day;

                        if (!empty($post['BusinessHour'][$dayName])) {

                            $newModelBusinessHourDay = BusinessHour::findOne(['unique_id' => $model->id . '-' . $day]);

                            if (empty($newModelBusinessHourDay)) {

                                $newModelBusinessHourDay = new BusinessHour();
                                $newModelBusinessHourDay->business_id = $model->id;
                                $newModelBusinessHourDay->unique_id = $model->id . '-' . $day;
                                $newModelBusinessHourDay->day = $day;
                            }

                            $newModelBusinessHourDay->is_open = !empty($post['BusinessHour'][$dayName]['is_open']) ? true : false;
                            $newModelBusinessHourDay->open_at = !empty($post['BusinessHour'][$dayName]['open_at']) ? $post['BusinessHour'][$dayName]['open_at'] : null;
                            $newModelBusinessHourDay->close_at = !empty($post['BusinessHour'][$dayName]['close_at']) ? $post['BusinessHour'][$dayName]['close_at'] : null;

                            if (!$flag = $newModelBusinessHourDay->save()) {
                                break;
                            } else {
                                array_push($dataBusinessHour, $newModelBusinessHourDay->toArray());
                            }
                        }
                    }
                }

                if ($flag) {
                    $modelBusinessDetail->price_min = !empty($modelBusinessDetail->price_min) ? $modelBusinessDetail->price_min : 0;
                    $modelBusinessDetail->price_max = !empty($modelBusinessDetail->price_max) ? $modelBusinessDetail->price_max : 0;

                    $flag = $modelBusinessDetail->save();
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

        $dataBusinessCategory = empty($dataBusinessCategory) ? $model['businessCategories'] : $dataBusinessCategory;

        $dataBusinessProductCategory = $model['businessProductCategories'];

        $businessProductCategoryParent = [];
        $businessProductCategoryChild = [];

        foreach ($dataBusinessProductCategory as $valueBusinessProductCategory) {

            if ($valueBusinessProductCategory['productCategory']['is_parent']) {
                
                $businessProductCategoryParent[] = $valueBusinessProductCategory;
            } else {
                
                $businessProductCategoryChild[] = $valueBusinessProductCategory;
            }
        }

        $dataBusinessProductCategoryParent = empty($dataBusinessProductCategoryParent) ? $businessProductCategoryParent : $dataBusinessProductCategoryParent;
        $dataBusinessProductCategoryChild = empty($dataBusinessProductCategoryChild) ? $businessProductCategoryChild : $dataBusinessProductCategoryChild;

        $dataBusinessHour = empty($dataBusinessHour) ? $model['businessHours'] : $dataBusinessHour;

        $dataBusinessFacility = empty($dataBusinessFacility) ? $model['businessFacilities'] : $dataBusinessFacility;

        return $this->render('update_marketing_info', [
            'model' => $model,
            'modelBusinessCategory' => $modelBusinessCategory,
            'dataBusinessCategory' => $dataBusinessCategory,
            'modelBusinessProductCategory' => $modelBusinessProductCategory,
            'dataBusinessProductCategoryParent' => $dataBusinessProductCategoryParent,
            'dataBusinessProductCategoryChild' => $dataBusinessProductCategoryChild,
            'modelBusinessFacility' => $modelBusinessFacility,
            'dataBusinessFacility' => $dataBusinessFacility,
            'modelBusinessHour' => $modelBusinessHour,
            'dataBusinessHour' => $dataBusinessHour,
            'modelBusinessDetail' => $modelBusinessDetail,
        ]);
    }

    public function actionUpdateGalleryPhoto($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessImages' => function($query) {
                 
                    $query->orderBy(['order' => SORT_ASC]);
                }
            ])
            ->andWhere(['business.id' => $id])
            ->one();

        $modelBusinessImage = new BusinessImage();
        $dataBusinessImage = [];
        $newDataBusinessImage = [];

        $deletedBusinessImageId = [];

        if (!empty(($post = Yii::$app->request->post()))) {

            if (!empty($save)) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;
                
                $order = count($model->businessImages);
                    
                if (!empty($post['BusinessImageDelete'])) {
                    
                    if (($flag = BusinessImage::deleteAll(['id' => $post['BusinessImageDelete']]))) {
                        
                        $deletedBusinessImageId = $post['BusinessImageDelete'];
                    }
                    
                    if ($flag) {
                        
                        $order = 0;
                        
                        foreach ($model->businessImages as $dataModelBusinessImage) {
                            
                            $deleted = false;
                            
                            foreach ($deletedBusinessImageId as $businessImageId) {
                                
                                if ($dataModelBusinessImage->id == $businessImageId) {
                                    
                                    $deleted = true;
                                    break;
                                }
                            }
                            
                            if (!$deleted) {
                                
                                $order++;
                                
                                $dataModelBusinessImage->order = $order;
                                
                                if (!($flag = $dataModelBusinessImage->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }
                
                if ($flag) {
                    
                    $newModelBusinessImage = new BusinessImage(['business_id' => $model->id]);
                    
                    if ($newModelBusinessImage->load($post)) {
                        
                        $images = Tools::uploadFiles('/img/registry_business/', $newModelBusinessImage, 'image', 'business_id', '', true);
                        
                        foreach ($images as $image) {
                            
                            $order++;
                            
                            $newModelBusinessImage = new BusinessImage();
                            $newModelBusinessImage->business_id = $model->id;
                            $newModelBusinessImage->image = $image;
                            $newModelBusinessImage->type = 'Gallery';
                            $newModelBusinessImage->is_primary = false;
                            $newModelBusinessImage->category = 'Ambience';
                            $newModelBusinessImage->order = $order;
                            
                            if (!($flag = $newModelBusinessImage->save())) {
                                break;
                            } else {
                                array_push($newDataBusinessImage, $newModelBusinessImage->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    foreach ($model->businessImages as $existModelBusinessImage) {

                        $existModelBusinessImage->type = !empty($post['profile'][$existModelBusinessImage->id]) ? 'Profile' : 'Gallery';
                        $existModelBusinessImage->is_primary = !empty($post['thumbnail']) && $post['thumbnail'] == $existModelBusinessImage->id ? true : false;
                        $existModelBusinessImage->category = !empty($post['category'][$existModelBusinessImage->id]) ? $post['category'][$existModelBusinessImage->id] : $modelBusinessImage->category;

                        if (!($flag = $modelBusinessImage->save())) {
                            break;
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

        foreach ($model['businessImages'] as $valueBusinessImage) {

            $deleted = false;

            foreach ($deletedBusinessImageId as $businessImageId) {

                if ($businessImageId == $valueBusinessImage['id']) {
                    $deleted = true;
                    break;
                }
            }

            if (!$deleted) {
                array_push($dataBusinessImage, $valueBusinessImage);
            }
        }
        
        if (!empty($newDataBusinessImage)) {
            
            $dataBusinessImage = ArrayHelper::merge($dataBusinessImage, $newDataBusinessImage);
        }

        return $this->render('update_gallery_photo', [
            'model' => $model,
            'modelBusinessImage' => $modelBusinessImage,
            'dataBusinessImage' => $dataBusinessImage,
        ]);
    }
    
    public function actionUp($id)
    {
        $modelBusinessImage = BusinessImage::findOne($id);
        
        $modelBusinessImageTemp = BusinessImage::find()
            ->andWhere(['business_id' => $modelBusinessImage->business_id])
            ->andWhere(['order' => $modelBusinessImage->order - 1])
            ->one();
        
        if ($modelBusinessImage->order > 1) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $modelBusinessImageTemp->order = $modelBusinessImage->order;
            
            if (($flag = $modelBusinessImageTemp->save())) {
                
                $modelBusinessImage->order -= 1;
                
                $flag = $modelBusinessImage->save();
            }
            
            if ($flag) {
                
                $transaction->commit();
            } else {
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                
                $transaction->rollBack();
            }
        }
        
        return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business/update-gallery-photo', 'id' => $modelBusinessImage->business_id]));
    }
    
    public function actionDown($id)
    {
        $modelBusinessImage = BusinessImage::findOne($id);
        
        $modelBusinessImageTemp = BusinessImage::find()
            ->andWhere(['business_id' => $modelBusinessImage->business_id])
            ->andWhere(['order' => $modelBusinessImage->order + 1])
            ->one();
        
        if ($modelBusinessImageTemp !== null) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $modelBusinessImageTemp->order = $modelBusinessImage->order;
            
            if (($flag = $modelBusinessImageTemp->save())) {
                    
                $modelBusinessImage->order += 1;
                
                $flag = $modelBusinessImage->save();
            }
            
            if ($flag) {
                
                $transaction->commit();
            } else {
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                
                $transaction->rollBack();
            }
        }
        
        return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business/update-gallery-photo', 'id' => $modelBusinessImage->business_id]));
    }

    protected function findModel($id)
    {
        if (($model = Business::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}