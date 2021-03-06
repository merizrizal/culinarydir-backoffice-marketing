<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\RegistryBusiness;
use core\models\search\RegistryBusinessSearch;
use core\models\ApplicationBusiness;
use core\models\LogStatusApproval;
use core\models\LogStatusApprovalAction;
use core\models\Person;
use core\models\RegistryBusinessCategory;
use core\models\RegistryBusinessProductCategory;
use core\models\RegistryBusinessHour;
use core\models\RegistryBusinessHourAdditional;
use core\models\RegistryBusinessFacility;
use core\models\RegistryBusinessImage;
use core\models\RegistryBusinessContactPerson;
use core\models\RegistryBusinessPayment;
use core\models\RegistryBusinessDelivery;
use core\models\StatusApproval;
use core\models\StatusApprovalAction;
use sycomponent\AjaxRequest;
use sycomponent\Tools;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * RegistryBusinessController implements the CRUD actions for RegistryBusiness model.
 */
class RegistryBusinessController extends \backoffice\controllers\BaseController
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

    public function actionCreate($save = null)
    {
        $model = new RegistryBusiness();
        $model->setScenario(RegistryBusiness::SCENARIO_CREATE);

        $modelRegistryBusinessCategory = new RegistryBusinessCategory();
        $dataRegistryBusinessCategory = [];

        $modelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
        $dataRegistryBusinessProductCategoryParent = [];
        $dataRegistryBusinessProductCategoryChild = [];

        $modelRegistryBusinessFacility = new RegistryBusinessFacility();
        $dataRegistryBusinessFacility = [];

        $modelRegistryBusinessHour = new RegistryBusinessHour();
        $dataRegistryBusinessHour = [];
        
        $modelRegistryBusinessHourAdditional = new RegistryBusinessHourAdditional();
        $dataRegistryBusinessHourAdditional = [];

        $modelRegistryBusinessImage = new RegistryBusinessImage();
        $modelRegistryBusinessImage->setScenario(RegistryBusinessImage::SCENARIO_CREATE);
        
        $modelPerson = new Person();        
        $modelRegistryBusinessContactPerson = new RegistryBusinessContactPerson();
        $dataRegistryBusinessContactPerson = [];
        
        $modelRegistryBusinessPayment = new RegistryBusinessPayment();
        $dataRegistryBusinessPayment = [];
        
        $modelRegistryBusinessDelivery = new RegistryBusinessDelivery();
        $dataRegistryBusinessDelivery = [];

        if ($model->load(($post = Yii::$app->request->post()))) {
            
            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                $modelApplicationBusiness = new ApplicationBusiness();
                $modelApplicationBusiness->user_in_charge = Yii::$app->user->identity->id;
                $modelApplicationBusiness->counter = 1;

                if (($flag = $modelApplicationBusiness->save())) {

                    $modelLogStatusApproval = new LogStatusApproval();
                    $modelLogStatusApproval->application_business_id = $modelApplicationBusiness->id;
                    $modelLogStatusApproval->status_approval_id = StatusApproval::find()->andWhere(['group' => 0])->asArray()->one()['id'];
                    $modelLogStatusApproval->is_actual = true;
                    $modelLogStatusApproval->application_business_counter = $modelApplicationBusiness->counter;

                    $flag = $modelLogStatusApproval->save();
                }

                if ($flag) {

                    $model->application_business_id = $modelApplicationBusiness->id;
                    $model->user_in_charge = $modelApplicationBusiness->user_in_charge;
                    $model->application_business_counter = $modelApplicationBusiness->counter;
                    $model->setCoordinate();
                    $model->price_min = !empty($model->price_min) ? $model->price_min : 0;
                    $model->price_max = !empty($model->price_max) ? $model->price_max : 0;

                    if (($flag = $model->save())) {

                        if (!empty($post['RegistryBusinessCategory']['category_id'])) {

                            foreach ($post['RegistryBusinessCategory']['category_id'] as $categoryId) {

                                $newModelRegistryBusinessCategory = new RegistryBusinessCategory();
                                $newModelRegistryBusinessCategory->unique_id = $model->id . '-' . $categoryId;
                                $newModelRegistryBusinessCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessCategory->category_id = $categoryId;
                                $newModelRegistryBusinessCategory->is_active = true;

                                if (!($flag = $newModelRegistryBusinessCategory->save())) {
                                    
                                    break;
                                } else {
                                    
                                    array_push($dataRegistryBusinessCategory, $newModelRegistryBusinessCategory->toArray());
                                }
                            }
                        }
                    }

                    if ($flag) {

                        if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['parent'])) {

                            foreach ($post['RegistryBusinessProductCategory']['product_category_id']['parent'] as $productCategoryId) {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $productCategoryId;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $productCategoryId;
                                $newModelRegistryBusinessProductCategory->is_active = true;

                                if (!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                    
                                    break;
                                } else {
                                    
                                    array_push($dataRegistryBusinessProductCategoryParent, $newModelRegistryBusinessProductCategory->toArray());
                                }
                            }
                        }
                    }

                    if ($flag) {

                        if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['child'])) {

                            foreach ($post['RegistryBusinessProductCategory']['product_category_id']['child'] as $productCategoryId) {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $productCategoryId;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $productCategoryId;
                                $newModelRegistryBusinessProductCategory->is_active = true;

                                if (!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                    
                                    break;
                                } else {
                                    
                                    array_push($dataRegistryBusinessProductCategoryChild, $newModelRegistryBusinessProductCategory->toArray());
                                }
                            }
                        }
                    }
                    
                    if ($flag) {
                        
                        if (!empty($post['RegistryBusinessFacility']['facility_id'])) {
                            
                            foreach ($post['RegistryBusinessFacility']['facility_id'] as $facilityId) {
                                
                                $newModelRegistryBusinessFacility = new RegistryBusinessFacility();
                                $newModelRegistryBusinessFacility->unique_id = $model->id . '-' . $facilityId;
                                $newModelRegistryBusinessFacility->registry_business_id = $model->id;
                                $newModelRegistryBusinessFacility->facility_id = $facilityId;
                                $newModelRegistryBusinessFacility->is_active = true;
                                
                                if (!($flag = $newModelRegistryBusinessFacility->save())) {
                                    
                                    break;
                                } else {
                                    
                                    array_push($dataRegistryBusinessFacility, $newModelRegistryBusinessFacility->toArray());
                                }
                            }
                        }
                    }

                    if ($flag) {

                        $loopDays = ['1', '2', '3', '4', '5', '6', '7'];

                        foreach ($loopDays as $day) {

                            $dayName = 'day' . $day;

                            if (!empty($post['RegistryBusinessHour'][$dayName])) {

                                $newModelRegistryBusinessHourDay = new RegistryBusinessHour();
                                $newModelRegistryBusinessHourDay->registry_business_id = $model->id;
                                $newModelRegistryBusinessHourDay->unique_id = $model->id . '-' . $day;
                                $newModelRegistryBusinessHourDay->day = $day;
                                $newModelRegistryBusinessHourDay->is_open = !empty($post['RegistryBusinessHour'][$dayName]['is_open']) ? true : false;
                                $newModelRegistryBusinessHourDay->open_at = !empty($post['RegistryBusinessHour'][$dayName]['open_at']) ? $post['RegistryBusinessHour'][$dayName]['open_at'] : null;
                                $newModelRegistryBusinessHourDay->close_at = !empty($post['RegistryBusinessHour'][$dayName]['close_at']) ? $post['RegistryBusinessHour'][$dayName]['close_at'] : null;
                                
                                if (!($flag = $newModelRegistryBusinessHourDay->save())) {
                                    
                                    break;
                                } else {
                                    
                                    array_push($dataRegistryBusinessHour, $newModelRegistryBusinessHourDay->toArray());
                                }
                            }
                            
                            if (!empty($post['RegistryBusinessHourAdditional'][$dayName])) {
                                
                                foreach ($post['RegistryBusinessHourAdditional'][$dayName] as $i => $dataAdditional) {
                                    
                                    if (!empty($dataAdditional['open_at']) || !empty($dataAdditional['close_at'])) {
                                            
                                        $newModelRegistryBusinessHourAdditional = new RegistryBusinessHourAdditional();
                                        $newModelRegistryBusinessHourAdditional->unique_id = $newModelRegistryBusinessHourDay->id . '-' . $day . '-' . $i;
                                        $newModelRegistryBusinessHourAdditional->registry_business_hour_id = $newModelRegistryBusinessHourDay->id;
                                        $newModelRegistryBusinessHourAdditional->day = $day;
                                        $newModelRegistryBusinessHourAdditional->is_open = $newModelRegistryBusinessHourDay->is_open;
                                        $newModelRegistryBusinessHourAdditional->open_at = !empty($dataAdditional['open_at']) ? $dataAdditional['open_at'] : null;
                                        $newModelRegistryBusinessHourAdditional->close_at = !empty($dataAdditional['close_at']) ? $dataAdditional['close_at'] : null;
                                    
                                        if (!($flag = $newModelRegistryBusinessHourAdditional->save())) {
                                            
                                            break;
                                        } else {
                                            
                                            if (empty($dataRegistryBusinessHourAdditional[$dayName])) {
                                                
                                                $dataRegistryBusinessHourAdditional[$dayName] = [];
                                            }
                                            
                                            array_push($dataRegistryBusinessHourAdditional[$dayName], $newModelRegistryBusinessHourAdditional->toArray());
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {

                        $newModelRegistryBusinessImage = new RegistryBusinessImage();

                        if ($newModelRegistryBusinessImage->load($post)) {

                            $images = Tools::uploadFiles('/img/registry_business/', $newModelRegistryBusinessImage, 'image', 'registry_business_id', '', true);

                            foreach ($images as $i => $image) {

                                $i++;
                                
                                $newModelRegistryBusinessImage = new RegistryBusinessImage();
                                $newModelRegistryBusinessImage->registry_business_id = $model->id;
                                $newModelRegistryBusinessImage->image = $image;
                                $newModelRegistryBusinessImage->type = 'Gallery';
                                $newModelRegistryBusinessImage->category = 'Ambience';
                                $newModelRegistryBusinessImage->order = $i;

                                if (!($flag = $newModelRegistryBusinessImage->save())) {
                                    
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {

                        if (!empty($post['Person']) && !empty($post['RegistryBusinessContactPerson'])) {

                            foreach ($post['Person'] as $i => $dataPerson) {
                                
                                $newModelPerson = new Person();
                                $newModelPerson->first_name = $dataPerson['first_name'];
                                $newModelPerson->last_name = !empty($dataPerson['last_name']) ? $dataPerson['last_name'] : null;
                                $newModelPerson->phone = !empty($dataPerson['phone']) ? $dataPerson['phone'] : null;
                                $newModelPerson->email = !empty($dataPerson['email']) ? $dataPerson['email'] : null;

                                if (!($flag = $newModelPerson->save())) {
                                    
                                    break;
                                } else {
                                    
                                    $newModelRegistryBusinessContactPerson = new RegistryBusinessContactPerson();
                                    $newModelRegistryBusinessContactPerson->registry_business_id = $model->id;
                                    $newModelRegistryBusinessContactPerson->person_id = $newModelPerson->id;
                                    $newModelRegistryBusinessContactPerson->is_primary_contact = !empty($post['RegistryBusinessContactPerson'][$i]['is_primary_contact']) ? true : false;
                                    $newModelRegistryBusinessContactPerson->note = !empty($post['RegistryBusinessContactPerson'][$i]['note']) ? $post['RegistryBusinessContactPerson'][$i]['note'] : null;
                                    $newModelRegistryBusinessContactPerson->position = $post['RegistryBusinessContactPerson'][$i]['position'];
                                
                                    if (!($flag = $newModelRegistryBusinessContactPerson->save())) {
                                        
                                        break;
                                    } else {
                                        
                                        array_push($dataRegistryBusinessContactPerson, ArrayHelper::merge($newModelRegistryBusinessContactPerson->toArray(), $newModelPerson->toArray()));
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($flag) {
                        
                        if (!empty($post['RegistryBusinessPayment'])) {
                            
                            foreach ($post['RegistryBusinessPayment'] as $dataPaymentMethod) {
                                
                                $newModelRegistryBusinessPayment = new RegistryBusinessPayment();
                                $newModelRegistryBusinessPayment->registry_business_id = $model->id;
                                $newModelRegistryBusinessPayment->payment_method_id = $dataPaymentMethod['payment_method_id'];
                                $newModelRegistryBusinessPayment->is_active = true;
                                $newModelRegistryBusinessPayment->note = !empty($dataPaymentMethod['note']) ? $dataPaymentMethod['note'] : null;
                                $newModelRegistryBusinessPayment->description = !empty($dataPaymentMethod['description']) ? $dataPaymentMethod['description'] : null;
                                
                                if (!($flag = $newModelRegistryBusinessPayment->save())) {
                                    
                                    break;
                                } else {
                                    
                                    array_push($dataRegistryBusinessPayment, $newModelRegistryBusinessPayment->toArray());
                                }
                            }
                        }
                    }
                    
                    if ($flag) {
                        
                        if (!empty($post['RegistryBusinessDelivery'])) {
                            
                            foreach ($post['RegistryBusinessDelivery'] as $dataDeliveryMethod) {
                                
                                $newModelRegistryBusinessDelivery = new RegistryBusinessDelivery();
                                $newModelRegistryBusinessDelivery->registry_business_id = $model->id;
                                $newModelRegistryBusinessDelivery->delivery_method_id = $dataDeliveryMethod['delivery_method_id'];
                                $newModelRegistryBusinessDelivery->is_active = true;
                                $newModelRegistryBusinessDelivery->note = !empty($dataDeliveryMethod['note']) ? $dataDeliveryMethod['note'] : null;
                                $newModelRegistryBusinessDelivery->description = !empty($dataDeliveryMethod['description']) ? $dataDeliveryMethod['description'] : null;
                                
                                if (!($flag = $newModelRegistryBusinessDelivery->save())) {
                                    
                                    break;
                                } else {
                                    
                                    array_push($dataRegistryBusinessDelivery, $newModelRegistryBusinessDelivery->toArray());
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Create Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Create data process is success. Data has been saved'));

                    $transaction->commit();

                    return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/registry-business/view-pndg', 'id' => $model->id]));
                } else {

                    $model->setIsNewRecord(true);

                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Create Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Create data process is fail. Data fail to save'));

                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelRegistryBusinessCategory' => $modelRegistryBusinessCategory,
            'dataRegistryBusinessCategory' => $dataRegistryBusinessCategory,
            'modelRegistryBusinessProductCategory' => $modelRegistryBusinessProductCategory,
            'dataRegistryBusinessProductCategoryParent' => $dataRegistryBusinessProductCategoryParent,
            'dataRegistryBusinessProductCategoryChild' => $dataRegistryBusinessProductCategoryChild,
            'modelRegistryBusinessFacility' => $modelRegistryBusinessFacility,
            'dataRegistryBusinessFacility' => $dataRegistryBusinessFacility,
            'modelRegistryBusinessHour' => $modelRegistryBusinessHour,
            'dataRegistryBusinessHour' => $dataRegistryBusinessHour,
            'modelRegistryBusinessHourAdditional' => $modelRegistryBusinessHourAdditional,
            'dataRegistryBusinessHourAdditional' => $dataRegistryBusinessHourAdditional,
            'modelRegistryBusinessImage' => $modelRegistryBusinessImage,
            'modelPerson' => $modelPerson,
            'modelRegistryBusinessContactPerson' => $modelRegistryBusinessContactPerson,
            'dataRegistryBusinessContactPerson' => $dataRegistryBusinessContactPerson,
            'modelRegistryBusinessPayment' => $modelRegistryBusinessPayment,
            'dataRegistryBusinessPayment' => $dataRegistryBusinessPayment,
            'modelRegistryBusinessDelivery' => $modelRegistryBusinessDelivery,
            'dataRegistryBusinessDelivery' => $dataRegistryBusinessDelivery,
        ]);
    }

    public function actionIndexPndg()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 80px">
                            {view}{delete}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 104px">
                            {view}{delete}
                        </div>
                    </div>
                </div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-search-plus"></i>', ['view-pndg', 'id' => $model->id], [
                        'id' => 'view',
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'View',
                    ]);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-trash-alt"></i>', ['delete', 'id' => $model->id, 'statusApproval' => 'pndg'], [
                        'id' => 'delete',
                        'class' => 'btn btn-danger',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-not-ajax' => 1,
                        'title' => 'Delete',
                        'model-id' => $model->id,
                        'model-name' => $model->name,
                    ]);
                },
            ]
        ];

        return $this->index('PNDG', Yii::t('app', 'Pending Application'), $actionColumn);
    }

    public function actionIndexIcorct()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 40px">
                            {resubmit}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 52px">
                            {resubmit}
                        </div>
                    </div>
                </div>',
            'buttons' => [
                'resubmit' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-check"></i>', ['view-icorct', 'id' => $model->id], [
                        'id' => 'resubmit',
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'Resubmit',
                    ]);
                },
            ]
        ];

        return $this->index('ICORCT', Yii::t('app', 'Incorrect Application'), $actionColumn);
    }

    public function actionIndexRjct()
    {
        $actionColumn = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '
                <div class="btn-container hide">
                    <div class="visible-lg visible-md">
                        <div class="btn-group btn-group-md" role="group" style="width: 40px">
                            {view}
                        </div>
                    </div>
                    <div class="visible-sm visible-xs">
                        <div class="btn-group btn-group-lg" role="group" style="width: 52px">
                            {view}
                        </div>
                    </div>
                </div>',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-search-plus"></i>', ['view-rjct', 'id' => $model->id], [
                        'id' => 'view',
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'View',
                    ]);
                },
            ]
        ];

        return $this->index('RJCT', Yii::t('app', 'Reject Application'), $actionColumn);
    }

    public function actionViewPndg($id)
    {
        $actionButton = [
            'update' => function ($model, $dropup = '') {
                return '
                    <div class="btn-group ' . $dropup . '">

                        ' . Html::button('<i class="fa fa-pencil-alt"></i> Edit', [
                                'type' => 'button',
                                'class' => 'btn btn-primary dropdown-toggle',
                                'data-toggle' => 'dropdown',
                                'aria-haspopup' => 'true',
                                'aria-expanded' => 'false',
                            ]) . '

                        <ul class="dropdown-menu">
                            <li>' . Html::a(Yii::t('app', 'Business Information'), ['update-business-info', 'id' => $model['id'], 'statusApproval' => 'pndg']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Marketing Information'), ['update-marketing-info', 'id' => $model['id'], 'statusApproval' => 'pndg']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Gallery Photo'), ['update-gallery-photo', 'id' => $model['id'], 'statusApproval' => 'pndg']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Operational Hours'), ['update-business-hour', 'id' => $model['id'], 'statusApproval' => 'pndg']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Contact Person'), ['update-contact-person', 'id' => $model['id'], 'statusApproval' => 'pndg']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Payment Methods'), ['registry-business-payment/index', 'id' => $model['id'], 'statusApproval' => 'pndg']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Delivery Methods'), ['registry-business-delivery/index', 'id' => $model['id'], 'statusApproval' => 'pndg']) . '</li>
                        </ul>
                    </div>
                ';
            },
            'delete' => function ($model) {
                return Html::a('<i class="fa fa-trash-alt"></i> Delete', ['delete', 'id' => $model['id'], 'statusApproval' => 'pndg'], [
                    'id' => 'delete',
                    'class' => 'btn btn-danger',
                    'style' => 'color:white',
                    'data-not-ajax' => 1,
                    'model-id' => $model['id'],
                    'model-name' => $model['name'],
                ]);
            },
        ];

        return $this->view($id, 'PNDG', $actionButton);
    }

    public function actionViewIcorct($id)
    {
        $actionButton = [
            'update' => function ($model, $dropup = '') {
                return '
                    <div class="btn-group ' . $dropup . '">

                        ' . Html::button('<i class="fa fa-pencil-alt"></i> Edit', [
                                'type' => 'button',
                                'class' => 'btn btn-primary dropdown-toggle',
                                'data-toggle' => 'dropdown',
                                'aria-haspopup' => 'true',
                                'aria-expanded' => 'false',
                            ]) . '

                        <ul class="dropdown-menu">
                            <li>' . Html::a(Yii::t('app', 'Business Information'), ['update-business-info', 'id' => $model['id'], 'statusApproval' => 'icorct']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Marketing Information'), ['update-marketing-info', 'id' => $model['id'], 'statusApproval' => 'icorct']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Gallery Photo'), ['update-gallery-photo', 'id' => $model['id'], 'statusApproval' => 'icorct']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Operational Hours'), ['update-business-hour', 'id' => $model['id'], 'statusApproval' => 'icorct']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Contact Person'), ['update-contact-person', 'id' => $model['id'], 'statusApproval' => 'icorct']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Payment Methods'), ['registry-business-payment/index', 'id' => $model['id'], 'statusApproval' => 'icorct']) . '</li>
                            <li>' . Html::a(Yii::t('app', 'Delivery Methods'), ['registry-business-delivery/index', 'id' => $model['id'], 'statusApproval' => 'icorct']) . '</li>
                        </ul>
                    </div>
                ';
            },
            'resubmit' => function ($model) {
                return Html::a('<i class="fa fa-check"></i> Resubmit', ['resubmit', 'id' => $model['id'], 'appBId' => $model['applicationBusiness']['id'], 'appBCounter' => $model['applicationBusiness']['counter'], 'statusApproval' => 'ICORCT'], [
                    'id' => 'resubmit',
                    'class' => 'btn btn-success',
                ]);
            },
        ];

        return $this->view($id, 'ICORCT', $actionButton);
    }

    public function actionViewRjct($id)
    {
        return $this->view($id, 'RJCT');
    }

    public function actionUpdateBusinessInfo($id, $save = null, $statusApproval)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                $model->setCoordinate();

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

        return $this->render('update_business_info', [
            'model' => $model,
            'statusApproval' => $statusApproval,
        ]);
    }

    public function actionUpdateMarketingInfo($id, $save = null, $statusApproval)
    {
        $model = RegistryBusiness::find()
            ->joinWith([
                'registryBusinessCategories' => function ($query) {
                    
                    $query->andOnCondition(['registry_business_category.is_active' => true]);
                },
                'registryBusinessCategories.category',
                'registryBusinessProductCategories' => function ($query) {
                    
                    $query->andOnCondition(['registry_business_product_category.is_active' => true]);
                },
                'registryBusinessProductCategories.productCategory',
                'registryBusinessFacilities' => function ($query) {
                    
                    $query->andOnCondition(['registry_business_facility.is_active' => true]);
                },
                'registryBusinessFacilities.facility'
            ])
            ->andWhere(['registry_business.id' => $id])
            ->one();

        $modelRegistryBusinessCategory = new RegistryBusinessCategory();
        $dataRegistryBusinessCategory = [];

        $modelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
        $dataRegistryBusinessProductCategoryParent = [];
        $dataRegistryBusinessProductCategoryChild = [];

        $modelRegistryBusinessFacility = new RegistryBusinessFacility();
        $dataRegistryBusinessFacility = [];
        
        if ($model->load(($post = Yii::$app->request->post()))) {

            if (!empty($save)) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                $model->price_min = !empty($model->price_min) ? $model->price_min : 0;
                $model->price_max = !empty($model->price_max) ? $model->price_max : 0;

                if (($flag = $model->save())) {

                    if (!empty($post['RegistryBusinessCategory']['category_id'])) {

                        foreach ($post['RegistryBusinessCategory']['category_id'] as $categoryId) {

                            $newModelRegistryBusinessCategory = RegistryBusinessCategory::findOne(['unique_id' => $model->id . '-' . $categoryId]);

                            if (!empty($newModelRegistryBusinessCategory)) {

                                $newModelRegistryBusinessCategory->is_active = true;
                            } else {

                                $newModelRegistryBusinessCategory = new RegistryBusinessCategory();
                                $newModelRegistryBusinessCategory->unique_id = $model->id . '-' . $categoryId;
                                $newModelRegistryBusinessCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessCategory->category_id = $categoryId;
                                $newModelRegistryBusinessCategory->is_active = true;
                            }

                            if (!($flag = $newModelRegistryBusinessCategory->save())) {
                                
                                break;
                            } else {
                                
                                array_push($dataRegistryBusinessCategory, $newModelRegistryBusinessCategory->toArray());
                            }
                        }
                        
                        if ($flag) {
                            
                            foreach ($model->registryBusinessCategories as $existModelRegistryBusinessCategory) {
                                
                                $exist = false;
                                
                                foreach ($post['RegistryBusinessCategory']['category_id'] as $categoryId) {
                                    
                                    if ($existModelRegistryBusinessCategory['category_id'] == $categoryId) {
                                        
                                        $exist = true;
                                        break;
                                    }
                                }
                                
                                if (!$exist) {
                                    
                                    $existModelRegistryBusinessCategory->is_active = false;
                                    
                                    if (!($flag = $existModelRegistryBusinessCategory->save())) {
                                        
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['parent'])) {

                        foreach ($post['RegistryBusinessProductCategory']['product_category_id']['parent'] as $productCategoryId) {

                            $newModelRegistryBusinessProductCategory = RegistryBusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $productCategoryId]);

                            if (!empty($newModelRegistryBusinessProductCategory)) {

                                $newModelRegistryBusinessProductCategory->is_active = true;
                            } else {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $productCategoryId;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $productCategoryId;
                                $newModelRegistryBusinessProductCategory->is_active = true;
                            }

                            if (!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                
                                break;
                            } else {
                                
                                array_push($dataRegistryBusinessProductCategoryParent, $newModelRegistryBusinessProductCategory->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($post['RegistryBusinessProductCategory']['product_category_id']['child'] as $productCategoryId) {

                            $newModelRegistryBusinessProductCategory = RegistryBusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $productCategoryId]);

                            if (!empty($newModelRegistryBusinessProductCategory)) {

                                $newModelRegistryBusinessProductCategory->is_active = true;
                            } else {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $productCategoryId;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $productCategoryId;
                                $newModelRegistryBusinessProductCategory->is_active = true;
                            }

                            if (!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                
                                break;
                            } else {
                                
                                array_push($dataRegistryBusinessProductCategoryChild, $newModelRegistryBusinessProductCategory->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['parent']) && !empty($post['RegistryBusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($model->registryBusinessProductCategories as $existModelRegistryBusinessProductCategory) {

                            $exist = false;

                            foreach ($post['RegistryBusinessProductCategory']['product_category_id'] as $dataProductCategory) {

                                foreach ($dataProductCategory as $productCategoryId) {

                                    if ($existModelRegistryBusinessProductCategory['product_category_id'] == $productCategoryId) {

                                        $exist = true;
                                        break 2;
                                    }
                                }
                            }

                            if (!$exist) {

                                $existModelRegistryBusinessProductCategory->is_active = false;

                                if (!($flag = $existModelRegistryBusinessProductCategory->save())) {
                                    
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessFacility']['facility_id'])) {

                        foreach ($post['RegistryBusinessFacility']['facility_id'] as $facilityId) {

                            $newModelRegistryBusinessFacility = RegistryBusinessFacility::findOne(['unique_id' => $model->id . '-' . $facilityId]);

                            if (!empty($newModelRegistryBusinessFacility)) {

                                $newModelRegistryBusinessFacility->is_active = true;
                            } else {

                                $newModelRegistryBusinessFacility = new RegistryBusinessFacility();
                                $newModelRegistryBusinessFacility->unique_id = $model->id . '-' . $facilityId;
                                $newModelRegistryBusinessFacility->registry_business_id = $model->id;
                                $newModelRegistryBusinessFacility->facility_id = $facilityId;
                                $newModelRegistryBusinessFacility->is_active = true;
                            }

                            if (!($flag = $newModelRegistryBusinessFacility->save())) {
                                
                                break;
                            } else {
                                
                                array_push($dataRegistryBusinessFacility, $newModelRegistryBusinessFacility->toArray());
                            }
                        }
                        
                        if ($flag) {
                            
                            foreach ($model->registryBusinessFacilities as $existModelRegistryBusinessFacility) {
                                
                                $exist = false;
                                
                                foreach ($post['RegistryBusinessFacility']['facility_id'] as $facilityId) {
                                    
                                    if ($existModelRegistryBusinessFacility['facility_id'] == $facilityId) {
                                        
                                        $exist = true;
                                        break;
                                    }
                                }
                                
                                if (!$exist) {
                                    
                                    $existModelRegistryBusinessFacility->is_active = false;
                                    
                                    if (!($flag = $existModelRegistryBusinessFacility->save())) {
                                        
                                        break;
                                    }
                                }
                            }
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

        $registryBusinessProductCategoryParent = [];
        $registryBusinessProductCategoryChild = [];

        foreach ($model['registryBusinessProductCategories'] as $existModelRegistryBusinessProductCategory) {

            if ($existModelRegistryBusinessProductCategory['productCategory']['is_parent']) {
                
                $registryBusinessProductCategoryParent[] = $existModelRegistryBusinessProductCategory;
            } else {
                
                $registryBusinessProductCategoryChild[] = $existModelRegistryBusinessProductCategory;
            }
        }

        $dataRegistryBusinessCategory = empty($dataRegistryBusinessCategory) ? $model['registryBusinessCategories'] : $dataRegistryBusinessCategory;
        $dataRegistryBusinessProductCategoryParent = empty($dataRegistryBusinessProductCategoryParent) ? $registryBusinessProductCategoryParent : $dataRegistryBusinessProductCategoryParent;
        $dataRegistryBusinessProductCategoryChild = empty($dataRegistryBusinessProductCategoryChild) ? $registryBusinessProductCategoryChild : $dataRegistryBusinessProductCategoryChild;
        $dataRegistryBusinessFacility = empty($dataRegistryBusinessFacility) ? $model['registryBusinessFacilities'] : $dataRegistryBusinessFacility;

        return $this->render('update_marketing_info', [
            'model' => $model,
            'modelRegistryBusinessCategory' => $modelRegistryBusinessCategory,
            'dataRegistryBusinessCategory' => $dataRegistryBusinessCategory,
            'modelRegistryBusinessProductCategory' => $modelRegistryBusinessProductCategory,
            'dataRegistryBusinessProductCategoryParent' => $dataRegistryBusinessProductCategoryParent,
            'dataRegistryBusinessProductCategoryChild' => $dataRegistryBusinessProductCategoryChild,
            'modelRegistryBusinessFacility' => $modelRegistryBusinessFacility,
            'dataRegistryBusinessFacility' => $dataRegistryBusinessFacility,
            'statusApproval' => $statusApproval,
        ]);
    }

    public function actionUpdateGalleryPhoto($id, $save = null, $statusApproval)
    {
        $model = RegistryBusiness::find()
            ->joinWith([
                'registryBusinessImages' => function ($query) {
                
                    $query->orderBy(['order' => SORT_ASC]);
                }
            ])
            ->andWhere(['registry_business.id' => $id])
            ->one();
            
        $modelRegistryBusinessImage = new RegistryBusinessImage();
        $dataRegistryBusinessImage = [];
        $newDataRegistryBusinessImage = [];

        $deletedRegistryBusinessImageId = [];

        if (!empty(($post = Yii::$app->request->post()))) {

            if (!empty($save)) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;
                
                $order = count($model->registryBusinessImages);
                
                if (!empty($post['RegistryBusinessImageDelete'])) {
                    
                    if (($flag = RegistryBusinessImage::deleteAll(['id' => $post['RegistryBusinessImageDelete']]))) {
                        
                        if ($order == count($post['RegistryBusinessImageDelete'])) {
                            
                            $flag = false;
                        } else {
                            
                            $deletedRegistryBusinessImageId = $post['RegistryBusinessImageDelete'];
                        }
                    }
                    
                    if ($flag) {
                        
                        $order = 0;
                        
                        foreach ($model->registryBusinessImages as $dataModelRegistryBusinessImage) {
                            
                            $deleted = false;
                            
                            foreach ($deletedRegistryBusinessImageId as $registryBusinessImageId) {
                                
                                if ($dataModelRegistryBusinessImage->id == $registryBusinessImageId) {
                                    
                                    $deleted = true;
                                    break;
                                }
                            }
                            
                            if (!$deleted) {
                            
                                $order++;
                                
                                $dataModelRegistryBusinessImage->order = $order;
                                
                                if (!($flag = $dataModelRegistryBusinessImage->save())) {
                                    
                                    break;
                                }
                            }
                        }
                    }
                }
                
                if ($flag) {
                
                    $newModelRegistryBusinessImage = new RegistryBusinessImage();
                    
                    if ($newModelRegistryBusinessImage->load($post)) {
                        
                        $images = Tools::uploadFiles('/img/registry_business/', $newModelRegistryBusinessImage, 'image', 'registry_business_id', '', true);
                        
                        foreach ($images as $image) {
                            
                            $order++;
                            
                            $newModelRegistryBusinessImage = new RegistryBusinessImage();
                            $newModelRegistryBusinessImage->registry_business_id = $model->id;
                            $newModelRegistryBusinessImage->image = $image;
                            $newModelRegistryBusinessImage->type = 'Gallery';
                            $newModelRegistryBusinessImage->category = 'Ambience';
                            $newModelRegistryBusinessImage->order = $order;
                            
                            if (!($flag = $newModelRegistryBusinessImage->save())) {
                                
                                break;
                            } else {
                                
                                array_push($newDataRegistryBusinessImage, $newModelRegistryBusinessImage->toArray());
                            }
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
        
        foreach ($model['registryBusinessImages'] as $valueRegistryBusinessImage) {
            
            $deleted = false;
            
            foreach ($deletedRegistryBusinessImageId as $registryBusinessImageId) {
                
                if ($registryBusinessImageId == $valueRegistryBusinessImage['id']) {
                    
                    $deleted = true;
                    break;
                }
            }
            
            if (!$deleted) {
                
                array_push($dataRegistryBusinessImage, $valueRegistryBusinessImage->toArray());
            }
        }
        
        if (!empty($newDataRegistryBusinessImage)) {
            
            $dataRegistryBusinessImage = ArrayHelper::merge($dataRegistryBusinessImage, $newDataRegistryBusinessImage);
        }

        return $this->render('update_gallery_photo', [
            'model' => $model,
            'modelRegistryBusinessImage' => $modelRegistryBusinessImage,
            'dataRegistryBusinessImage' => $dataRegistryBusinessImage,
            'statusApproval' => $statusApproval,
        ]);
    }
    
    public function actionUpdateContactPerson($id, $save = null, $statusApproval)
    {
        $model = RegistryBusiness::find()
            ->joinWith([
                'registryBusinessContactPeople' => function ($query) {
                    
                    $query->orderBy(['registry_business_contact_person.id' => SORT_ASC]);
                },
                'registryBusinessContactPeople.person',
            ])
            ->andWhere(['registry_business.id' => $id])
            ->one();
            
        $modelPerson = new Person();
        $modelRegistryBusinessContactPerson = new RegistryBusinessContactPerson();
        $dataRegistryBusinessContactPerson = [];
        
        $isEmpty = false;
        
        if (!empty($post = Yii::$app->request->post())) {
            
            if (!empty($save)) {
                
                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;
                
                $isEmpty = (empty($post['Person']) && empty($post['RegistryBusinessContactPerson']));
                
                if (!empty($post['RegistryBusinessContactPersonDeleted'])) {
                    
                    if (($flag = RegistryBusinessContactPerson::deleteAll(['person_id' => $post['RegistryBusinessContactPersonDeleted']]))) {
                        
                        $flag = Person::deleteAll(['id' => $post['RegistryBusinessContactPersonDeleted']]);
                    }
                }
                
                if (!empty($post['Person']) && !empty($post['RegistryBusinessContactPerson'])) {
                    
                    foreach ($post['Person'] as $i => $person) {
                            
                        if (!empty($model['registryBusinessContactPeople'][$i])) {
                            
                            $newModelPerson = Person::findOne(['id' => $model['registryBusinessContactPeople'][$i]['person_id']]);
                        } else {
                            
                            $newModelPerson = new Person();
                        }
                        
                        $newModelPerson->first_name = $person['first_name'];
                        $newModelPerson->last_name = $person['last_name'];
                        $newModelPerson->phone = $person['phone'];
                        $newModelPerson->email = $person['email'];
                        
                        if (!($flag = $newModelPerson->save())) {
                            
                            break;
                        } else {
                            
                            $newModelRegistryBusinessContactPerson = RegistryBusinessContactPerson::findOne(['person_id' => $newModelPerson->id]);
                            
                            if (empty($newModelRegistryBusinessContactPerson)) {
                                
                                $newModelRegistryBusinessContactPerson = new RegistryBusinessContactPerson();
                                $newModelRegistryBusinessContactPerson->registry_business_id = $model->id;
                                $newModelRegistryBusinessContactPerson->person_id = $newModelPerson->id;
                            }
                            
                            $newModelRegistryBusinessContactPerson->position = $post['RegistryBusinessContactPerson'][$i]['position'];
                            $newModelRegistryBusinessContactPerson->is_primary_contact = !empty($post['RegistryBusinessContactPerson'][$i]['is_primary_contact']) ? true : false;
                            $newModelRegistryBusinessContactPerson->note = $post['RegistryBusinessContactPerson'][$i]['note'];
                            
                            if (!($flag = $newModelRegistryBusinessContactPerson->save())) {
                                
                                break;
                            } else {
                                
                                array_push($dataRegistryBusinessContactPerson, ArrayHelper::merge($newModelRegistryBusinessContactPerson->toArray(), $newModelPerson->toArray()));
                            }
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
        
        if (!$isEmpty) {
            
            if (empty($dataRegistryBusinessContactPerson)) {
                
                foreach ($model->registryBusinessContactPeople as $dataContactPerson) {
                    
                    $dataContactPerson = ArrayHelper::merge($dataContactPerson->toArray(), $dataContactPerson->person->toArray());
                    
                    array_push($dataRegistryBusinessContactPerson, $dataContactPerson);
                }
            }
        }
            
        return $this->render('update_contact_person', [
            'model' => $model,
            'modelPerson' => $modelPerson,
            'modelRegistryBusinessContactPerson' => $modelRegistryBusinessContactPerson,
            'dataRegistryBusinessContactPerson' => $dataRegistryBusinessContactPerson,
            'statusApproval' => $statusApproval,
        ]);
    }
    
    public function actionUpdateBusinessHour($id, $save = null, $statusApproval)
    {
        $model = RegistryBusiness::find()
            ->joinWith([
                'registryBusinessHours' => function ($query) {
                
                    $query->orderBy(['registry_business_hour.day' => SORT_ASC]);
                },
                'registryBusinessHours.registryBusinessHourAdditionals',
            ])
            ->andWhere(['registry_business.id' => $id])
            ->one();
            
        $modelRegistryBusinessHour = new RegistryBusinessHour();
        $dataRegistryBusinessHour = [];
        
        $modelRegistryBusinessHourAdditional = new RegistryBusinessHourAdditional();
        $dataRegistryBusinessHourAdditional = [];
        
        if (!empty($post = Yii::$app->request->post())) {
            
            if (!empty($save)) {
                
                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;
                
                $loopDays = ['1', '2', '3', '4', '5', '6', '7'];
                
                foreach ($loopDays as $day) {
                    
                    $dayName = 'day' . $day;
                    
                    if (!empty($post['RegistryBusinessHour'][$dayName])) {
                        
                        $newModelRegistryBusinessHourDay = RegistryBusinessHour::findOne(['unique_id' => $model->id . '-' . $day]);
                        
                        if (empty($newModelRegistryBusinessHourDay)) {
                            
                            $newModelRegistryBusinessHourDay = new RegistryBusinessHour();
                            $newModelRegistryBusinessHourDay->registry_business_id = $model->id;
                            $newModelRegistryBusinessHourDay->unique_id = $model->id . '-' . $day;
                            $newModelRegistryBusinessHourDay->day = $day;
                        }
                        
                        $newModelRegistryBusinessHourDay->is_open = !empty($post['RegistryBusinessHour'][$dayName]['is_open']) ? true : false;
                        $newModelRegistryBusinessHourDay->open_at = !empty($post['RegistryBusinessHour'][$dayName]['open_at']) ? $post['RegistryBusinessHour'][$dayName]['open_at'] : null;
                        $newModelRegistryBusinessHourDay->close_at = !empty($post['RegistryBusinessHour'][$dayName]['close_at']) ? $post['RegistryBusinessHour'][$dayName]['close_at'] : null;
                        
                        if (!($flag = $newModelRegistryBusinessHourDay->save())) {
                            
                            break;
                        } else {
                            
                            array_push($dataRegistryBusinessHour, $newModelRegistryBusinessHourDay->toArray());
                        }
                    }
                    
                    if ($flag && !empty($post['RegistryBusinessHourAdditionalDeleted'][$dayName])) {
                        
                        $flag = RegistryBusinessHourAdditional::deleteAll(['id' => $post['RegistryBusinessHourAdditionalDeleted'][$dayName]]);
                    }

                    if ($flag && !empty($post['RegistryBusinessHourAdditional'][$dayName])) {
                        
                        foreach ($post['RegistryBusinessHourAdditional'][$dayName] as $i => $registryBusinessHourAdditional) {
                            
                            if (!empty($registryBusinessHourAdditional['open_at']) || !empty($registryBusinessHourAdditional['close_at'])) {
                                
                                $newModelRegistryBusinessHourAdditional = RegistryBusinessHourAdditional::findOne(['unique_id' => $newModelRegistryBusinessHourDay->id . '-' . $day . '-' . $i]);
                                
                                if (empty($newModelRegistryBusinessHourAdditional)) {
                                    
                                    $newModelRegistryBusinessHourAdditional = new RegistryBusinessHourAdditional();
                                    $newModelRegistryBusinessHourAdditional->unique_id = $newModelRegistryBusinessHourDay->id . '-' . $day . '-' . $i;
                                    $newModelRegistryBusinessHourAdditional->registry_business_hour_id = $newModelRegistryBusinessHourDay->id;
                                    $newModelRegistryBusinessHourAdditional->day = $day;
                                }
                                
                                $newModelRegistryBusinessHourAdditional->is_open = $newModelRegistryBusinessHourDay->is_open;
                                $newModelRegistryBusinessHourAdditional->open_at = !empty($registryBusinessHourAdditional['open_at']) ? $registryBusinessHourAdditional['open_at'] : null;
                                $newModelRegistryBusinessHourAdditional->close_at = !empty($registryBusinessHourAdditional['close_at']) ? $registryBusinessHourAdditional['close_at'] : null;
                                
                                if (!($flag = $newModelRegistryBusinessHourAdditional->save())) {
                                    
                                    break;
                                } else {
                                    
                                    if (empty($dataRegistryBusinessHourAdditional[$dayName])) {
                                        
                                        $dataRegistryBusinessHourAdditional[$dayName] = [];
                                    }
                                    
                                    array_push($dataRegistryBusinessHourAdditional[$dayName], $newModelRegistryBusinessHourAdditional->toArray());
                                }
                            }
                        }
                    }
                }
                
                if ($flag) {
                
                    $model->note_business_hour = !empty($post['RegistryBusiness']['note_business_hour']) ? $post['RegistryBusiness']['note_business_hour'] : null;
                    
                    $flag = $model->save();
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
        
        $dataRegistryBusinessHour = empty($dataRegistryBusinessHour) ? $model->registryBusinessHours : $dataRegistryBusinessHour;
        
        if (empty($dataRegistryBusinessHourAdditional)) {
            
            foreach ($dataRegistryBusinessHour as $businessHour) {
                
                $dayName = 'day' . $businessHour['day'];
                
                $dataRegistryBusinessHourAdditional[$dayName] = [];
                
                if (!empty($businessHour['registryBusinessHourAdditionals'])) {
                    
                    foreach ($businessHour['registryBusinessHourAdditionals'] as $registryBusinessHourAdditional) {
                        
                        array_push($dataRegistryBusinessHourAdditional[$dayName], $registryBusinessHourAdditional);
                    }
                }
            }
        }
        
        return $this->render('update_business_hour', [
            'model' => $model,
            'modelRegistryBusinessHour' => $modelRegistryBusinessHour,
            'dataRegistryBusinessHour' => $dataRegistryBusinessHour,
            'modelRegistryBusinessHourAdditional' => $modelRegistryBusinessHourAdditional,
            'dataRegistryBusinessHourAdditional' => $dataRegistryBusinessHourAdditional,
            'statusApproval' => $statusApproval,
        ]);
    }
    
    /**
     * Deletes an existing RegistryBusiness model.
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

        $return['url'] = Yii::$app->urlManager->createUrl([$this->module->id . '/registry-business/index-' . $statusApproval]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionResubmit($id, $appBId, $appBCounter, $statusApproval) {

        $modelStatusApprovalAction = StatusApprovalAction::find()
            ->andWhere(['url' => 'status-approval-action/fix-incorrect'])
            ->asArray()->one();

        $modelLogStatusApproval = LogStatusApproval::find()
            ->andWhere(['application_business_id' => $appBId])
            ->andWhere(['status_approval_id' => $statusApproval])
            ->andWhere(['is_actual' => true])
            ->andWhere(['application_business_counter' => $appBCounter])
            ->one();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $modelLogStatusApproval->is_actual = false;

        if (($flag = $modelLogStatusApproval->save())) {

            $modelLogStatusApprovalAction = new LogStatusApprovalAction();
            $modelLogStatusApprovalAction->log_status_approval_id = $modelLogStatusApproval['id'];
            $modelLogStatusApprovalAction->status_approval_action_id = $modelStatusApprovalAction['id'];

            if (($flag = $modelLogStatusApprovalAction->save())) {

                $modelLogStatusApproval = new LogStatusApproval();
                $modelLogStatusApproval->application_business_id = $appBId;
                $modelLogStatusApproval->status_approval_id = 'RSBMT';
                $modelLogStatusApproval->is_actual = true;
                $modelLogStatusApproval->application_business_counter = $appBCounter;

                $flag = $modelLogStatusApproval->save();
            }

            if ($flag) {
                
                $flag = $this->run('/approval/status-approval/resubmit', ['appBId' => $appBId, 'regBId' => $id]);
            }
        }

        if ($flag) {

            Yii::$app->session->setFlash('status', 'success');
            Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Success'));
            Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is success. Data has been saved'));

            $transaction->commit();

            return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl([$this->module->id . '/registry-business/index-icorct']));
        } else {

            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
            Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
    
            $transaction->rollBack();
    
            return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl([$this->module->id . '/registry-business/view-icorct', 'id' => $id]));
        }
    }

    public function actionReportByDistrict()
    {
        $tanggal = null;
        $data = null;

        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {

            $basic = RegistryBusiness::find()
                ->joinWith([
                    'district',
                    'district.region',
                ])
                ->andWhere('(registry_business.created_at::date) AT time zone \'Asia/Jakarta\' BETWEEN \'' . $post['tanggal_from'] . '\' AND \'' . $post['tanggal_to'] . '\'')
                ->asArray()->all();

            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from'],'long') . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to'],'long');

            $data = [];
            foreach ($basic as $b) {
                $data[$b['district_id']][] = $b;
            }
        }
        
        return $this->render('report/report_by_district',[
            'tanggal' => $tanggal,
            'data' => $data,
        ]);
    }

    public function actionReportByVillage()
    {
        $tanggal = null;
        $data = null;
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {

            $basic = RegistryBusiness::find()
                ->joinWith([
                    'village',
                    'district',
                ])
                ->andWhere('(registry_business.created_at::date) AT time zone \'Asia/Jakarta\' BETWEEN \'' . $post['tanggal_from'] . '\' AND \'' . $post['tanggal_to'] . '\'')
                ->asArray()->all();

            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from'],'long') . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to'],'long');

            $data = [];
            foreach ($basic as $b) {
                $data[$b['village_id']][] = $b;
            }
        }
        
        return $this->render('report/report_by_village',[
            'tanggal' => $tanggal,
            'data' => $data,
        ]);
    }

    /**
     * Finds the RegistryBusiness model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RegistryBusiness the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RegistryBusiness::findOne($id)) !== null) {
            
            return $model;
        } else {
            
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function index($statusApproval, $title, $actionColumn) {

        $searchModel = new RegistryBusinessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            //->andWhere(['registry_business.user_in_charge' => Yii::$app->user->getIdentity()->id])
            ->andWhere(['log_status_approval.status_approval_id' => $statusApproval])
            ->andWhere(['log_status_approval.is_actual' => 1])
            ->andWhere('registry_business.application_business_counter = application_business.counter')
            ->distinct();

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => $title,
            'statusApproval' => $statusApproval,
            'actionColumn' => $actionColumn,
        ]);
    }

    private function view($id, $statusApproval, $actionButton = null) {

        $model = RegistryBusiness::find()
            ->joinWith([
                'membershipType',
                'city',
                'district',
                'village',
                'userInCharge',
                'registryBusinessCategories' => function ($query) {
                    
                    $query->andOnCondition(['registry_business_category.is_active' => true]);
                },
                'registryBusinessCategories.category',
                'registryBusinessProductCategories' => function ($query) {
                    
                    $query->andOnCondition(['registry_business_product_category.is_active' => true]);
                },
                'registryBusinessHours' => function ($query) {
                    
                    $query->andOnCondition(['registry_business_hour.is_open' => true])
                        ->orderBy(['registry_business_hour.day' => SORT_ASC]);
                },
                'registryBusinessHours.registryBusinessHourAdditionals',
                'registryBusinessProductCategories.productCategory',
                'registryBusinessFacilities' => function ($query) {
                    
                    $query->andOnCondition(['registry_business_facility.is_active' => true]);
                },
                'registryBusinessFacilities.facility',
                'registryBusinessPayments' => function ($query) {
                
                    $query->andOnCondition(['registry_business_payment.is_active' => true]);
                },
                'registryBusinessPayments.paymentMethod',
                'registryBusinessDeliveries' => function ($query) {
                
                    $query->andOnCondition(['registry_business_delivery.is_active' => true]);
                },
                'registryBusinessDeliveries.deliveryMethod',
                'registryBusinessImages',
                'registryBusinessContactPeople' => function ($query) {
                    
                    $query->orderBy(['registry_business_contact_person.id' => SORT_ASC]);
                },
                'registryBusinessContactPeople.person',
                'applicationBusiness',
                'applicationBusiness.logStatusApprovals' => function ($query) {
                    
                    $query->andOnCondition(['log_status_approval.is_actual' => true]);
                },
                'applicationBusiness.logStatusApprovals.statusApproval',
            ])
            ->andWhere(['registry_business.id' => $id])
            ->asArray()->one();
        
        return $this->render('view', [
            'model' => $model,
            'statusApproval' => $statusApproval,
            'actionButton' => $actionButton,
        ]);
    }
}
