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

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $modelRegistryBusinessCategory core\models\RegistryBusinessCategory */
/* @var $dataRegistryBusinessCategory array */
/* @var $modelRegistryBusinessProductCategory core\models\RegistryBusinessProductCategory */
/* @var $dataRegistryBusinessProductCategoryParent array */
/* @var $dataRegistryBusinessProductCategoryChild array */
/* @var $modelRegistryBusinessFacility core\models\RegistryBusinessFacility */
/* @var $dataRegistryBusinessFacility array */
/* @var $statusApproval string */
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Business Category')) ?>
                                    </div>
                                    <div class="col-xs-12">
    
                                        <?php
                                        $selectedDataCategory = [];
    
                                        if (!empty($dataRegistryBusinessCategory)) {
    
                                            foreach ($dataRegistryBusinessCategory as $registryBusinessCategory) {
    
                                                $selectedDataCategory[$registryBusinessCategory['category_id']] = ['selected' => true];
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Product Category')) ?>
                                    </div>
                                    <div class="col-xs-12">

                                        <?php
                                        $selectedDataProductParent = [];

                                        if (!empty($dataRegistryBusinessProductCategoryParent)) {

                                            foreach ($dataRegistryBusinessProductCategoryParent as $registryBusinessProductCategoryParent) {

                                                $selectedDataProductParent[$registryBusinessProductCategoryParent['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelRegistryBusinessProductCategory, 'product_category_id[parent]')->dropDownList(
                                            ArrayHelper::map(
                                                ProductCategory::find()
                                                    ->andWhere(['type' => 'General'])
                                                    ->andWhere(['is_active' => true])
                                                    ->orderBy('name')->asArray()->all(),
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
                                    <div class="col-xs-12">

                                        <?php
                                        $selectedDataProductChild = [];

                                        if (!empty($dataRegistryBusinessProductCategoryParent)) {

                                            foreach ($dataRegistryBusinessProductCategoryChild as $registryBusinessProductCategoryChild) {

                                                $selectedDataProductChild[$registryBusinessProductCategoryChild['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelRegistryBusinessProductCategory, 'product_category_id[child]')->dropDownList(
                                            ArrayHelper::map(
                                                ProductCategory::find()
                                                    ->andWhere(['OR', ['type' => 'Specific'], ['type' => 'Specific-Menu']])
                                                    ->andWhere(['is_active' => true])
                                                    ->orderBy('name')->asArray()->all(),
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Facility')) ?>
                                    </div>
                                    <div class="col-xs-12">

                                        <?php
                                        $selectedDataFacility = [];

                                        if (!empty($dataRegistryBusinessFacility)) {

                                            foreach ($dataRegistryBusinessFacility as $registryBusinessFacility) {

                                                $selectedDataFacility[$registryBusinessFacility['facility_id']] = ['selected' => true];
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
                                    <div class="col-xs-12">
                                        <?= Html::label(Yii::t('app', 'Average Spending')) ?>
                                    </div>
                                    <div class="col-xs-5 col-sm-4 col-lg-3">

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
                                    <div class="col-xs-1 text-center">
                                        -
                                    </div>
                                    <div class="col-xs-5 col-sm-4 col-lg-3">
    
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
                            </div>

                            <div class="row">
                                <div class="col-xs-12">

                                    <?php
                                    echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['view-' . strtolower($statusApproval), 'id' => $model->id], ['class' => 'btn btn-default']); ?>

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
';

$this->registerJs($jscript); ?>