<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\touchspin\TouchSpin;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\Category;
use core\models\Facility;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $modelBusinessCategory core\models\BusinessCategory */
/* @var $dataBusinessCategory core\models\BusinessCategory */
/* @var $modelBusinessProductCategory core\models\BusinessProductCategory */
/* @var $dataBusinessProductCategoryParent core\models\BusinessProductCategory */
/* @var $dataBusinessProductCategoryChild core\models\BusinessProductCategory */
/* @var $modelBusinessFacility core\models\BusinessFacility */
/* @var $dataBusinessFacility core\models\BusinessFacility */
/* @var $modelBusinessDetail core\models\BusinessDetail */
/* @var $dataProductCategoryParent array */
/* @var $dataProductCategoryChild array */
/* @var $day string */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'Business',
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['member']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-member', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Marketing Information');

echo $ajaxRequest->component(); ?>

<div class="business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="business-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'business-form',
                        'action' => ['update-marketing-info', 'id' => $model->id],
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Business Category')) ?>
                                    </div>
                                    <div class="col-xs-12">

                                        <?php
                                        $selectedDataCategory = [];

                                        if (!empty($dataBusinessCategory)) {

                                            foreach ($dataBusinessCategory as $businessCategory) {

                                                $selectedDataCategory[$businessCategory['category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelBusinessCategory, 'category_id')->dropDownList(
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Product Category')) ?>
                                    </div>
                                    <div class="col-xs-12">

                                        <?php
                                        $selectedDataProductParent = [];

                                        if (!empty($dataBusinessProductCategoryParent)) {

                                            foreach ($dataBusinessProductCategoryParent as $businessProductCategoryParent) {

                                                $selectedDataProductParent[$businessProductCategoryParent['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelBusinessProductCategory, 'product_category_id[parent]')
                                            ->dropDownList($dataProductCategoryParent, [
                                                'multiple' => 'multiple',
                                                'prompt' => '',
                                                'style' => 'width: 100%',
                                                'options' => $selectedDataProductParent
                                            ]) ?>

                                    </div>
                                    <div class="col-xs-12">

                                        <?php
                                        $selectedDataProductChild = [];

                                        if (!empty($dataBusinessProductCategoryChild)) {

                                            foreach ($dataBusinessProductCategoryChild as $businessProductCategoryChild) {

                                                $selectedDataProductChild[$businessProductCategoryChild['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelBusinessProductCategory, 'product_category_id[child]')
                                            ->dropDownList($dataProductCategoryChild, [
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Facility')) ?>
                                    </div>
                                    <div class="col-xs-12">

                                        <?php
                                        $selectedDataFacility = [];

                                        if (!empty($dataBusinessFacility)) {

                                            foreach ($dataBusinessFacility as $businessFacility) {

                                                $selectedDataFacility[$businessFacility['facility_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelBusinessFacility, 'facility_id')->dropDownList(
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Average Spending')) ?>
                                    </div>
                                    <div class="col-xs-5 col-sm-4 col-lg-3">
    
                                        <?= $form->field($modelBusinessDetail, 'price_min')->widget(TouchSpin::className(), [
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
                                    <div class="col-xs-1 text-center">
                                        -
                                    </div>
                                    <div class="col-xs-5 col-sm-4 col-lg-3">
    
                                        <?= $form->field($modelBusinessDetail, 'price_max')->widget(TouchSpin::className(), [
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
                            </div>
                            
                            <div class="row">
                                <div class="col-xs-12">

                                    <?php
                                    echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['view-member', 'id' => $model->id], ['class' => 'btn btn-default']); ?>

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
$jscript = '
    $("#businesscategory-category_id").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Business Category') . '",
    });

    $("#businessproductcategory-product_category_id-parent").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Product Category General') . '"
    });

    $("#businessproductcategory-product_category_id-child").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Product Category Specific') . '"
    });

    $("#businessfacility-facility_id").select2({
        theme: "krajee",
        dropdownCssClass: "select2-grid-system",
        placeholder: "' . Yii::t('app', 'Facility') . '"
    });
';

$this->registerJs($jscript); ?>