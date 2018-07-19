<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\RegistryBusiness;
use core\models\search\RegistryBusinessSearch;
use core\models\RegistryBusinessCategory;
use core\models\RegistryBusinessProductCategory;
use core\models\RegistryBusinessHour;
use core\models\RegistryBusinessFacility;
use core\models\RegistryBusinessImage;
use core\models\Business;
use sybase\SybaseController;
use sycomponent\AjaxRequest;
use sycomponent\Tools;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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

    /**
     * Lists all RegistryBusiness models.
     * @return mixed
     */
    public function actionIndex($type = null)
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = '@backoffice/views/layouts/ajax';
        }

        $searchModel = new RegistryBusinessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($type == 'my') {
            $dataProvider->query
                ->andWhere(['user_in_charge' => Yii::$app->user->getIdentity()->id]);
        }

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $this->getView()->params['type'] = $type;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RegistryBusiness model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $type = null)
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = 'ajax';
        }

        $model = RegistryBusiness::find()
                ->joinWith([
                    'membershipType',
                    'city',
                    'district',
                    'village',
                    'userInCharge',
                    'registryBusinessCategories' => function($query) {
                        $query->andOnCondition(['registry_business_category.is_active' => true]);
                    },
                    'registryBusinessCategories.category',
                    'registryBusinessProductCategories' => function($query) {
                        $query->andOnCondition(['registry_business_product_category.is_active' => true]);
                    },
                    'registryBusinessHours' => function($query) {
                        $query->andOnCondition(['registry_business_hour.is_open' => true]);
                    },
                    'registryBusinessProductCategories.productCategory',
                    'registryBusinessProductCategories.productCategory.parent' => function($query) {
                        $query->from('product_category parent');
                    },
                    'registryBusinessFacilities' => function($query) {
                        $query->andOnCondition(['registry_business_facility.is_active' => true]);
                    },
                    'registryBusinessFacilities.facility',
                    'registryBusinessImages',
                ])
                ->andWhere(['registry_business.id' => $id])
                ->one();

        $this->getView()->params['type'] = $type;

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new RegistryBusiness model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($save = null, $type = null)
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = 'ajax';
        }

        $render = 'create';

        $model = new RegistryBusiness();
        $model->setScenario(RegistryBusiness::SCENARIO_CREATE);

        $modelRegistryBusinessCategory = new RegistryBusinessCategory();
        $dataRegistryBusinessCategory = [];

        $modelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
        $dataRegistryBusinessProductCategoryParent = [];
        $dataRegistryBusinessProductCategoryChild = [];

        $modelRegistryBusinessHour = new RegistryBusinessHour();
        $dataRegistryBusinessHour = [];

        $modelRegistryBusinessFacility = new RegistryBusinessFacility();
        $dataRegistryBusinessFacility = [];

        $modelRegistryBusinessImage = new RegistryBusinessImage();
        $modelRegistryBusinessImage->setScenario(RegistryBusinessImage::SCENARIO_CREATE);
        $dataRegistryBusinessImage = [];

        if ($model->load(($post = Yii::$app->request->post()))) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                $model->status = 'Pending';
                $model->user_in_charge = Yii::$app->user->identity->id;

                $flag = $model->save();

                if ($flag) {

                    if (!empty($post['RegistryBusinessCategory']['category_id'])) {

                        foreach ($post['RegistryBusinessCategory']['category_id'] as $value) {

                            $newModelRegistryBusinessCategory = new RegistryBusinessCategory();
                            $newModelRegistryBusinessCategory->unique_id = $model->id . '-' . $value;
                            $newModelRegistryBusinessCategory->registry_business_id = $model->id;
                            $newModelRegistryBusinessCategory->category_id = $value;
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

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id'] && $post['RegistryBusinessProductCategory']['product_category_id']['parent'])) {

                        if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['parent'])) {

                            foreach ($post['RegistryBusinessProductCategory']['product_category_id']['parent'] as $value) {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $value;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $value;
                                $newModelRegistryBusinessProductCategory->is_active = true;

                                if (!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                    break;
                                } else {
                                    array_push($dataRegistryBusinessProductCategoryParent, $newModelRegistryBusinessProductCategory->toArray());
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id'] && $post['RegistryBusinessProductCategory']['product_category_id']['child'])) {

                        if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['child'])) {

                            foreach ($post['RegistryBusinessProductCategory']['product_category_id']['child'] as $value) {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $value;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $value;
                                $newModelRegistryBusinessProductCategory->is_active = true;

                                if (!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                    break;
                                } else {
                                    array_push($dataRegistryBusinessProductCategoryChild, $newModelRegistryBusinessProductCategory->toArray());
                                }
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

                            if (!$flag = $newModelRegistryBusinessHourDay->save()) {
                                break;
                            } else {
                                array_push($dataRegistryBusinessHour, $newModelRegistryBusinessHourDay->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessFacility']['facility_id'])) {

                        foreach ($post['RegistryBusinessFacility']['facility_id'] as $value) {

                            $newModelRegistryBusinessFacility = new RegistryBusinessFacility();
                            $newModelRegistryBusinessFacility->unique_id = $model->id . '-' . $value;
                            $newModelRegistryBusinessFacility->registry_business_id = $model->id;
                            $newModelRegistryBusinessFacility->facility_id = $value;
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

                    $newModelRegistryBusinessImage = new RegistryBusinessImage(['registry_business_id' => $model->id]);

                    if ($newModelRegistryBusinessImage->load($post)) {

                        $images = Tools::uploadFiles('/img/registry_business/', $newModelRegistryBusinessImage, 'image', 'registry_business_id', '', true);

                        foreach ($images as $image) {

                            $newModelRegistryBusinessImage = new RegistryBusinessImage();
                            $newModelRegistryBusinessImage->registry_business_id = $model->id;
                            $newModelRegistryBusinessImage->image = $image;
                            $newModelRegistryBusinessImage->type = 'Gallery';

                            if (!($flag = $newModelRegistryBusinessImage->save())) {
                                break;
                            }
                        }
                    }
                }

                if ($flag) {

                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Create Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Create data process is success. Data has been saved'));

                    $transaction->commit();

                    return $this->runAction('view', ['id' => $model->id, 'type' => $type]);
                } else {

                    $model->setIsNewRecord(true);

                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Create Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Create data process is fail. Data fail to save'));

                    $transaction->rollBack();
                }
            }
        }

        $this->getView()->params['type'] = $type;

        return $this->render($render, [
            'model' => $model,
            'modelRegistryBusinessCategory' => $modelRegistryBusinessCategory,
            'dataRegistryBusinessCategory' => $dataRegistryBusinessCategory,
            'modelRegistryBusinessProductCategory' => $modelRegistryBusinessProductCategory,
            'dataRegistryBusinessProductCategoryParent' => $dataRegistryBusinessProductCategoryParent,
            'dataRegistryBusinessProductCategoryChild' => $dataRegistryBusinessProductCategoryChild,
            'modelRegistryBusinessFacility' => $modelRegistryBusinessFacility,
            'dataRegistryBusinessFacility' => $dataRegistryBusinessFacility,
            'modelRegistryBusinessImage' => $modelRegistryBusinessImage,
            'dataRegistryBusinessImage' => $dataRegistryBusinessImage,
            'modelRegistryBusinessHour' => $modelRegistryBusinessHour,
            'dataRegistryBusinessHour' => $dataRegistryBusinessHour,
        ]);
    }

    /**
     * Updates an existing RegistryBusiness model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $save = null, $type = null)
    {
        return $this->formUpdate($id, 'update', $save, $type);
    }

    /**
     * Deletes an existing RegistryBusiness model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $type = null)
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

        $return['url'] = Yii::$app->urlManager->createUrl(['registry-business/index', 'type' => $type]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionResubmit($id, $save = null, $type = null) {

        return $this->formUpdate($id, 'resubmit', $save, $type);
    }

    public function actionUpgradeMembership($id, $save = null, $type = null)
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = 'ajax';
        }

        $modelBusiness = Business::find()
                ->joinWith([
                    'businessLocation',
                    'businessDetail',
                    'userInCharge',
                    'businessDetail',
                    'businessLocation',
                    'businessCategories' => function($query) {
                        $query->andOnCondition(['business_category.is_active' => true]);
                    },
                    'businessCategories.category',
                    'businessProductCategories' => function($query) {
                        $query->andOnCondition(['business_product_category.is_active' => true]);
                    },
                    'businessHours' => function($query) {
                        $query->andOnCondition(['business_hour.is_open' => true]);
                    },
                    'businessProductCategories.productCategory',
                    'businessProductCategories.productCategory.parent' => function($query) {
                        $query->from('product_category parent');
                    },
                    'businessFacilities' => function($query) {
                        $query->andOnCondition(['business_facility.is_active' => true]);
                    },
                    'businessFacilities.facility',
                    'businessImages',
                ])
                ->andWhere(['business.id' => $id])
                ->one();

        $model = $modelBusiness->registryBusinessApprovalLogs[count($modelBusiness['registryBusinessApprovalLogs']) - 1]->registryBusiness;

        if ($model->load(($post = Yii::$app->request->post()))) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                $modelRegistryBusiness = new RegistryBusiness();
                $modelRegistryBusiness->membership_type_id = $model->membership_type_id;
                $modelRegistryBusiness->name = $model->name;
                $modelRegistryBusiness->unique_name = $model->unique_name;
                $modelRegistryBusiness->email = $model->email;
                $modelRegistryBusiness->phone1 = $model->phone1;
                $modelRegistryBusiness->phone2 = $model->phone2;
                $modelRegistryBusiness->phone3 = $model->phone3;
                $modelRegistryBusiness->address_type = $model->address_type;
                $modelRegistryBusiness->address = $model->address;
                $modelRegistryBusiness->address_info = $model->address_info;
                $modelRegistryBusiness->city_id = $model->city_id;
                $modelRegistryBusiness->district_id = $model->district_id;
                $modelRegistryBusiness->village_id = $model->village_id;
                $modelRegistryBusiness->coordinate = $model->coordinate;
                $modelRegistryBusiness->status = 'Pending';
                $modelRegistryBusiness->user_in_charge = $model->user_in_charge;

                if ($modelRegistryBusiness->save()) {

                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is success. Data has been saved'));

                    AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['business/index']));
                    return $this->render('@backend/views/site/zero');
                } else {

                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                }
            }
        }

        $this->getView()->params['type'] = $type;

        return $this->render('upgrade_membership', [
            'model' => $model,
            'modelBusiness' => $modelBusiness,
        ]);
    }

    public function actionReportByDistrict()
    {
        if (Yii::$app->request->isAjax) {
            $this->layout = 'ajax';
        }

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
            foreach ($basic as $b)
            {
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
        if (Yii::$app->request->isAjax) {
            $this->layout = 'ajax';
        }

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
            foreach ($basic as $b)
            {
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

    private function formUpdate($id, $render, $save = null, $type = null) {

        if (Yii::$app->request->isAjax) {
            $this->layout = 'ajax';
        }

        $model = $this->findModel($id);

        $modelRegistryBusinessCategory = new RegistryBusinessCategory();
        $dataRegistryBusinessCategory = [];

        $modelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();
        $dataRegistryBusinessProductCategoryParent = [];
        $dataRegistryBusinessProductCategoryChild = [];

        $modelRegistryBusinessHour = new RegistryBusinessHour();
        $dataRegistryBusinessHour = [];

        $modelRegistryBusinessFacility = new RegistryBusinessFacility();
        $dataRegistryBusinessFacility = [];

        $modelRegistryBusinessImage = new RegistryBusinessImage();
        $dataRegistryBusinessImage = [];

        if ($model->load(($post = Yii::$app->request->post()))) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                if (!empty($post['resubmit'])) {

                    $model->status = 'Pending';
                    $model->user_in_charge = Yii::$app->user->identity->id;
                }

                $flag = $model->save();

                if ($flag) {

                    if (!empty($post['RegistryBusinessCategory']['category_id'])) {

                        foreach ($post['RegistryBusinessCategory']['category_id'] as $value) {

                            $newModelRegistryBusinessCategory = new RegistryBusinessCategory();

                            $newModelRegistryBusinessCategory = RegistryBusinessCategory::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelRegistryBusinessCategory)) {

                                $newModelRegistryBusinessCategory->is_active = true;
                            } else {

                                $newModelRegistryBusinessCategory = new RegistryBusinessCategory();

                                $newModelRegistryBusinessCategory->unique_id = $model->id . '-' . $value;
                                $newModelRegistryBusinessCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessCategory->category_id = $value;
                                $newModelRegistryBusinessCategory->is_active = true;
                            }

                            if (!($flag = $newModelRegistryBusinessCategory->save())) {
                                break;
                            } else {
                                array_push($dataRegistryBusinessCategory, $newModelRegistryBusinessCategory->toArray());
                            }
                        }
                    } else {

                        foreach ($model->registryBusinessCategories as $valueRegistryBusinessCategory) {

                            $valueRegistryBusinessCategory->is_active = false;

                            if (!($flag = $valueRegistryBusinessCategory->save())) {
                                break;
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessCategory']['category_id'])) {

                        foreach ($model->registryBusinessCategories as $valueRegistryBusinessCategory) {

                            $exist = false;

                            foreach ($post['RegistryBusinessCategory']['category_id'] as $value) {

                                if ($valueRegistryBusinessCategory['category_id'] == $value) {

                                    $exist = true;
                                    break;
                                }
                            }

                            if (!$exist) {

                                $valueRegistryBusinessCategory->is_active = false;

                                if (!($flag = $valueRegistryBusinessCategory->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['parent'])) {

                        foreach ($post['RegistryBusinessProductCategory']['product_category_id']['parent'] as $value) {

                            $newModelRegistryBusinessProductCategory = RegistryBusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelRegistryBusinessProductCategory)) {

                                $newModelRegistryBusinessProductCategory->is_active = true;
                            } else {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();

                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $value;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $value;
                                $newModelRegistryBusinessProductCategory->is_active = true;
                            }

                            if(!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                break;
                            } else {
                                array_push($dataRegistryBusinessProductCategoryParent, $newModelRegistryBusinessProductCategory->toArray());
                            }
                        }
                    } else {

                        foreach ($model->registryBusinessProductCategories as $valueRegistryBusinessProductCategory) {

                            if (empty($valueRegistryBusinessProductCategory->productCategory->parent_id)) {

                                $valueRegistryBusinessProductCategory->is_active = false;

                                if (!($flag = $valueRegistryBusinessProductCategory->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($post['RegistryBusinessProductCategory']['product_category_id']['child'] as $value) {

                            $newModelRegistryBusinessProductCategory = RegistryBusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelRegistryBusinessProductCategory)) {

                                $newModelRegistryBusinessProductCategory->is_active = true;
                            } else {

                                $newModelRegistryBusinessProductCategory = new RegistryBusinessProductCategory();

                                $newModelRegistryBusinessProductCategory->unique_id = $model->id . '-' . $value;
                                $newModelRegistryBusinessProductCategory->registry_business_id = $model->id;
                                $newModelRegistryBusinessProductCategory->product_category_id = $value;
                                $newModelRegistryBusinessProductCategory->is_active = true;
                            }

                            if(!($flag = $newModelRegistryBusinessProductCategory->save())) {
                                break;
                            } else {
                                array_push($dataRegistryBusinessProductCategoryChild, $newModelRegistryBusinessProductCategory->toArray());
                            }
                        }
                    } else {

                        foreach ($model->registryBusinessProductCategories as $valueRegistryBusinessProductCategory) {

                            if (!empty($valueRegistryBusinessProductCategory->productCategory->parent_id)) {

                                $valueRegistryBusinessProductCategory->is_active = false;

                                if (!($flag = $valueRegistryBusinessProductCategory->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessProductCategory']['product_category_id']) && !empty($post['RegistryBusinessProductCategory']['product_category_id']['parent']) && !empty($post['RegistryBusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($model->registryBusinessProductCategories as $valueRegistryBusinessProductCategory) {

                            $exist = false;

                            foreach ($post['RegistryBusinessProductCategory']['product_category_id'] as $data) {

                                foreach ($data as $value) {

                                    if ($valueRegistryBusinessProductCategory['product_category_id'] == $value) {

                                        $exist = true;
                                        break 2;
                                    }
                                }
                            }

                            if (!$exist) {

                                $valueRegistryBusinessProductCategory->is_active = false;

                                if (!($flag = $valueRegistryBusinessProductCategory->save())) {
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

                            if (!$flag = $newModelRegistryBusinessHourDay->save()) {
                                break;
                            } else {
                                array_push($dataRegistryBusinessHour, $newModelRegistryBusinessHourDay->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessFacility']['facility_id'])) {

                        foreach ($post['RegistryBusinessFacility']['facility_id'] as $value) {

                            $newModelRegistryBusinessFacility = new RegistryBusinessFacility();

                            $newModelRegistryBusinessFacility = RegistryBusinessFacility::findOne(['unique_id' => $model->id . '-' . $value]);

                            if (!empty($newModelRegistryBusinessFacility)) {

                                $newModelRegistryBusinessFacility->is_active = true;
                            } else {

                                $newModelRegistryBusinessFacility = new RegistryBusinessFacility();

                                $newModelRegistryBusinessFacility->unique_id = $model->id . '-' . $value;
                                $newModelRegistryBusinessFacility->registry_business_id = $model->id;
                                $newModelRegistryBusinessFacility->facility_id = $value;
                                $newModelRegistryBusinessFacility->is_active = true;
                            }

                            if (!($flag = $newModelRegistryBusinessFacility->save())) {
                                break;
                            } else {
                                array_push($dataRegistryBusinessFacility, $newModelRegistryBusinessFacility->toArray());
                            }
                        }
                    } else {

                        foreach ($model->registryBusinessFacilities as $valueRegistryBusinessFacility) {

                            $valueRegistryBusinessFacility->is_active = false;

                            if (!($flag = $valueRegistryBusinessFacility->save())) {
                                break;
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessFacility']['facility_id'])) {

                        foreach ($model->registryBusinessFacilities as $valueRegistryBusinessFacility) {

                            $exist = false;

                            foreach ($post['RegistryBusinessFacility']['facility_id'] as $value) {

                                if ($valueRegistryBusinessFacility['facility_id'] == $value) {

                                    $exist = true;
                                    break;
                                }
                            }

                            if (!$exist) {

                                $valueRegistryBusinessFacility->is_active = false;

                                if (!($flag = $valueRegistryBusinessFacility->save())) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    $newModelRegistryBusinessImage = new RegistryBusinessImage(['registry_business_id' => $model->id]);

                    if ($newModelRegistryBusinessImage->load($post)) {

                        $images = Tools::uploadFiles('/img/registry_business/', $newModelRegistryBusinessImage, 'image', 'registry_business_id', '', true);

                        foreach ($images as $image) {

                            $newModelRegistryBusinessImage = new RegistryBusinessImage();
                            $newModelRegistryBusinessImage->registry_business_id = $model->id;
                            $newModelRegistryBusinessImage->image = $image;
                            $newModelRegistryBusinessImage->type = 'Gallery';

                            if (!($flag = $newModelRegistryBusinessImage->save())) {
                                break;
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['RegistryBusinessImageDelete'])) {
                        $flag = RegistryBusinessImage::deleteAll(['id' => $post['RegistryBusinessImageDelete']]);
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

        $dataRegistryBusinessCategory = empty($dataRegistryBusinessCategory) ?
                RegistryBusinessCategory::find()
                    ->andWhere(['registry_business_id' => $id, 'is_active' => true])
                    ->asArray()->all()
                :
                $dataRegistryBusinessCategory;

        $dataRegistryBusinessProductCategory = RegistryBusinessProductCategory::find()
                ->joinWith('productCategory')
                ->andWhere(['registry_business_product_category.registry_business_id' => $id, 'registry_business_product_category.is_active' => 1])
                ->asArray()->all();

        $registryBusinessProductCategoryParent = [];
        $registryBusinessProductCategoryChild = [];

        foreach ($dataRegistryBusinessProductCategory as $valueRegistryBusinessProductCategory) {

            if (empty($valueRegistryBusinessProductCategory['productCategory']['parent_id'])) {
                $registryBusinessProductCategoryParent[] = $valueRegistryBusinessProductCategory;
            } else {
                $registryBusinessProductCategoryChild[] = $valueRegistryBusinessProductCategory;
            }
        }

        $dataRegistryBusinessProductCategoryParent = empty($dataRegistryBusinessProductCategoryParent) ? $registryBusinessProductCategoryParent : $dataRegistryBusinessProductCategoryParent;
        $dataRegistryBusinessProductCategoryChild = empty($dataRegistryBusinessProductCategoryChild) ? $registryBusinessProductCategoryChild : $dataRegistryBusinessProductCategoryChild;

        $dataRegistryBusinessHour = empty($dataRegistryBusinessHour) ?
                RegistryBusinessHour::find()
                    ->andWhere(['registry_business_id' => $id])
                    ->asArray()->all()
                :
                $dataRegistryBusinessHour;

        $dataRegistryBusinessFacility = empty($dataRegistryBusinessFacility) ?
                RegistryBusinessFacility::find()
                    ->joinWith('facility')
                    ->andWhere(['registry_business_id' => $id, 'is_active' => true])
                    ->asArray()->all()
                :
                $dataRegistryBusinessFacility;

        $dataRegistryBusinessImage = RegistryBusinessImage::find()
                ->andWhere(['registry_business_id' => $id])
                ->asArray()->all();

        $this->getView()->params['type'] = $type;

        return $this->render($render, [
            'model' => $model,
            'modelRegistryBusinessCategory' => $modelRegistryBusinessCategory,
            'dataRegistryBusinessCategory' => $dataRegistryBusinessCategory,
            'modelRegistryBusinessProductCategory' => $modelRegistryBusinessProductCategory,
            'dataRegistryBusinessProductCategoryParent' => $dataRegistryBusinessProductCategoryParent,
            'dataRegistryBusinessProductCategoryChild' => $dataRegistryBusinessProductCategoryChild,
            'modelRegistryBusinessHour' => $modelRegistryBusinessHour,
            'dataRegistryBusinessHour' => $dataRegistryBusinessHour,
            'modelRegistryBusinessFacility' => $modelRegistryBusinessFacility,
            'dataRegistryBusinessFacility' => $dataRegistryBusinessFacility,
            'modelRegistryBusinessImage' => $modelRegistryBusinessImage,
            'dataRegistryBusinessImage' => $dataRegistryBusinessImage,
        ]);
    }
}
