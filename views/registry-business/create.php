<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\file\FileInput;
use kartik\touchspin\TouchSpin;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\MembershipType;
use core\models\City;
use core\models\Category;
use core\models\ProductCategory;
use core\models\Facility;
use core\models\Person;
use core\models\RegistryBusinessContactPerson;
use core\models\RegistryBusinessHourAdditional;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $dataRegistryBusinessCategory core\models\RegistryBusinessCategory */
/* @var $modelRegistryBusinessCategory core\models\RegistryBusinessCategory */
/* @var $dataRegistryBusinessProductCategoryParent core\models\RegistryBusinessProductCategory */
/* @var $dataRegistryBusinessProductCategoryChild core\models\RegistryBusinessProductCategory */
/* @var $modelRegistryBusinessProductCategory core\models\RegistryBusinessProductCategory */
/* @var $dataRegistryBusinessFacility core\models\RegistryBusinessFacility */
/* @var $modelRegistryBusinessFacility core\models\RegistryBusinessFacility */
/* @var $dataRegistryBusinessHour core\models\RegistryBusinessHour */
/* @var $modelRegistryBusinessHour core\models\RegistryBusinessHour */
/* @var $dataRegistryBusinessHourAdditional core\models\RegistryBusinessHourAdditional */
/* @var $modelRegistryBusinessHourAdditional core\models\RegistryBusinessHourAdditional */
/* @var $dataRegistryBusinessImage core\models\RegistryBusinessImage */
/* @var $modelRegistryBusinessImage core\models\RegistryBusinessImage */
/* @var $modelPerson core\models\Person */
/* @var $modelRegistryBusinessContactPerson core\models\RegistryBusinessContactPerson */
/* @var $dataRegistryBusinessContactPerson core\models\RegistryBusinessContactPerson */
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

$this->title = Yii::t('app', 'Create Application');
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component();

$jscript = '
    $("#wizard-create-application").steps({
        titleTemplate:
            "<span class=\"number\">" +
                "#index#" +
            "</span>" +
            "<span class=\"desc\">" +
                "#title#" +
            "</span>",
        onInit: function(event, currentIndex) {
    
            $("#wizard-create-application.wizard > .actions ul li a").addClass("btn btn-primary");
            $("#wizard-create-application.wizard > .actions").removeClass("actions").addClass("actionBar");
            $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#previous\"]").addClass("buttonDisabled");
        },
        onStepChanged: function(event, currentIndex, priorIndex) {
    
            if (priorIndex == 0) {
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#previous\"]").removeClass("buttonDisabled");
            } else if (currentIndex == 0) {
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#previous\"]").addClass("buttonDisabled");
            }
    
            var lastCount = $("#wizard-create-application.wizard > .steps").find("li").length - 1;
    
            if (currentIndex == lastCount) {
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").addClass("buttonDisabled");
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").parent().hide();
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#finish\"]").parent().show();
            } else if (priorIndex == lastCount) {
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").removeClass("buttonDisabled");
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").parent().show();
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#finish\"]").parent().hide();
            }
        },
        onFinished: function(event, currentIndex) {
    
            $("#registry-business-form").trigger("submit");
        },
        labels: {
            finish: "<i class=\"fa fa-save\"></i> Save",
            next: "<i class=\"fa fa-angle-double-right\"></i> Next",
            previous: "<i class=\"fa fa-angle-double-left\"></i> Previous"
        }
    });
';

$this->registerJs($jscript); ?>

<div class="registry-business-create">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="registry-business-form">
                    <div class="x_title">

                    </div>
                    <div class="x_content">

                        <?php
                        $form = ActiveForm::begin([
                            'id' => 'registry-business-form',
                            'action' => ['create'],
                            'options' => [

                            ],
                            'fieldConfig' => [
                                'template' => '{input}{error}',
                            ]
                        ]); ?>

                            <div id="wizard-create-application">
                                <h1><?= Yii::t('app', 'Membership Type') ?></h1>
                                <div>

                                    <?= $form->field($model, 'membership_type_id', [
                                        'template' => '
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    {input}
                                                    {error}
                                                </div>
                                            </div>
                                        ',
                                    ])->radioList(
                                        ArrayHelper::map(
                                            MembershipType::find()->andWhere(['as_archive' => false])->andWhere(['is_premium' => false])->orderBy('order')->asArray()->all(),
                                            'id',
                                            function($data) {
                                                
                                                return $data['name'];
                                            }
                                        ), [
                                        'item' => function ($index, $label, $name, $checked, $value) {

                                            return '
                                                <div class="col-xs-12 col-sm-4">
                                                    <label>' .
                                                        Html::radio($name, $checked, ['value' => $value]) . ' ' . $label . '
                                                    </label>
                                                </div>';
                                        }
                                    ]) ?>

                                </div>

                                <h1><?= Yii::t('app', 'Business Information') ?></h1>
                                <div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">

                                            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Name')]) ?>

                                        </div>
                                        <div class="col-xs-12 col-sm-6">

                                            <?= $form->field($model, 'unique_name', [
                                                'enableAjaxValidation' => true
                                            ])->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Unique Name')]) ?>

                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-xs-12 col-md-3">

                                            <?= $form->field($model, 'address_type')->dropDownList(['Gang' => 'Gang', 'Jalan' => 'Jalan', 'Komplek' => 'Komplek'], [
                                                'prompt' => Yii::t('app', 'Address Type'),
                                                'style' => 'width: 100%'
                                            ]) ?>

                                        </div>
                                        <div class="col-xs-12 col-md-5">

                                            <?= $form->field($model, 'address')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Address')]) ?>

                                        </div>
                                        <div class="col-xs-12 col-md-4">

                                            <?= $form->field($model, 'address_info')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Address Info')]) ?>

                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-3">

                                            <?= $form->field($model, 'city_id')->dropDownList(
                                                ArrayHelper::map(
                                                    City::find()->orderBy('name')->asArray()->all(),
                                                    'id',
                                                    function($data) {
                                                        
                                                        return $data['name'];
                                                    }
                                                ),
                                                [
                                                    'prompt' => '',
                                                    'style' => 'width: 100%'
                                                ]) ?>

                                        </div>
                                        <div class="col-xs-6 col-sm-3">

                                            <?= $form->field($model, 'district_id')->textInput([
                                                'style' => 'width: 100%'
                                            ]) ?>

                                        </div>
                                        <div class="col-xs-6 col-sm-3">

                                            <?= $form->field($model, 'village_id')->textInput([
                                                'style' => 'width: 100%'
                                            ]) ?>

                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-xs-8 col-sm-6">

                                            <?= $form->field($model, 'coordinate')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Coordinate')]) ?>

                                        </div>
                                        <div class="col-xs-4 col-sm-3">

                                            <?= Html::a('<i class="fa fa-map-marker-alt"></i> ' . Yii::t('app', 'Open Map'), '', ['class' => 'btn btn-primary btn-block open-map']) ?>

                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-3">

                                            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Email')]) ?>

                                        </div>
                                        <div class="col-xs-6 col-sm-3">

                                            <?= $form->field($model, 'phone1')->widget(MaskedInput::className(), [
                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                'options' => [
                                                    'placeholder' => Yii::t('app', 'Phone1'),
                                                    'class' => 'form-control'
                                                ]
                                            ]) ?>

                                        </div>
                                        <div class="col-xs-6 col-sm-3">

                                            <?= $form->field($model, 'phone2')->widget(MaskedInput::className(), [
                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                'options' => [
                                                    'placeholder' => Yii::t('app', 'Phone2'),
                                                    'class' => 'form-control'
                                                ]
                                            ]) ?>

                                        </div>
                                        <div class="col-xs-6 col-sm-3">

                                            <?= $form->field($model, 'phone3')->widget(MaskedInput::className(), [
                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                'options' => [
                                                    'placeholder' => Yii::t('app', 'Phone3'),
                                                    'class' => 'form-control'
                                                ]
                                            ]) ?>

                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-xs-12">

                                            <?= $form->field($model, 'about', [
                                                'template' => '{label}{input}{error}',
                                            ])->widget(CKEditor::className(), [
                                                'options' => ['rows' => 6],
                                            ]) ?>

                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-9">

                                            <?= $form->field($model, 'note')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Note')]) ?>

                                        </div>
                                    </div>
                                </div>

                                <h1><?= Yii::t('app', 'Marketing Information') ?></h1>
                                <div>
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
                                            <div class="col-xs-12">

                                                <?php
                                                $selectedDataProductChild = [];

                                                if (!empty($dataRegistryBusinessProductCategoryChild)) {

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
                                        <div class="row mb-10">
                                            <div class="col-xs-12">

                                                <?= Html::label(Yii::t('app', 'Business Hour')) ?>
                                                <?= Html::button(Yii::t('app', 'Set All'), ['class' => 'btn btn-primary btn-xs set-all-business-hour']) ?>

                                            </div>
                                        </div>

                                        <?php
                                        $days = Yii::$app->params['days'];
                                        $hours = Yii::$app->params['hours'];

                                        foreach ($days as $i => $day):
                                        
                                            $i++;
                                            $is24Hour = false;
                                            $dayName = 'day' . $i;

                                            foreach ($dataRegistryBusinessHour as $registryBusinessHour) {

                                                if ($registryBusinessHour['day'] == $i) {

                                                    $modelRegistryBusinessHour->is_open = $registryBusinessHour['is_open'];
                                                    $modelRegistryBusinessHour->open_at = $registryBusinessHour['open_at'];
                                                    $modelRegistryBusinessHour->close_at = $registryBusinessHour['close_at'];

                                                    if ($modelRegistryBusinessHour->open_at == '00:00:00' && $modelRegistryBusinessHour->close_at == '24:00:00') {

                                                        $is24Hour = true;
                                                    }
                                                    
                                                    break;
                                                }
                                            } ?>
                                                
											<div class="main-hour-form">
                                                <div class="row">
                                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
    
                                                        <?= Yii::t('app', $day) ?>
    
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
    
                                                        <?= $form->field($modelRegistryBusinessHour, '[' . $dayName . ']is_open')
                                                            ->checkbox([
                                                                'label' => Yii::t('app', 'Open'),
                                                                'class' => 'business-hour-is-open day-' . $i,
                                                            ]); ?>
    
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                        <div class="form-group">
    
                                                            <?= Html::checkbox('always24', $is24Hour, [
                                                                'label' => Yii::t('app', '24 Hours'),
                                                                'class' => 'business-hour-24h',
                                                                'disabled' => !$modelRegistryBusinessHour->is_open,
                                                                'id' => 'business-hour-24h-' . $i
                                                            ]); ?>
    
                                                        </div>
                                                    </div>
    
                                                    <div class="visible-xs clearfix"></div>
    
                                                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
    
                                                        <?= $form->field($modelRegistryBusinessHour, '[' . $dayName . ']open_at')
                                                            ->dropDownList($hours, [
                                                                'prompt' => '',
                                                                'class' => 'business-hour-time open',
                                                                'style' => 'width: 100%',
                                                                'disabled' => !$modelRegistryBusinessHour->is_open,
                                                            ]); ?>
    
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
    
                                                        <?= $form->field($modelRegistryBusinessHour, '[' . $dayName . ']close_at')
                                                            ->dropDownList($hours, [
                                                                'prompt' => '',
                                                                'class' => 'business-hour-time close',
                                                                'style' => 'width: 100%',
                                                                'disabled' => !$modelRegistryBusinessHour->is_open,
                                                            ]); ?>
    
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-sm-3 col-xs-4">
                                                    	
                                                    	<?= Html::hiddenInput('day', $i, ['class' => 'days-count']) ?>
                                                    	
                                                        <?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-default', 'id' =>'add-business-hour-' . $dayName]) ?>
                                                        <?= Html::button('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Delete'), ['class' => 'btn btn-default', 'id' => 'delete-business-hour-' . $dayName]) ?>
                                                        
                                                    </div>
                                                </div>
                                                
                                                <?php
                                                if (!empty($dataRegistryBusinessHourAdditional[$dayName])):
                                                    
                                                    $countAdditional = 0;
                                                    
                                                    foreach ($dataRegistryBusinessHourAdditional[$dayName] as $registryBusinessHourAdditional):
                                                            
                                                        $countAdditional++;
                                                        $modelRegistryBusinessHourAdditional->open_at = $registryBusinessHourAdditional['open_at'];
                                                        $modelRegistryBusinessHourAdditional->close_at = $registryBusinessHourAdditional['close_at']; ?>
                                                    
                                                        <div class="data-hour-form">
                                                            <div class="row">
                                                                <div class="col-lg-2 col-lg-offset-5 col-md-3 col-sm-3 col-xs-4">
                                                    
                                                                    <?= $form->field($modelRegistryBusinessHourAdditional, '[' . $dayName . '][' . $countAdditional . ']open_at')
                                                                        ->dropDownList($hours, [
                                                                            'prompt' => '',
                                                                            'class' => 'business-hour-time-additional open-additional',
                                                                            'style' => 'width: 100%',
                                                                            'disabled' => !$modelRegistryBusinessHour->is_open,
                                                                        ]); ?>
                                                    
                                                                </div>
                                                                <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                                                    
                                                                    <?= $form->field($modelRegistryBusinessHourAdditional, '[' . $dayName . '][' . $countAdditional . ']close_at')
                                                                        ->dropDownList($hours, [
                                                                            'prompt' => '',
                                                                            'class' => 'business-hour-time-additional close-additional',
                                                                            'style' => 'width: 100%',
                                                                            'disabled' => !$modelRegistryBusinessHour->is_open,
                                                                        ]); ?>
                                                    
                                                                </div>
                                                            </div>
                                                            
                                                            <?= Html::hiddenInput('HourCounter', $countAdditional, ['class' => 'counter-hour']) ?>
                                                            
                                                        </div>
                                                
                                    				<?php
                                                    endforeach;
                                                endif; ?>
                                            
                                            </div>
                                            
                                        <?php
                                        endforeach; ?>

                                        <div class="row">
                                            <div class="col-xs-12 col-sm-9">
                                                <?= $form->field($model, 'note_business_hour')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Note')]) ?>
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

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <?= Html::label(Yii::t('app', 'Foto')) ?>
                                            </div>
                                            <div class="col-sm-10">

                                                <?= $form->field($modelRegistryBusinessImage, 'image[]')->widget(FileInput::classname(), [
                                                    'options' => [
                                                        'accept' => 'image/*',
                                                        'multiple' => true,
                                                    ],
                                                    'pluginOptions' => [
                                                        'showRemove' => true,
                                                        'showUpload' => false,
                                                    ]
                                                ]); ?>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <h1><?= Yii::t('app', 'Contact Person') ?></h1>
                                <div>
                                	<div class="main-form">
                                	
                                    	<?php
                            	        if (!empty($dataRegistryBusinessContactPerson)):
                            	            
                            	            foreach ($dataRegistryBusinessContactPerson as $i => $contactPerson):
                            	                
                            	                $modelPerson->first_name = $contactPerson['first_name'];
                            	                $modelPerson->last_name = $contactPerson['last_name'];
                            	                $modelPerson->phone = $contactPerson['phone'];
                            	                $modelPerson->email = $contactPerson['email'];
                            	                $modelRegistryBusinessContactPerson->is_primary_contact = $contactPerson['is_primary_contact'];
                            	                $modelRegistryBusinessContactPerson->note = $contactPerson['note'];
                            	                $modelRegistryBusinessContactPerson->position = $contactPerson['position']; ?>
                            	                
                            	                <div class="mb-40 data-form">
                                                    <div class="row mt-10">
                                                        <div class="col-md-4 col-xs-6">
                                                        
                                                            <?= $form->field($modelPerson, '[' . $i .']first_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'First Name')]) ?>
                                                            
                                                        </div>
                                            
                                                        <div class="col-md-4 col-xs-6">
                                                        
                                                            <?= $form->field($modelPerson, '[' . $i .']last_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Last Name')]) ?>
                                                            
                                                        </div>
                                            
                                                        <div class="col-md-4 col-xs-12">
                                                        
                                                        	<?= $form->field($modelRegistryBusinessContactPerson, '[' . $i .']position')
                                                        	    ->dropDownList([
                                                        	       'Owner' => 'Owner', 
                                                        	       'Manager' => 'Manager', 
                                                        	       'Staff' => 'Staff'   
                                                        	    ], 
                                                    	        [
                                                    	           'prompt' => Yii::t('app', 'Position'),
                                                	               'class' => 'contact-person-position',
                                                    	            'style' => 'width: 100%'
                                                    	        ]); ?>
                                                        	
                                                        </div>
                                                    </div>
                                            
                                                    <div class="row">
                                                        <div class="col-md-4 col-xs-6">
                                                        
                                                            <?= $form->field($modelPerson, '[' . $i .']phone')->widget(MaskedInput::className(), [
                                                                'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                                'options' => [
                                                                    'class' => 'form-control',
                                                                    'placeholder' => Yii::t('app', 'Phone'),
                                                                ],
                                                            ]) ?>
                                                            
                                                        </div>
                                            
                                                        <div class="col-md-4 col-xs-6">
                                                        
                                                            <?= $form->field($modelPerson, '[' . $i .']email')->textInput([
                                                                'class' => 'form-control',
                                                                'placeholder' => 'Email',
                                                            ]) ?>
                                                            
                                                        </div>
                                            
                                                        <div class="col-md-4 col-xs-6">
                                                        
                                                        	<?= $form->field($modelRegistryBusinessContactPerson, '[' . $i .']is_primary_contact')->checkbox() ?>
                                                        	
                                                        </div>
                                                    </div>
                                            
                                                    <div class="row">
                                                    	<div class="col-md-8 col-xs-12">
                                                    	
                                                            <?= $form->field($modelRegistryBusinessContactPerson, '[' . $i .']note')->textarea(['rows' => 2, 'placeholder' => Yii::t('app', 'Note')]) ?>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                        	<?php
                            	            endforeach;
                            	        endif; ?>
                            	        
                        	        </div>
                                	
                                    <div class="row">
                                        <div class="col-md-12">
                                        
                                            <?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-default add-contact-person']) ?>
                                            <?= Html::button('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Delete'), ['class' => 'btn btn-default delete-contact-person']); ?>
                                            
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
</div>

<?php
$modelRegistryBusinessContactPerson = new RegistryBusinessContactPerson();
$modelPerson = new Person(); ?>

<div class="temp-form hide">
    <div class="mb-40 data-form">
        <div class="row mt-10">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[index]first_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'First Name')]) ?>
            </div>

            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[index]last_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Last Name')]) ?>
            </div>

            <div class="col-md-4 col-xs-12">
            
            	<?= $form->field($modelRegistryBusinessContactPerson, '[index]position')
            	    ->dropDownList([
            	       'Owner' => 'Owner', 
            	       'Manager' => 'Manager', 
            	       'Staff' => 'Staff'
            	    ], 
        	        [ 
    	               'prompt' => Yii::t('app', 'Position'),
        	           'class' => 'contact-person-position',
        	           'style' => 'width: 100%',
        	        ]); ?>
            	
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-xs-6">
            
                <?= $form->field($modelPerson, '[index]phone')->widget(MaskedInput::className(), [
                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => Yii::t('app', 'Phone'),
                    ],
                ]) ?>
                
            </div>

            <div class="col-md-4 col-xs-6">
            
                <?= $form->field($modelPerson, '[index]email')->textInput([
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ]) ?>
                
            </div>

            <div class="col-md-4 col-xs-6">
            	<?= $form->field($modelRegistryBusinessContactPerson, '[index]is_primary_contact')->checkbox(['class' => 'not-checkbox']) ?>
            </div>
        </div>

        <div class="row">
        	<div class="col-md-8 col-xs-12">
                <?= $form->field($modelRegistryBusinessContactPerson, '[index]note')->textarea(['rows' => 2, 'placeholder' => Yii::t('app', 'Note')]) ?>
            </div>
        </div>
    </div>
</div>

<?php
$modelRegistryBusinessHourAdditional = new RegistryBusinessHourAdditional(); ?>

<div class="additional-hour-temp-form hide">
    <div class="data-hour-form">
        <div class="row">
        	<div class="col-lg-2 col-lg-offset-5 col-md-3 col-sm-3 col-xs-4">

                <?= $form->field($modelRegistryBusinessHourAdditional, '[dayidx][index]open_at')
                    ->dropDownList($hours, [
                        'prompt' => '',
                        'class' => 'business-hour-time-additional open-additional',
                        'style' => 'width: 100%',
                        'disabled' => 'disabled',
                    ]); ?>

            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">

                <?= $form->field($modelRegistryBusinessHourAdditional, '[dayidx][index]close_at')
                    ->dropDownList($hours, [
                        'prompt' => '',
                        'class' => 'business-hour-time-additional close-additional',
                        'style' => 'width: 100%',
                        'disabled' => 'disabled',
                    ]); ?>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerCssFile(Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/demo/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile(Yii::$app->urlManager->baseUrl . '/media/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$cssscript = '
    .wizard > .content > .body ul > li {
        display: block;
    }

    .select2-container--krajee .select2-selection--multiple .select2-selection__choice__remove {
        margin-top: 1px;
    }

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

$this->registerJsFile(Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/build/jquery.steps.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var indexCount = ' . count($dataRegistryBusinessContactPerson) . ';

    $("#registrybusiness-address_type").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Address Type') . '",
        minimumResultsForSearch: Infinity
    });

    $("#registrybusiness-city_id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City ID') . '"
    });

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

    $(".business-hour-time.open").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Open') . '"
    });

    $(".business-hour-time.close").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Close') . '"
    });

    $(".counter-hour").parent().find(".business-hour-time-additional.open-additional").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Open') . '"
    });

    $(".counter-hour").parent().find(".business-hour-time-additional.close-additional").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Close') . '"
    });

    $(".main-form").find(".contact-person-position").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Position') . '",
        minimumResultsForSearch: "Infinity"
    });

    function district(executeRemote, afterSuccess) {

        function setDistrict(remoteData) {

            $("#registrybusiness-district_id").val(null).trigger("change");

            $("#registrybusiness-district_id").select2({
                theme: "krajee",
                placeholder: "' . Yii::t('app', 'District ID') . '",
                data: remoteData,
            });
        };

        if (executeRemote) {

            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl(['masterdata/district/get-district-by-city']) . '?id=" + $("#registrybusiness-city_id").select2("data")[0].id,
                success: function(response) {

                    setDistrict(response);

                    if (afterSuccess !== undefined) {

                        afterSuccess();
                    }
                }
            });
        } else {

            setDistrict([]);

            if (afterSuccess !== undefined) {

                afterSuccess();
            }
        }
    };

    function village(executeRemote, afterSuccess) {

        function setVillage(remoteData) {

            $("#registrybusiness-village_id").val(null).trigger("change");

            $("#registrybusiness-village_id").select2({
                theme: "krajee",
                placeholder: "' . Yii::t('app', 'Village ID') . '",
                data: remoteData,
            });
        };

        if (executeRemote) {

            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl(['masterdata/village/get-village-by-district']) . '?id=" + $("#registrybusiness-district_id").select2("data")[0].id,
                success: function(response) {

                    setVillage(response);

                    if (afterSuccess !== undefined) {

                        afterSuccess();
                    }
                }
            });
        } else {

            setVillage([]);

            if (afterSuccess !== undefined) {

                afterSuccess();
            }
        }
    };

    function addValidator(index) {

        $("#registry-business-form").yiiActiveForm("add", {
            "id":"person-" + index + "-first_name",
            "name":"[" + index + "]first_name",
            "container":".field-person-" + index + "-first_name",
            "input":"#person-" + index + "-first_name",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {"message":"Nama Depan tidak boleh kosong."});
                yii.validation.string(value, messages, {"message":"Nama Depan harus berupa string.","max":16,"tooLong":"Nama Depan harus memiliki paling banyak 16 karakter.","skipOnEmpty":1});
            }
        });

        $("#registry-business-form").yiiActiveForm("add", {
            "id":"person-" + index + "-last_name",
            "name":"[" + index + "]last_name",
            "container":".field-person-" + index + "-last_name",
            "input":"#person-" + index + "-last_name",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.string(value, messages, {"message":"Nama Belakang harus berupa string.","max":16,"tooLong":"Nama Belakang harus memiliki paling banyak 16 karakter.","skipOnEmpty":1});
            }
        });

        $("#registry-business-form").yiiActiveForm("add", {
            "id":"registrybusinesscontactperson-" + index + "-is_primary_contact",
            "name":"[" + index + "]is_primary_contact",
            "container":".field-registrybusinesscontactperson-" + index + "-is_primary_contact",
            "input":"#registrybusinesscontactperson-" + index + "-is_primary_contact",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.boolean(value, messages, {"trueValue":"1","falseValue":"0","message":"Kontak Utama harus berupa \"1\" atau \"0\".","skipOnEmpty":1});
            }
        });

        $("#registry-business-form").yiiActiveForm("add", {
            "id":"person-" + index + "-phone",
            "name":"[" + index + "]phone",
            "container":".field-person-" + index + "-phone",
            "input":"#person-" + index + "-phone",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.string(value, messages, {"message":"Telepon harus berupa string.","max":16,"tooLong":"Telepon harus memiliki paling banyak 16 karakter.","skipOnEmpty":1});
            }
        });

        $("#registry-business-form").yiiActiveForm("add", {
            "id":"person-" + index + "-email",
            "name":"[" + index + "]email",
            "container":".field-person-" + index + "-email",
            "input":"#person-" + index + "-email",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.string(value, messages, {"message":"Email harus berupa string.","max":64,"tooLong":"Email harus memiliki paling banyak 64 karakter.","skipOnEmpty":1});
                yii.validation.email(value, messages, {"pattern":/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/,"fullPattern":/^[^@]*<[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/,"allowName":false,"message":"Email bukan alamat email yang valid.","enableIDN":false,"skipOnEmpty":1});
            }
        });

        $("#registry-business-form").yiiActiveForm("add", {
            "id":"registrybusinesscontactperson-" + index + "-position",
            "name":"[" + index + "]position",
            "container":".field-registrybusinesscontactperson-" + index + "-position",
            "input":"#registrybusinesscontactperson-" + index + "-position",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {"message":"Jabatan tidak boleh kosong."});
            }
        });
    };

    function replaceComponent(contentClone, component, content, index) {

        var inputClass = contentClone.find(".field-" + component).attr("class");
        inputClass = inputClass.replace(content, index);
        contentClone.find("#" + component).parent().attr("class", inputClass);

        var inputName = contentClone.find("#" + component).attr("name");
        inputName = inputName.replace(content, index);
        contentClone.find("#" + component).attr("name", inputName);

        var inputId = contentClone.find("#" + component).attr("id");
        inputId = inputId.replace(content, index);
        contentClone.find("#" + component).attr("id", inputId);

        return contentClone;
    };

    $(".select2.select2-container").find("input.select2-search__field").css("width", "100%");

    district();

    village();

    $("#registrybusiness-city_id").val(1).trigger("change");

    $("#registrybusiness-city_id").on("select2:select", function(e) {

        district(true, function() {

            $("input#registrybusiness-village_id").val(null).trigger("change");
            village();
        });
    });

    if ($("#registrybusiness-city_id").select2("data")[0].id) {

        district(true, function() {

            $("input#registrybusiness-district_id").val("' . $model->district_id . '").trigger("change");

            if ($("#registrybusiness-district_id").select2("data")[0] && $("#registrybusiness-district_id").select2("data")[0].id) {

                village(true, function() {

                    $("input#registrybusiness-village_id").val("' . $model->village_id . '").trigger("change");
                });
            }
        });
    }

    $("#registrybusiness-district_id").on("select2:select", function(e) {

        village(true);
    });

    $(".open-map").on("click", function() {

        if (navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(function(position) {

                $("#registrybusiness-coordinate").val(position.coords.latitude + "," + position.coords.longitude);
            });
        } else {

            $(this).attr("href", "https://www.google.co.id/maps/@-6.9171962,107.6185384,14.75z?hl=en");
            $(this).trigger("click");
        }
        
        return false;
    });

    $(".add-contact-person").on("click", function() {

        var formContactPerson = $(".temp-form").clone();

        formContactPerson = replaceComponent(formContactPerson, "person-index-first_name", "index", indexCount);
        formContactPerson = replaceComponent(formContactPerson, "person-index-last_name", "index", indexCount);
        formContactPerson = replaceComponent(formContactPerson, "registrybusinesscontactperson-index-is_primary_contact", "index", indexCount);
        formContactPerson = replaceComponent(formContactPerson, "person-index-phone", "index", indexCount);
        formContactPerson = replaceComponent(formContactPerson, "person-index-email", "index", indexCount);
        formContactPerson = replaceComponent(formContactPerson, "registrybusinesscontactperson-index-note", "index", indexCount);
        formContactPerson = replaceComponent(formContactPerson, "registrybusinesscontactperson-index-position", "index", indexCount);

        $(".main-form").append(formContactPerson.html());

        addValidator(indexCount);

        $("#person-" + indexCount + "-phone").inputmask({"mask":["999-999-9999","9999-999-9999","9999-9999-9999","9999-99999-9999"]});' .

        Yii::$app->params['checkbox-radio-script'](null, null, '#registrybusinesscontactperson-" + indexCount + "-is_primary_contact') . '

        $(".main-form").find(".contact-person-position").select2({
            theme: "krajee",
            placeholder: "' . Yii::t('app', 'Position') . '",
            minimumResultsForSearch: "Infinity"
        });

        indexCount++;

        return false;
    });

    $(".delete-contact-person").on("click", function() {

        $(".main-form").children(".data-form").last().remove();
        
        if (indexCount > 0) {

            indexCount--;
        }
        
        return false;
    });

    $(".set-all-business-hour").on("click", function() {
    
        $(".business-hour-is-open").each(function() {

            var thisObj = $(this);
            var rootParentThisObj = thisObj.parents(".main-hour-form");

            var businessHourIsOpenDay1 = $(".business-hour-is-open.day-1");
            var rootParentbusinessHourIsOpen = $(".business-hour-is-open.day-1").parents(".main-hour-form");

            var businessHourIsOpen = "uncheck";
            var businessHour24h = "uncheck";

            if (businessHourIsOpenDay1.is(":checked")) {

                businessHourIsOpen = "check";
            }

            if (rootParentbusinessHourIsOpen.find(".business-hour-24h").is(":checked")) {

                businessHour24h = "check";
            }

            $(this).iCheck(businessHourIsOpen);
            rootParentThisObj.find(".business-hour-24h").iCheck(businessHour24h);
            rootParentThisObj.find(".business-hour-time.open").val(rootParentbusinessHourIsOpen.find(".business-hour-time.open").val()).trigger("change");
            rootParentThisObj.find(".business-hour-time.close").val(rootParentbusinessHourIsOpen.find(".business-hour-time.close").val()).trigger("change");
        });

        return false;
    });

    $(".days-count").each(function() {

        var thisObj = $(this);
        
        var counterHour = thisObj.parent().parent().siblings(".data-hour-form").last().find(".counter-hour");

        var indexHourCount = (counterHour.length ? counterHour.val() : 0);

        if ($("#business-hour-24h-" + thisObj.val()).is(":checked")) {

            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").parent().hide();
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").parent().hide();
    
            $("#add-business-hour-day" + thisObj.val()).hide();
            $("#delete-business-hour-day" + thisObj.val()).hide();
        }

        $("#registrybusinesshour-day" + thisObj.val() + "-is_open").on("ifChecked", function(e) {
    
            $("#business-hour-24h-" + thisObj.val()).iCheck("enable");

            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").removeAttr("disabled");
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").removeAttr("disabled");
            
            for (var counter = 1; counter <= indexHourCount; counter++) {

                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + counter + "-open_at").removeAttr("disabled");
                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + counter + "-close_at").removeAttr("disabled");
            }
        });
    
        $("#registrybusinesshour-day" + thisObj.val() + "-is_open").on("ifUnchecked", function(e) {
    
            $("#business-hour-24h-" + thisObj.val()).iCheck("disable");
            $("#business-hour-24h-" + thisObj.val()).iCheck("uncheck");
            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").attr("disabled","disabled");
            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").val(null).trigger("change");

            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").attr("disabled","disabled");
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").val(null).trigger("change");
    
            for (var counter = 1; counter <= indexHourCount; counter++) {
                
                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + counter + "-open_at").attr("disabled","disabled");
                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + counter + "-open_at").val(null).trigger("change");

                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + counter + "-close_at").attr("disabled","disabled");
                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + counter + "-close_at").val(null).trigger("change");
            }
        });
    
        $("#business-hour-24h-" + thisObj.val()).on("ifChecked", function(e) {
    
            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").val("00:00:00").trigger("change");
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").val("24:00:00").trigger("change");

            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").parent().hide();
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").parent().hide();

            $("#add-business-hour-day" + thisObj.val()).hide();
            $("#delete-business-hour-day" + thisObj.val()).hide();

            for (var counter = 1; counter <= indexHourCount; counter++) {

                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + counter + "-open_at").parents(".data-hour-form").remove();
            }

            indexHourCount = 0;
        });
    
        $("#business-hour-24h-" + thisObj.val()).on("ifUnchecked",function(e) {
    
            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").val(null).trigger("change");
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").val(null).trigger("change");

            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").parent().show();
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").parent().show();

            $("#add-business-hour-day" + thisObj.val()).show();
            $("#delete-business-hour-day" + thisObj.val()).show();
        });

        thisObj.parent().find("#add-business-hour-day" + thisObj.val()).on("click", function() {

            indexHourCount++;
    
            var formBusinessHour = $(".additional-hour-temp-form").clone();
    
            formBusinessHour = replaceComponent(formBusinessHour, "registrybusinesshouradditional-dayidx-index-open_at", "index", indexHourCount);
            formBusinessHour = replaceComponent(formBusinessHour, "registrybusinesshouradditional-dayidx-index-close_at", "index", indexHourCount);

            formBusinessHour = replaceComponent(formBusinessHour, "registrybusinesshouradditional-dayidx-" + indexHourCount + "-open_at", "idx", thisObj.val());
            formBusinessHour = replaceComponent(formBusinessHour, "registrybusinesshouradditional-dayidx-" + indexHourCount + "-close_at", "idx", thisObj.val());
    
            thisObj.parents(".main-hour-form").append(formBusinessHour.html());

            if ($("#registrybusinesshour-day" + thisObj.val() + "-is_open").is(":checked")) {

                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + indexHourCount + "-open_at").removeAttr("disabled");
                $("#registrybusinesshouradditional-day"  + thisObj.val() + "-" + indexHourCount + "-close_at").removeAttr("disabled");
            }

            thisObj.parent().parent().siblings(".data-hour-form").last().find(".business-hour-time-additional.open-additional").select2({
                theme: "krajee",
                placeholder: "' . Yii::t('app', 'Time Open') . '"
            });
        
            thisObj.parent().parent().siblings(".data-hour-form").last().find(".business-hour-time-additional.close-additional").select2({
                theme: "krajee",
                placeholder: "' . Yii::t('app', 'Time Close') . '"
            });

            return false;
        });

        thisObj.parent().find("#delete-business-hour-day" + thisObj.val()).on("click", function() {
    
            thisObj.parent().parent().siblings(".data-hour-form").last().remove();
            
            if (indexHourCount > 0) {

                indexHourCount--;
            }

            return false;
        });
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script'](null, null, 'input[type=\"radio\"]:not(\".not-checkbox\"), input[type=\"checkbox\"]:not(\".not-checkbox\")') . $jscript); ?>
