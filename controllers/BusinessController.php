<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\Business;
use core\models\search\BusinessSearch;
use core\models\BusinessCategory;
use core\models\BusinessProductCategory;
use core\models\BusinessHour;
use core\models\BusinessHourAdditional;
use core\models\BusinessFacility;
use core\models\BusinessImage;
use core\models\BusinessContactPerson;
use core\models\ProductCategory;
use core\models\RegistryBusinessContactPerson;
use core\models\Person;
use core\models\UserLevel;
use sycomponent\AjaxRequest;
use sycomponent\Tools;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use core\models\User;
use core\models\UserPerson;
use core\models\UserRole;
use core\models\UserAkses;
use core\models\UserAksesAppModule;

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

    public function actionMember()
    {
        $searchModel = new BusinessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//         $dataProvider->query->andWhere(['user_in_charge' => Yii::$app->user->getIdentity()->id]);

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        return $this->render('member', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewMember($id)
    {
        $model = Business::find()
            ->joinWith([
                'membershipType',
                'businessLocation.city',
                'businessLocation.district',
                'businessLocation.village',
                'userInCharge',
                'businessCategories' => function ($query) {

                    $query->andOnCondition(['business_category.is_active' => true]);
                },
                'businessCategories.category',
                'businessProductCategories' => function ($query) {

                    $query->andOnCondition(['business_product_category.is_active' => true]);
                },
                'businessProductCategories.productCategory' => function ($query) {

                    $query->andOnCondition(['<>', 'product_category.type', 'Menu']);
                },
                'businessHours' => function ($query) {

                    $query->andOnCondition(['business_hour.is_open' => true])
                        ->orderBy(['business_hour.day' => SORT_ASC]);
                },
                'businessHours.businessHourAdditionals',
                'businessFacilities' => function ($query) {

                    $query->andOnCondition(['business_facility.is_active' => true]);
                },
                'businessFacilities.facility',
                'businessPayments' => function ($query) {

                    $query->andOnCondition(['business_payment.is_active' => true]);
                },
                'businessPayments.paymentMethod',
                'businessDeliveries' => function ($query) {

                    $query->andOnCondition(['business_delivery.is_active' => true]);
                },
                'businessDeliveries.deliveryMethod',
                'businessDetail',
                'businessImages' => function ($query) {

                    $query->orderBy(['business_image.order' => SORT_ASC]);
                },
                'businessContactPeople' => function ($query) {

                    $query->orderBy(['business_contact_person.created_at' => SORT_ASC]);
                },
                'businessContactPeople.person',
                'applicationBusiness',
                'applicationBusiness.logStatusApprovals' => function ($query) {

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

        if ($model->load(Yii::$app->request->post()) && $modelBusinessLocation->load(Yii::$app->request->post())) {

            if (empty($save)) {

                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(ActiveForm::validate($model), ActiveForm::validate($modelBusinessLocation));
            } else {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                if (($flag = $model->save())) {

                    $modelBusinessLocation->address = trim(str_replace("\n", '', Yii::$app->request->post()['BusinessLocation']['address']));
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
                'businessCategories' => function ($query) {

                    $query->andOnCondition(['business_category.is_active' => true]);
                },
                'businessCategories.category',
                'businessProductCategories' => function ($query) {

                    $query->andOnCondition(['business_product_category.is_active' => true]);
                },
                'businessProductCategories.productCategory' => function ($query) {

                    $query->andOnCondition(['<>', 'product_category.type', 'Menu']);
                },
                'businessFacilities' => function ($query) {

                    $query->andOnCondition(['business_facility.is_active' => true]);
                },
                'businessFacilities.facility',
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

        $modelBusinessDetail = $model->businessDetail;

        if ($modelBusinessDetail->load(($post = Yii::$app->request->post()))) {

            if (!empty($save)) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

                if (($flag = $model->save())) {

                    if (!empty($post['BusinessCategory']['category_id'])) {

                        foreach ($post['BusinessCategory']['category_id'] as $categoryId) {

                            $newModelBusinessCategory = BusinessCategory::findOne(['unique_id' => $model->id . '-' . $categoryId]);

                            if (!empty($newModelBusinessCategory)) {

                                $newModelBusinessCategory->is_active = true;
                            } else {

                                $newModelBusinessCategory = new BusinessCategory();
                                $newModelBusinessCategory->unique_id = $model->id . '-' . $categoryId;
                                $newModelBusinessCategory->business_id = $model->id;
                                $newModelBusinessCategory->category_id = $categoryId;
                                $newModelBusinessCategory->is_active = true;
                            }

                            if (!($flag = $newModelBusinessCategory->save())) {

                                break;
                            } else {

                                array_push($dataBusinessCategory, $newModelBusinessCategory->toArray());
                            }
                        }

                        if ($flag) {

                            foreach ($model->businessCategories as $existModelBusinessCategory) {

                                $exist = false;

                                foreach ($post['BusinessCategory']['category_id'] as $categoryId) {

                                    if ($existModelBusinessCategory['category_id'] == $categoryId) {

                                        $exist = true;
                                        break;
                                    }
                                }

                                if (!$exist) {

                                    $existModelBusinessCategory->is_active = false;

                                    if (!($flag = $existModelBusinessCategory->save())) {

                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessProductCategory']['product_category_id']['parent'])) {

                        foreach ($post['BusinessProductCategory']['product_category_id']['parent'] as $productCategoryId) {

                            $newModelBusinessProductCategory = BusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $productCategoryId]);

                            if (empty($newModelBusinessProductCategory)) {

                                $newModelBusinessProductCategory = new BusinessProductCategory();
                                $newModelBusinessProductCategory->unique_id = $model->id . '-' . $productCategoryId;
                                $newModelBusinessProductCategory->business_id = $model->id;
                                $newModelBusinessProductCategory->product_category_id = $productCategoryId;
                            }

                            $newModelBusinessProductCategory->is_active = true;

                            if (!($flag = $newModelBusinessProductCategory->save())) {

                                break;
                            } else {

                                array_push($dataBusinessProductCategoryParent, $newModelBusinessProductCategory->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($post['BusinessProductCategory']['product_category_id']['child'] as $productCategoryId) {

                            $newModelBusinessProductCategory = BusinessProductCategory::findOne(['unique_id' => $model->id . '-' . $productCategoryId]);

                            if (empty($newModelBusinessProductCategory)) {

                                $newModelBusinessProductCategory = new BusinessProductCategory();
                                $newModelBusinessProductCategory->unique_id = $model->id . '-' . $productCategoryId;
                                $newModelBusinessProductCategory->business_id = $model->id;
                                $newModelBusinessProductCategory->product_category_id = $productCategoryId;
                            }

                            $newModelBusinessProductCategory->is_active = true;

                            if (!($flag = $newModelBusinessProductCategory->save())) {

                                break;
                            } else {

                                array_push($dataBusinessProductCategoryChild, $newModelBusinessProductCategory->toArray());
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessProductCategory']['product_category_id']['parent']) && !empty($post['BusinessProductCategory']['product_category_id']['child'])) {

                        foreach ($model->businessProductCategories as $existModelBusinessProductCategory) {

                            if (!empty($existModelBusinessProductCategory->productCategory)) {

                                $exist = false;

                                foreach ($post['BusinessProductCategory']['product_category_id'] as $dataProductCategory) {

                                    foreach ($dataProductCategory as $productCategoryId) {

                                        if ($existModelBusinessProductCategory['product_category_id'] == $productCategoryId) {

                                            $exist = true;
                                            break 2;
                                        }
                                    }
                                }

                                if (!$exist) {

                                    $existModelBusinessProductCategory->is_active = false;

                                    if (!($flag = $existModelBusinessProductCategory->save())) {

                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['BusinessFacility']['facility_id'])) {

                        foreach ($post['BusinessFacility']['facility_id'] as $facilityId) {

                            $newModelBusinessFacility = BusinessFacility::findOne(['unique_id' => $model->id . '-' . $facilityId]);

                            if (!empty($newModelBusinessFacility)) {

                                $newModelBusinessFacility->is_active = true;
                            } else {

                                $newModelBusinessFacility = new BusinessFacility();
                                $newModelBusinessFacility->unique_id = $model->id . '-' . $facilityId;
                                $newModelBusinessFacility->business_id = $model->id;
                                $newModelBusinessFacility->facility_id = $facilityId;
                                $newModelBusinessFacility->is_active = true;
                            }

                            if (!($flag = $newModelBusinessFacility->save())) {

                                break;
                            } else {

                                array_push($dataBusinessFacility, $newModelBusinessFacility->toArray());
                            }
                        }

                        if ($flag) {

                            foreach ($model->businessFacilities as $existModelBusinessFacility) {

                                $exist = false;

                                foreach ($post['BusinessFacility']['facility_id'] as $facilityId) {

                                    if ($existModelBusinessFacility['facility_id'] == $facilityId) {

                                        $exist = true;
                                        break;
                                    }
                                }

                                if (!$exist) {

                                    $existModelBusinessFacility->is_active = false;

                                    if (!($flag = $existModelBusinessFacility->save())) {

                                        break;
                                    }
                                }
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

        $businessProductCategoryParent = [];
        $businessProductCategoryChild = [];

        foreach ($model['businessProductCategories'] as $valueBusinessProductCategory) {

            if ($valueBusinessProductCategory['productCategory']['type'] == 'General') {

                $businessProductCategoryParent[] = $valueBusinessProductCategory;
            } else if ($valueBusinessProductCategory['productCategory']['type'] == 'Specific' || $valueBusinessProductCategory['productCategory']['type'] == 'Specific-Menu') {

                $businessProductCategoryChild[] = $valueBusinessProductCategory;
            }
        }

        $dataBusinessCategory = empty($dataBusinessCategory) ? $model['businessCategories'] : $dataBusinessCategory;
        $dataBusinessProductCategoryParent = empty($dataBusinessProductCategoryParent) ? $businessProductCategoryParent : $dataBusinessProductCategoryParent;
        $dataBusinessProductCategoryChild = empty($dataBusinessProductCategoryChild) ? $businessProductCategoryChild : $dataBusinessProductCategoryChild;
        $dataBusinessFacility = empty($dataBusinessFacility) ? $model['businessFacilities'] : $dataBusinessFacility;

        $modelProductCategory = ProductCategory::find()
            ->andWhere(['<>', 'type', 'Menu'])
            ->andWhere(['is_active' => true])
            ->orderBy('name')->asArray()->all();

        $dataProductCategoryParent = [];
        $dataProductCategoryChild = [];

        foreach ($modelProductCategory as $dataProductCategory) {

            if ($dataProductCategory['type'] == 'General') {

                $dataProductCategoryParent[$dataProductCategory['id']] = $dataProductCategory['name'];
            } else {

                $dataProductCategoryChild[$dataProductCategory['id']] = $dataProductCategory['name'];
            }
        }

        return $this->render('update_marketing_info', [
            'model' => $model,
            'modelBusinessCategory' => $modelBusinessCategory,
            'dataBusinessCategory' => $dataBusinessCategory,
            'modelBusinessProductCategory' => $modelBusinessProductCategory,
            'dataBusinessProductCategoryParent' => $dataBusinessProductCategoryParent,
            'dataBusinessProductCategoryChild' => $dataBusinessProductCategoryChild,
            'modelBusinessFacility' => $modelBusinessFacility,
            'dataBusinessFacility' => $dataBusinessFacility,
            'modelBusinessDetail' => $modelBusinessDetail,
            'dataProductCategoryParent' => $dataProductCategoryParent,
            'dataProductCategoryChild' => $dataProductCategoryChild,
        ]);
    }

    public function actionUpdateGalleryPhoto($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessImages' => function ($query) {

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

                        if ($order == count($post['BusinessImageDelete'])) {

                            $flag = false;
                        } else {

                            $deletedBusinessImageId = $post['BusinessImageDelete'];
                        }
                    }
                }

                if ($flag) {

                    $newModelBusinessImage = new BusinessImage();

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
                        $existModelBusinessImage->is_primary = !empty($post['thumbnail']) && $post['thumbnail'] == $existModelBusinessImage->id;
                        $existModelBusinessImage->category = $post['category'][$existModelBusinessImage->id];
                        $existModelBusinessImage->order = $post['order'][$existModelBusinessImage->id];

                        if (!($flag = $existModelBusinessImage->save())) {

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

    public function actionUpdateContactPerson($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessContactPeople' => function ($query) {

                    $query->orderBy(['business_contact_person.created_at' => SORT_ASC]);
                },
                'businessContactPeople.person',
                'businessContactPeople.person.userPerson',
            ])
            ->andWhere(['business.id' => $id])
            ->one();

        $modelPerson = new Person();
        $modelBusinessContactPerson = new BusinessContactPerson();
        $dataBusinessContactPerson = [];

        $isEmpty = false;

        if (!empty(($post = Yii::$app->request->post()))) {

            if (!empty($save)) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = true;

                $isEmpty = (empty($post['Person']) && empty($post['BusinessContactPerson']));

                if (!empty($post['BusinessContactPersonDeleted'])) {

                    $modelRegistryBusinessContactPerson = RegistryBusinessContactPerson::findOne(['person_id' => $post['BusinessContactPersonDeleted']]);

                    if (!empty($modelRegistryBusinessContactPerson)) {

                        $flag = RegistryBusinessContactPerson::deleteAll(['person_id' => $post['BusinessContactPersonDeleted']]);
                    }

                    if ($flag) {

                        if (($flag = BusinessContactPerson::deleteAll(['person_id' => $post['BusinessContactPersonDeleted']]))) {

                            foreach ($post['BusinessContactPersonDeleted'] as $i => $dataContactPersonDeleted) {

                                if (empty($model->businessContactPeople[$i]->person->userPerson)) {

                                    if (!($flag = $model->businessContactPeople[$i]->person->delete())) {

                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    if (!empty($post['Person']) && !empty($post['BusinessContactPerson'])) {

                        foreach ($post['Person'] as $i => $person) {

                            if (!empty($model['businessContactPeople'][$i])) {

                                $newModelPerson = Person::findOne(['id' => $model['businessContactPeople'][$i]['person_id']]);
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

                                $newModelBusinessContactPerson = BusinessContactPerson::findOne(['person_id' => $newModelPerson->id, 'business_id' => $id]);

                                if (empty($newModelBusinessContactPerson)) {

                                    $newModelBusinessContactPerson = new BusinessContactPerson();
                                    $newModelBusinessContactPerson->business_id = $model->id;
                                    $newModelBusinessContactPerson->person_id = $newModelPerson->id;
                                }

                                $newModelBusinessContactPerson->position = $post['BusinessContactPerson'][$i]['position'];
                                $newModelBusinessContactPerson->is_primary_contact = !empty($post['BusinessContactPerson'][$i]['is_primary_contact']);
                                $newModelBusinessContactPerson->note = $post['BusinessContactPerson'][$i]['note'];

                                if (!($flag = $newModelBusinessContactPerson->save())) {

                                    break;
                                } else {

                                    array_push($dataBusinessContactPerson, ArrayHelper::merge($newModelBusinessContactPerson->toArray(), $newModelPerson->toArray()));
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

        if (!$isEmpty) {

            if (empty($dataBusinessContactPerson)) {

                foreach ($model->businessContactPeople as $dataContactPerson) {

                    array_push($dataBusinessContactPerson, ArrayHelper::merge($dataContactPerson->toArray(), $dataContactPerson->person->toArray()));
                }
            }
        }

        return $this->render('update_contact_person', [
            'model' => $model,
            'modelPerson' => $modelPerson,
            'modelBusinessContactPerson' => $modelBusinessContactPerson,
            'dataBusinessContactPerson' => $dataBusinessContactPerson,
        ]);
    }

    public function actionUpdateBusinessHour($id, $save = null)
    {
        $model = Business::find()
            ->joinWith([
                'businessDetail',
                'businessHours' => function ($query) {

                    $query->orderBy(['business_hour.day' => SORT_ASC]);
                },
                'businessHours.businessHourAdditionals',
            ])
            ->andWhere(['business.id' => $id])
            ->one();

        $modelBusinessHour = new BusinessHour();
        $dataBusinessHour = [];

        $modelBusinessHourAdditional = new BusinessHourAdditional();
        $dataBusinessHourAdditional = [];

        if (!empty($post = Yii::$app->request->post())) {

            if (!empty($save)) {

                $transaction = Yii::$app->db->beginTransaction();
                $flag = false;

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

                        $newModelBusinessHourDay->is_open = !empty($post['BusinessHour'][$dayName]['is_open']);
                        $newModelBusinessHourDay->open_at = !empty($post['BusinessHour'][$dayName]['open_at']) ? $post['BusinessHour'][$dayName]['open_at'] : null;
                        $newModelBusinessHourDay->close_at = !empty($post['BusinessHour'][$dayName]['close_at']) ? $post['BusinessHour'][$dayName]['close_at'] : null;

                        if (!($flag = $newModelBusinessHourDay->save())) {

                            break;
                        } else {

                            array_push($dataBusinessHour, $newModelBusinessHourDay->toArray());
                        }
                    }

                    if ($flag && !empty($post['BusinessHourAdditionalDeleted'][$dayName])) {

                        $flag = BusinessHourAdditional::deleteAll(['id' => $post['BusinessHourAdditionalDeleted'][$dayName]]);
                    }

                    if ($flag && !empty($post['BusinessHourAdditional'][$dayName])) {

                        foreach ($post['BusinessHourAdditional'][$dayName] as $i => $businessHourAdditional) {

                            if (!empty($businessHourAdditional['open_at']) || !empty($businessHourAdditional['close_at'])) {

                                $newModelBusinessHourAdditional = BusinessHourAdditional::findOne(['unique_id' => $newModelBusinessHourDay->id . '-' . $day . '-' . $i]);

                                if (empty($newModelBusinessHourAdditional)) {

                                    $newModelBusinessHourAdditional = new BusinessHourAdditional();
                                    $newModelBusinessHourAdditional->unique_id = $newModelBusinessHourDay->id . '-' . $day . '-' . $i;
                                    $newModelBusinessHourAdditional->business_hour_id = $newModelBusinessHourDay->id;
                                    $newModelBusinessHourAdditional->day = $day;
                                }

                                $newModelBusinessHourAdditional->is_open = $newModelBusinessHourDay->is_open;
                                $newModelBusinessHourAdditional->open_at = !empty($businessHourAdditional['open_at']) ? $businessHourAdditional['open_at'] : null;
                                $newModelBusinessHourAdditional->close_at = !empty($businessHourAdditional['close_at']) ? $businessHourAdditional['close_at'] : null;

                                if (!($flag = $newModelBusinessHourAdditional->save())) {

                                    break;
                                } else {

                                    if (empty($dataBusinessHourAdditional[$dayName])) {

                                        $dataBusinessHourAdditional[$dayName] = [];
                                    }

                                    array_push($dataBusinessHourAdditional[$dayName], $newModelBusinessHourAdditional->toArray());
                                }
                            }
                        }
                    }
                }

                if ($flag) {

                    $model->businessDetail->note_business_hour = !empty($post['BusinessDetail']['note_business_hour']) ? $post['BusinessDetail']['note_business_hour'] : null;

                    $flag = $model->businessDetail->save();
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

        $dataBusinessHour = empty($dataBusinessHour) ? $model->businessHours : $dataBusinessHour;

        if (empty($dataBusinessHourAdditional)) {

            foreach ($dataBusinessHour as $businessHour) {

                $dayName = 'day' . $businessHour['day'];

                $dataBusinessHourAdditional[$dayName] = [];

                if (!empty($businessHour['businessHourAdditionals'])) {

                    foreach ($businessHour['businessHourAdditionals'] as $businessHourAdditional) {

                        array_push($dataBusinessHourAdditional[$dayName], $businessHourAdditional);
                    }
                }
            }
        }

        return $this->render('update_business_hour', [
            'model' => $model,
            'modelBusinessHour' => $modelBusinessHour,
            'dataBusinessHour' => $dataBusinessHour,
            'modelBusinessHourAdditional' => $modelBusinessHourAdditional,
            'dataBusinessHourAdditional' => $dataBusinessHourAdditional,
        ]);
    }

    public function actionUpgradeMembership($id, $save = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if (!empty($save)) {

                if ($model->save()) {

                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Success'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is success. Data has been saved'));
                } else {

                    $model->setIsNewRecord(true);

                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                    Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));
                }
            }
        }

        return $this->render('upgrade_membership', [
            'model' => $model,
        ]);
    }

    public function actionChooseBusinessUser($id)
    {
        $model = Business::find()
            ->joinWith([
                'businessContactPeople' => function ($query) {

                    $query->orderBy(['business_contact_person.created_at' => SORT_ASC]);
                },
                'businessContactPeople.person',
            ])
            ->andWhere(['business.id' => $id])
            ->asArray()->one();

        return $this->render('choose_business_user', [
            'model' => $model
        ]);
    }

    public function actionAddBusinessUser($id, $selected, $userSource, $save = null)
    {
        if (empty($selected)) {

            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', Yii::t('app', 'No User Selected'));
            Yii::$app->session->setFlash('message2', Yii::t('app', 'Please select the user that you want to add first'));

            return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business/choose-business-user', 'id' => $id]));
        }

        $modelBusiness = Business::find()
            ->joinWith([
                'businessContactPeople' => function ($query) use ($selected) {

                    $query->orderBy(['business_contact_person.created_at' => SORT_ASC])
                        ->andOnCondition(['business_contact_person.id' => explode(',', trim($selected, ','))]);
                },
                'businessContactPeople.person',
                'businessContactPeople.person.userPerson.user'
            ])
            ->andWhere(['business.id' => $id])
            ->one();

        $dataUser = [];
        $dataContactPerson = [];

        $modelUser = new User();
        $modelUserRole = new UserRole();
        $modelPerson = new Person();
        $modelBusinessContactPerson = new BusinessContactPerson();

        $userLevel = UserLevel::find()
            ->andWhere(['nama_level' => 'Business Owner'])
            ->asArray()->one();

        if ($userSource == 'Contact-Person') {

            if (!empty(($post = Yii::$app->request->post()))) {

                $modelUsers = [];

                foreach ($modelBusiness['businessContactPeople'] as $dataBusinessContactPerson) {

                    $modelUsers[] = empty($dataBusinessContactPerson->person->userPerson) ? new User() : $dataBusinessContactPerson->person->userPerson->user;
                }

                if (Model::loadMultiple($modelUsers, $post)) {

                    if (empty($save)) {

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return ActiveForm::validateMultiple($modelUsers);
                    } else {

                        $flag = false;
                        $transaction = Yii::$app->db->beginTransaction();

                        foreach ($modelBusiness['businessContactPeople'] as $i => $dataBusinessContactPerson) {

                            $modelUsers[$i]->setPassword(!empty($post['User'][$i]['password']) ? $post['User'][$i]['password'] : $modelUsers[$i]->password);

                            if (!($flag = $modelUsers[$i]->save())) {

                                break;
                            } else {

                                $modelUserRole->user_id = $modelUsers[$i]->id;
                                $modelUserRole->user_level_id = $userLevel['id'];
                                $modelUserRole->unique_id = $modelUsers[$i]->id . '-' . $userLevel['id'];
                                $modelUserRole->is_active = true;

                                if (!($flag = $modelUserRole->save())) {

                                    break;
                                } else {

                                    $modelUserAkses = UserAkses::find()
                                        ->andWhere(['user_level_id' => $modelUserRole->user_level_id])
                                        ->asArray()->all();

                                    foreach ($modelUserAkses as $dataUserAkses) {

                                        $modelUserAksesAppModule = new UserAksesAppModule();
                                        $modelUserAksesAppModule->unique_id = $modelUsers[$i]->id . '-' . $dataUserAkses['user_app_module_id'];
                                        $modelUserAksesAppModule->user_id = $modelUsers[$i]->id;
                                        $modelUserAksesAppModule->user_app_module_id = $dataUserAkses['user_app_module_id'];
                                        $modelUserAksesAppModule->is_active = $dataUserAkses['is_active'];
                                        $modelUserAksesAppModule->used_by_user_role = [$modelUserRole->unique_id];

                                        if (!($flag = $modelUserAksesAppModule->save())) {

                                            break;
                                        }
                                    }

                                    if ($flag) {

                                        $userFullName = explode(' ', $modelUsers[$i]->full_name);

                                        array_push($dataUser, $modelUsers[$i]->toArray());

                                        if (empty($dataBusinessContactPerson->person->userPerson)) {

                                            $newModelUserPerson = new UserPerson();
                                            $newModelUserPerson->user_id = $modelUsers[$i]->id;
                                            $newModelUserPerson->person_id = $dataBusinessContactPerson->person_id;

                                            if (!($flag = $newModelUserPerson->save())) {

                                                break;
                                            } else {

                                                $modelPerson = $newModelUserPerson->person;

                                                $modelPerson->first_name = $userFullName[0];
                                                $modelPerson->last_name = !empty($userFullName[1]) ? $userFullName[1] : null;
                                                $modelPerson->email = $modelUsers[$i]->email;

                                                if (!($flag = $modelPerson->save())) {

                                                    break;
                                                }
                                            }
                                        } else {

                                            if (!empty($post['is_merge'][$i])) {

                                                $dataBusinessContactPerson->person->email = $post['User'][$i]['email'];
                                                $dataBusinessContactPerson->person->first_name = $userFullName[0];
                                                $dataBusinessContactPerson->person->last_name = !empty($userFullName[1]) ? $userFullName[1] : null;

                                                if (!($flag = $dataBusinessContactPerson->person->save())) {

                                                    break;
                                                }
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

                            return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business/choose-business-user', 'id' => $id]));
                        } else {

                            Yii::$app->session->setFlash('status', 'danger');
                            Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                            Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));

                            $transaction->rollBack();
                        }
                    }
                }
            }

            if (empty($dataUser)) {

                foreach ($modelBusiness->businessContactPeople as $dataBusinessContactPerson) {

                    if (!empty($dataBusinessContactPerson->person->userPerson->user)) {

                        array_push($dataUser, ArrayHelper::merge($dataBusinessContactPerson->person->toArray(), $dataBusinessContactPerson->person->userPerson->user->toArray()));
                    } else {

                        array_push($dataUser, $dataBusinessContactPerson->person->toArray());
                    }
                }
            }
        } else if ($userSource == 'User-Asikmakan') {

            $model = User::find()
                ->joinWith([
                    'userPerson.person',
                    'userPerson.person.businessContactPeople' => function ($query) use ($id) {

                        $query->andOnCondition(['business_contact_person.business_id' => $id]);
                    }
                ])
                ->andWhere(['user.email' => explode(',', trim($selected, ','))])
                ->all();

            if (empty($model)) {

                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', Yii::t('app', 'User Not Found'));
                Yii::$app->session->setFlash('message2', Yii::t('app', 'No user found by that email'));

                return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business/choose-business-user', 'id' => $id]));
            }

            if (!empty(($post = Yii::$app->request->post()))) {

                if (!empty($save)) {

                    $flag = false;
                    $transaction = Yii::$app->db->beginTransaction();

                    foreach ($model as $i => $dataUser) {

                        $modelPerson = $dataUser->userPerson->person;

                        $modelPerson->first_name = $post['Person'][$i]['first_name'];
                        $modelPerson->last_name = $post['Person'][$i]['last_name'];
                        $modelPerson->phone = $post['Person'][$i]['phone'];
                        $modelPerson->email = $post['Person'][$i]['email'];

                        if (!($flag = $modelPerson->save())) {

                            break;
                        } else {

                            if (!empty($modelPerson->businessContactPeople)) {

                                $newModelBusinessContactPerson = $modelPerson->businessContactPeople[0];
                            } else {

                                $newModelBusinessContactPerson = new BusinessContactPerson();
                                $newModelBusinessContactPerson->business_id = $id;
                                $newModelBusinessContactPerson->person_id = $modelPerson->id;
                            }

                            $newModelBusinessContactPerson->is_primary_contact = $post['BusinessContactPerson'][$i]['is_primary_contact'];
                            $newModelBusinessContactPerson->note = $post['BusinessContactPerson'][$i]['note'];
                            $newModelBusinessContactPerson->position = $post['BusinessContactPerson'][$i]['position'];

                            if (!($flag = $newModelBusinessContactPerson->save())) {

                                break;
                            } else {

                                array_push($dataContactPerson, ArrayHelper::merge($modelPerson->toArray(), $newModelBusinessContactPerson->toArray()));

                                if (!empty($post['is_merge'][$i])) {

                                    $dataUser->email = $post['Person'][$i]['email'];
                                    $dataUser->full_name = $post['Person'][$i]['first_name'] . ' ' . $post['Person'][$i]['last_name'];

                                    if (!($flag = $dataUser->save())) {

                                        break;
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

                        return AjaxRequest::redirect($this, Yii::$app->urlManager->createUrl(['marketing/business/choose-business-user', 'id' => $id]));
                    } else {

                        Yii::$app->session->setFlash('status', 'danger');
                        Yii::$app->session->setFlash('message1', Yii::t('app', 'Update Data Is Fail'));
                        Yii::$app->session->setFlash('message2', Yii::t('app', 'Update data process is fail. Data fail to save'));

                        $transaction->rollBack();
                    }
                }
            }

            if (empty($dataContactPerson)) {

                foreach ($model as $dataUser) {

                    if (!empty($dataUser->userPerson->person->businessContactPeople[0])) {

                        array_push($dataContactPerson, ArrayHelper::merge($dataUser->userPerson->person->toArray(), $dataUser->userPerson->person->businessContactPeople[0]));
                    } else {

                        array_push($dataContactPerson, $dataUser->userPerson->person->toArray());
                    }
                }
            }
        }

        return $this->render('add_business_user', [
            'model' => !empty($model) ? $model : null,
            'modelBusiness' => $modelBusiness,
            'modelUser' => $modelUser,
            'dataUser' => $dataUser,
            'modelUserRole' => $modelUserRole,
            'modelPerson' => $modelPerson,
            'modelBusinessContactPerson' => $modelBusinessContactPerson,
            'dataContactPerson' => $dataContactPerson,
            'userLevel' => $userLevel,
            'selected' => $selected,
            'userSource' => $userSource
        ]);
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