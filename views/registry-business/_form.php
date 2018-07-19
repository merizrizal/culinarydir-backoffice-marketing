<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\file\FileInput;
use kartik\touchspin\TouchSpin;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use sycomponent\Tools;
use core\models\MembershipType;
use core\models\City;
use core\models\Category;
use core\models\ProductCategory;
use core\models\Facility;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $form yii\widgets\ActiveForm */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryBusiness',
]);

$ajaxRequest->form();

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) :
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

$membershipType = MembershipType::find()->andWhere(['is_free' => true, 'as_archive' => false])->orderBy('order')->asArray()->all();

$membershipTypeDefault = null;
foreach ($membershipType as $value) {

    if ($value['is_default']) {
        $membershipTypeDefault = $value['id'];
        break;
    }
}

$category = Category::find()
        ->orderBy('name')
        ->asArray()->all();

$productParentCategory = ProductCategory::find()
        ->andWhere(['parent_id' => null])
        ->orderBy('name')
        ->asArray()->all();

$productCategory = ProductCategory::find()
        ->andWhere(['not', ['parent_id' => null]])
        ->orderBy('name')
        ->asArray()->all();

$facility = Facility::find()
        ->orderBy('name')
        ->asArray()->all(); ?>

<?= $ajaxRequest->component() ?>

<div class="row">
    <div class="col-sm-12">
        <div class="x_panel">
            <div class="registry-business-form">

                <?php
                $form = ActiveForm::begin([
                    'id' => 'registry-business-form',
                    'action' => $model->isNewRecord ? (empty($type) ? ['create'] : ['create', 'type' => $type]) : (empty($type) ? [empty($resubmit) ? 'update' : 'resubmit', 'id' => $model->id] : [empty($resubmit) ? 'update' : 'resubmit', 'id' => $model->id, 'type' => $type]),
                    'options' => [

                    ],
                    'fieldConfig' => [
                        'template' => '{input}{error}',
                    ]
                ]); ?>

                    <div class="x_title">

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?php
                                    if (!$model->isNewRecord)
                                        echo Html::a('<i class="fa fa-upload"></i> ' . 'Create', (empty($type) ? ['create'] : ['create', 'type' => $type]), ['class' => 'btn btn-success']); ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="x_content">

                        <?php
                        if (!$model->isNewRecord): ?>

                            <div class="row">
                                <div class="col-lg-12">
                                    <h4><strong><?= Yii::t('app', 'User In Charge') ?></strong> : <?= $model->userInCharge->full_name ?></h4>
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <h4><strong><?= Yii::t('app', 'Status') ?></strong> : <?= $model->status ?></h4>
                                    <hr>
                                </div>
                            </div>

                        <?php
                        endif;?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h4><strong><?= Yii::t('app', 'Membership Type') ?></strong></h4>
                                    <hr>
                                </div>
                            </div>
                        </div>

                        <?php
                        $model->membership_type_id = $membershipTypeDefault; ?>

                        <?= $form->field($model, 'membership_type_id', [
                            'template' => '
                                <div class="row">
                                    <div class="col-md-12">
                                        {input}
                                        {error}
                                    </div>
                                </div>
                            ',
                        ])->radioList(
                                ArrayHelper::map(
                                    MembershipType::find()->andWhere(['is_free' => true, 'as_archive' => false])->orderBy('order')->asArray()->all(),
                                    'id',
                                    function($data) {
                                        return $data['name'];
                                    }
                                ),
                                [
                                    'separator' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'
                                ]) ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4><strong><?= Yii::t('app', 'Business Information') ?></strong></h4>
                                    <hr>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Name')]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'unique_name', [
                                    'enableAjaxValidation' => true
                                ])->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Unique Name')]) ?>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <?= $form->field($model, 'address_type')->dropDownList(['Gang' => 'Gang', 'Jalan' => 'Jalan', 'Komplek' => 'Komplek'], ['prompt' => Yii::t('app', 'Address Type'), 'style' => 'width: 100%']) ?>
                            </div>

                            <div class="col-md-5">
                                <?= $form->field($model, 'address')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Address')]) ?>
                            </div>


                            <div class="col-md-4">
                                <?= $form->field($model, 'address_info')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Address Info')]) ?>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-3 col-xs-6">
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

                            <div class="col-lg-3 col-xs-6">
                                <?= $form->field($model, 'district_id')->textInput([
                                    'style' => 'width: 100%'
                                ]) ?>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <?= $form->field($model, 'village_id')->textInput([
                                    'style' => 'width: 100%'
                                ]) ?>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-6 col-xs-9">
                                <?= $form->field($model, 'coordinate')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Coordinate')]) ?>
                            </div>

                            <div class="col-lg-3 col-xs-3">
                                <?= Html::a('<i class="fa fa-map-marker-alt"></i> ' . Yii::t('app', 'Open Map'), 'https://www.google.co.id/maps/@-6.9171962,107.6185384,14.75z?hl=en', ['class' => 'btn btn-primary btn-block direct', 'target' => '_blank']) ?>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-lg-3 col-xs-6">
                                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Email')]) ?>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <?= $form->field($model, 'phone1')->widget(MaskedInput::className(), [
                                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                    'options' => [
                                        'placeholder' => Yii::t('app', 'Phone1'),
                                        'class' => 'form-control'
                                    ]
                                ]) ?>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <?= $form->field($model, 'phone2')->widget(MaskedInput::className(), [
                                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                    'options' => [
                                        'placeholder' => Yii::t('app', 'Phone2'),
                                        'class' => 'form-control'
                                    ]
                                ]) ?>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <?= $form->field($model, 'phone3')->widget(MaskedInput::className(), [
                                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                    'options' => [
                                        'placeholder' => Yii::t('app', 'Phone3'),
                                        'class' => 'form-control'
                                    ]
                                ]) ?>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4><strong><?= Yii::t('app', 'Contact Person') ?></strong></h4>
                                    <hr>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4><strong><?= Yii::t('app', 'Marketing Information') ?></strong></h4>
                                    <hr>
                                </div>
                            </div>
                        </div>

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

                                        foreach ($dataRegistryBusinessCategory as $value) {

                                            if (!empty($value['category_id'])) {

                                                $selectedDataCategory[$value['category_id']] = ['selected' => true];
                                            }
                                        }
                                    } ?>

                                    <?= $form->field($modelRegistryBusinessCategory, 'category_id')->dropDownList(
                                        ArrayHelper::map(
                                            $category,
                                            'id',
                                            'name'
                                        ),
                                        [
                                            'class' => 'registry-business-category',
                                            'multiple' => 'multiple',
                                            'prompt' => '',
                                            'style' => 'width: 100%',
                                            'options' => $selectedDataCategory
                                        ]) ?>

                                </div>
                            </div>
                        </div>

                        <hr>

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

                                        foreach ($dataRegistryBusinessProductCategoryParent as $value) {

                                            $selectedDataProductParent[$value['product_category_id']] = ['selected' => true];
                                        }
                                    } ?>

                                    <?= $form->field($modelRegistryBusinessProductCategory, 'product_category_id[parent]')->dropDownList(
                                        ArrayHelper::map(
                                            $productParentCategory,
                                            'id',
                                            'name'
                                        ),
                                        [
                                            'class' => 'registry-business-product-category-parent',
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

                                        foreach ($dataRegistryBusinessProductCategoryChild as $value) {

                                            $selectedDataProductChild[$value['product_category_id']] = ['selected' => true];
                                        }
                                    } ?>

                                    <?= $form->field($modelRegistryBusinessProductCategory, 'product_category_id[child]')->dropDownList(
                                        ArrayHelper::map(
                                            $productCategory,
                                            'id',
                                            'name'
                                        ),
                                        [
                                            'class' => 'registry-business-product-category-child',
                                            'multiple' => 'multiple',
                                            'prompt' => '',
                                            'style' => 'width: 100%',
                                            'options' => $selectedDataProductChild
                                        ]) ?>

                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label"><?= Yii::t('app', 'Business Hour') ?></label>
                                </div>
                            </div>

                            <?php
                            $days = Yii::$app->params['days'];
                            $hours = Yii::$app->params['hours'];

                            foreach ($days as $i => $day):

                                $is24Hour = false;

                                foreach ($dataRegistryBusinessHour as $value) {

                                    if ($value['day'] == ($i + 1)) {

                                        $modelRegistryBusinessHour->is_open = $value['is_open'];
                                        $modelRegistryBusinessHour->open_at = $value['open_at'];
                                        $modelRegistryBusinessHour->close_at = $value['close_at'];

                                        if ($modelRegistryBusinessHour->open_at == '00:00:00' && $modelRegistryBusinessHour->close_at == '24:00:00') {
                                            $is24Hour = true;
                                        }

                                        break;
                                    }
                                } ?>

                                <div class="row">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">
                                        <label class="control-label"><?= Yii::t('app', $days[$i]) ?></label>
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">

                                        <?= $form->field($modelRegistryBusinessHour, '[day' . ($i + 1) . ']is_open')
                                            ->checkbox([
                                                'label' => Yii::t('app', 'Open'),
                                                'class' => 'cb-is-open',
                                                'data-day' => $i + 1,
                                            ]); ?>

                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                        <div class="form-group">

                                            <?= Html::checkbox('always24', $is24Hour, [
                                                'label' => Yii::t('app', '24 Hours'),
                                                'data-day' => $i + 1,
                                                'class' => 'cb-always24',
                                                'disabled' => !$modelRegistryBusinessHour->is_open,
                                                'id' => 'cb-always24-' . ($i + 1)
                                            ]); ?>

                                        </div>
                                    </div>

                                    <div class="visible-xs clearfix"></div>

                                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">

                                        <?= $form->field($modelRegistryBusinessHour, '[day' . ($i + 1) . ']open_at')
                                            ->dropDownList(
                                                $hours,
                                                [
                                                    'prompt' => '',
                                                    'class' => 'cb-time open',
                                                    'style' => 'width: 100%',
                                                    'disabled' => !$modelRegistryBusinessHour->is_open,
                                                ]
                                            ); ?>

                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">

                                        <?= $form->field($modelRegistryBusinessHour, '[day' . ($i + 1) . ']close_at')
                                            ->dropDownList(
                                                $hours,
                                                [
                                                    'prompt' => '',
                                                    'class' => 'cb-time close',
                                                    'style' => 'width: 100%',
                                                    'disabled' => !$modelRegistryBusinessHour->is_open,
                                                ]
                                            ); ?>

                                    </div>
                                </div>

                            <?php
                            endforeach; ?>

                        </div>

                        <hr>

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
                                        'verticalupclass' => 'glyphicon glyphicon-plus',
                                        'verticaldownclass' => 'glyphicon glyphicon-minus',
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
                                        'verticalupclass' => 'glyphicon glyphicon-plus',
                                        'verticaldownclass' => 'glyphicon glyphicon-minus',
                                    ],
                                ]); ?>

                            </div>
                        </div>

                        <hr>

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

                                        foreach ($dataRegistryBusinessFacility as $value) {

                                            if (!empty($value['facility_id'])) {

                                                $selectedDataFacility[$value['facility_id']] = ['selected' => true];
                                            }
                                        }
                                    } ?>

                                    <?= $form->field($modelRegistryBusinessFacility, 'facility_id')->dropDownList(
                                        ArrayHelper::map(
                                            $facility,
                                            'id',
                                            'name'
                                        ),
                                        [
                                            'class' => 'registry-business-facility',
                                            'multiple' => 'multiple',
                                            'prompt' => '',
                                            'style' => 'width: 100%',
                                            'options' => $selectedDataFacility
                                        ]) ?>

                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-10 col-sm-offset-2">

                                    <div class="row">

                                        <?php
                                        foreach ($dataRegistryBusinessImage as $registryBusinessImage): ?>

                                            <div class="col-xs-3">
                                                <div class="thumbnail">
                                                    <div class="image view view-first">
                                                        <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $registryBusinessImage['image'], 200, 150), ['style' => 'width: 100%; display: block;']);  ?>
                                                        <div class="mask">
                                                            <p>&nbsp;</p>
                                                            <div class="tools tools-bottom">
                                                                <a class="show-image direct" href="<?= Yii::getAlias('@uploadsUrl') . '/img/registry_business/' . $registryBusinessImage['image'] ?>"><i class="fa fa-search"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-center mt-10">
                                                        <?= Html::checkbox('RegistryBusinessImageDelete[]', false, ['class' => 'form-control', 'label' => 'Delete', 'value' => $registryBusinessImage['id']]) ?>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php
                                        endforeach; ?>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <?= Html::label(Yii::t('app', 'Foto'), null, ['class' => 'control-label']) ?>
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

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php
                                    if (!empty($resubmit)) {

                                        $icon = '<i class="fa fa-share-square"></i> ';
                                        echo Html::hiddenInput('resubmit', true);
                                        echo Html::submitButton($model->isNewRecord ? $icon . 'Resubmit' : $icon . 'Resubmit', ['class' => 'btn btn-primary']);
                                    } else {

                                        $icon = '<i class="fa fa-save"></i> ';
                                        echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    }

                                    echo Html::a('<i class="fa fa-times"></i> Cancel', (empty($type) ? ['index'] : ['index', 'type' => $type]), ['class' => 'btn btn-default']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div><!-- /.row -->

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$cssscript = '
    ul#select2-registrybusinesscategory-category_id-results > li {
        float: left;
        width: 50%;
    }

    ul#select2-registrybusinessproductcategory-product_category_id-parent-results > li {
        float: left;
        width: 50%;
    }

    ul#select2-registrybusinessproductcategory-product_category_id-child-results > li {
        float: left;
        width: 50%;
    }

    ul#select2-registrybusinessfacility-facility_id-results > li {
        float: left;
        width: 50%;
    }

    @media (min-width: 768px) {
        ul#select2-registrybusinesscategory-category_id-results > li {
            float: left;
            width: 33.33333333%;
        }

        ul#select2-registrybusinessproductcategory-product_category_id-parent-results > li {
            float: left;
            width: 33.33333333%;
        }

        ul#select2-registrybusinessproductcategory-product_category_id-child-results > li {
            float: left;
            width: 33.33333333%;
        }

        ul#select2-registrybusinessfacility-facility_id-results > li {
            float: left;
            width: 33.33333333%;
        }
    }

    @media (min-width: 1200px) {
        ul#select2-registrybusinesscategory-category_id-results > li {
            float: left;
            width: 20%;
        }

        ul#select2-registrybusinessproductcategory-product_category_id-parent-results > li {
            float: left;
            width: 20%;
        }

        ul#select2-registrybusinessproductcategory-product_category_id-child-results > li {
            float: left;
            width: 20%;
        }

        ul#select2-registrybusinessfacility-facility_id-results > li {
            float: left;
            width: 20%;
        }
    }
';

$this->registerCss($cssscript);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#registrybusiness-address_type").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Address Type') . '",
        minimumResultsForSearch: "Infinity"
    });

    $("#registrybusiness-city_id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City ID') . '"
    });

    $(".registry-business-category").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Business Category') . '",
    });

    $(".registry-business-product-category-parent").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Product Category General') . '"
    });

    $(".registry-business-product-category-child").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Product Category Specific') . '"
    });

    $(".cb-time.open").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Open') . '"
    });

    $(".cb-time.close").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Close') . '"
    });

    $(".registry-business-facility").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Facility') . '"
    });

    $("#registrybusiness-city_id").val(1).trigger("change");

    var district = function(executeRemote, afterSuccess) {

        var setDistrict = function(remoteData) {

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
                url: "' . Yii::$app->urlManager->createUrl('district/get-district-by-city') . '?id=" + $("#registrybusiness-city_id").select2("data")[0].id,
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

    district();

    if ($("#registrybusiness-city_id").select2("data")[0].id) {

        district(true, function() {

            $("input#registrybusiness-district_id").val("' . $model->district_id . '").trigger("change");

            if ($("#registrybusiness-district_id").select2("data")[0].id) {

                village(true, function() {

                    $("input#registrybusiness-village_id").val("' . $model->village_id . '").trigger("change");
                });
            }
        });
    }

    $("#registrybusiness-city_id").on("select2:select", function(e) {

        district(true, function() {

            $("input#registrybusiness-village_id").val(null).trigger("change");
            village();
        });
    });

    var village = function(executeRemote, afterSuccess) {

        var setVillage = function(remoteData) {

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
                url: "' . Yii::$app->urlManager->createUrl('village/get-village-by-district') . '?id=" + $("#registrybusiness-district_id").select2("data")[0].id,
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

    village();

    $("#registrybusiness-district_id").on("select2:select", function(e) {

        village(true);
    });

    $(".thumbnail").magnificPopup({
        delegate: "a.show-image",
        type: "image",
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
        },
        image: {
            tError: "The image could not be loaded."
        }
    });

    $(".cb-is-open").on("ifChecked",function(e){

        var elemDay = $(this).data("day");

        $("#cb-always24-" + elemDay).iCheck("enable");
        $("#registrybusinesshour-day"  + elemDay + "-open_at").removeAttr("disabled");
        $("#registrybusinesshour-day"  + elemDay + "-close_at").removeAttr("disabled");
    });

    $(".cb-is-open").on("ifUnchecked",function(e){

        var elemDay = $(this).data("day");

        $("#cb-always24-" + elemDay).iCheck("disable");
        $("#registrybusinesshour-day"  + elemDay + "-open_at").attr("disabled","disabled");
        $("#registrybusinesshour-day"  + elemDay + "-close_at").attr("disabled","disabled");
    });

    $(".cb-always24").on("ifChecked",function(e){

        var elemDay = $(this).data("day");

        $("#registrybusinesshour-day"  + elemDay + "-open_at").val("00:00:00").trigger("change");
        $("#registrybusinesshour-day"  + elemDay + "-close_at").val("24:00:00").trigger("change");
    });

    $(".cb-always24").on("ifUnchecked",function(e){

        var elemDay = $(this).data("day");

        $("#registrybusinesshour-day"  + elemDay + "-open_at").val(null).trigger("change");
        $("#registrybusinesshour-day"  + elemDay + "-close_at").val(null).trigger("change");
    });
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>