<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\touchspin\TouchSpin;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\Category;
use core\models\ProductCategory;
use core\models\Facility;
use core\models\PaymentMethod;
use core\models\DeliveryMethod;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $dataRegistryBusinessCategory core\models\RegistryBusinessCategory */
/* @var $modelRegistryBusinessCategory core\models\RegistryBusinessCategory */
/* @var $dataRegistryBusinessProductCategoryParent core\models\RegistryBusinessProductCategory */
/* @var $dataRegistryBusinessProductCategoryChild core\models\RegistryBusinessProductCategory */
/* @var $modelRegistryBusinessProductCategory core\models\RegistryBusinessProductCategory */
/* @var $dataRegistryBusinessFacility core\models\RegistryBusinessFacility */
/* @var $modelRegistryBusinessFacility core\models\RegistryBusinessFacility */
/* @var $dataRegistryBusinessPayment core\models\RegistryBusinessPayment */
/* @var $modelRegistryBusinessPayment core\models\RegistryBusinessPayment */
/* @var $dataRegistryBusinessDelivery core\models\RegistryBusinessDelivery */
/* @var $modelRegistryBusinessDelivery core\models\RegistryBusinessDelivery */
/* @var $statusApproval backoffice\modules\marketing\controllers\RegistryBusinessController */
/* @var $day string */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryBusiness',
]);

$ajaxRequest->form();

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) {

    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();
}

$this->title = 'Update ' . Yii::t('app', 'Marketing Information') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Data Application'), 'url' => ['index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-' . strtolower($statusApproval), 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Marketing Information');

echo $ajaxRequest->component(); ?>

<div class="registry-business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="registry-business-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-business-form',
                        'action' => ['update-marketing-info', 'id' => $model->id, 'statusApproval' => strtolower($statusApproval)],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Marketing Information') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::label(Yii::t('app', 'Business Category'), null, ['class' => 'control-label']) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">

                                        <?php
                                        $selectedDataCategory = [];

                                        if (!empty($dataRegistryBusinessCategory)) {

                                            foreach ($dataRegistryBusinessCategory as $registryBusinessCategory) {

                                                if (!empty($registryBusinessCategory['category_id'])) {

                                                    $selectedDataCategory[$registryBusinessCategory['category_id']] = ['selected' => true];
                                                }
                                            }
                                        }

                                        echo $form->field($modelRegistryBusinessCategory, 'category_id')->dropDownList(
                                            ArrayHelper::map(
                                                Category::find()->orderBy('name')->asArray()->all(),
                                                'id',
                                                'name'
                                            ),
                                            [
                                                'multiple' => 'multiple',
                                                'prompt' => '',
                                                'style' => 'width: 100%',
                                                'options' => $selectedDataCategory
                                            ]) ?>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::label(Yii::t('app', 'Product Category'), null, ['class' => 'control-label']) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">

                                        <?php
                                        $selectedDataProductParent = [];

                                        if (!empty($dataRegistryBusinessProductCategoryParent)) {

                                            foreach ($dataRegistryBusinessProductCategoryParent as $registryBusinessProductCategoryParent) {

                                                $selectedDataProductParent[$registryBusinessProductCategoryParent['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelRegistryBusinessProductCategory, 'product_category_id[parent]')->dropDownList(
                                            ArrayHelper::map(
                                                ProductCategory::find()->andWhere(['is_parent' => true])->orderBy('name')->asArray()->all(),
                                                'id',
                                                'name'
                                            ),
                                            [
                                                'multiple' => 'multiple',
                                                'prompt' => '',
                                                'style' => 'width: 100%',
                                                'options' => $selectedDataProductParent
                                            ]) ?>

                                    </div>
                                    <div class="col-md-12">

                                        <?php
                                        $selectedDataProductChild = [];

                                        if (!empty($dataRegistryBusinessProductCategoryParent)) {

                                            foreach ($dataRegistryBusinessProductCategoryChild as $registryBusinessProductCategoryChild) {

                                                $selectedDataProductChild[$registryBusinessProductCategoryChild['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelRegistryBusinessProductCategory, 'product_category_id[child]')->dropDownList(
                                            ArrayHelper::map(
                                                ProductCategory::find()->andWhere(['is_parent' => false])->orderBy('name')->asArray()->all(),
                                                'id',
                                                'name'
                                            ),
                                            [
                                                'multiple' => 'multiple',
                                                'prompt' => '',
                                                'style' => 'width: 100%',
                                                'options' => $selectedDataProductChild
                                            ]) ?>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::label(Yii::t('app', 'Facility'), null, ['class' => 'control-label']) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">

                                        <?php
                                        $selectedDataFacility = [];

                                        if (!empty($dataRegistryBusinessFacility)) {

                                            foreach ($dataRegistryBusinessFacility as $registryBusinessFacility) {

                                                if (!empty($registryBusinessFacility['facility_id'])) {

                                                    $selectedDataFacility[$registryBusinessFacility['facility_id']] = ['selected' => true];
                                                }
                                            }
                                        }

                                        echo $form->field($modelRegistryBusinessFacility, 'facility_id')->dropDownList(
                                            ArrayHelper::map(
                                                Facility::find()->orderBy('name')->asArray()->all(),
                                                'id',
                                                'name'
                                            ),
                                            [
                                                'multiple' => 'multiple',
                                                'prompt' => '',
                                                'style' => 'width: 100%',
                                                'options' => $selectedDataFacility
                                            ]) ?>

                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label"><?= Yii::t('app', 'Price Range') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-5">

                                    <?= $form->field($model, 'price_min')->widget(TouchSpin::className(), [
                                        'options' => [
                                            'placeholder' => Yii::t('app', 'Price Min'),
                                        ],
                                        'pluginOptions' => [
                                            'min' => 0,
                                            'max' => 1000000,
                                            'step' => 10000,
                                            'prefix' => 'Rp',
                                            'verticalbuttons' => true,
                                            'verticalup' => '<i class="glyphicon glyphicon-plus"></i>',
                                            'verticaldown' => '<i class="glyphicon glyphicon-minus"></i>'
                                        ],
                                    ]); ?>

                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-center">
                                    -
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-5">

                                    <?= $form->field($model, 'price_max')->widget(TouchSpin::className(), [
                                        'options' => [
                                            'placeholder' => Yii::t('app', 'Price Max'),
                                        ],
                                        'pluginOptions' => [
                                            'min' => 0,
                                            'max' => 1000000,
                                            'step' => 10000,
                                            'prefix' => 'Rp',
                                            'verticalbuttons' => true,
                                            'verticalup' => '<i class="glyphicon glyphicon-plus"></i>',
                                            'verticaldown' => '<i class="glyphicon glyphicon-minus"></i>'
                                        ],
                                    ]); ?>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12">

                                        <?php
                                        echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                        echo Html::a('<i class="fa fa-times"></i> Cancel', ['view-' . strtolower($statusApproval), 'id' => $model->id], ['class' => 'btn btn-default']); ?>

                                    </div>
                                </div>
                            </div>

                        </div>

                    <?php
                    ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$cssscript = '
    .select2-grid-system ul.select2-results__options > li.select2-results__option {
        float: left;
        width: 50%;
    }

    @media (min-width: 768px) {
        .select2-grid-system ul.select2-results__options > li.select2-results__option {
            float: left;
            width: 33.33333333%;
        }
    }

    @media (min-width: 1200px) {
        .select2-grid-system ul.select2-results__options > li.select2-results__option {
            float: left;
            width: 20%;
        }
    }
';

$this->registerCss($cssscript);

$jscript = '
    $("#registrybusinesscategory-category_id").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Business Category') . '",
    });

    $("#registrybusinessproductcategory-product_category_id-parent").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Product Category General') . '"
    });

    $("#registrybusinessproductcategory-product_category_id-child").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Product Category Specific') . '"
    });

    $("#registrybusinessfacility-facility_id").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Facility') . '"
    });

    $("#registrybusinesspayment-payment_method_id").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Payment Methods') . '"
    });

    $("#registrybusinessdelivery-delivery_method_id").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Delivery Methods') . '"
    });
';

$this->registerJs($jscript); ?>