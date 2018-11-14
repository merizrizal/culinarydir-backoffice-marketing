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
use core\models\BusinessHourAdditional;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $modelBusinessCategory core\models\BusinessCategory */
/* @var $dataBusinessCategory core\models\BusinessCategory */
/* @var $modelBusinessProductCategory core\models\BusinessProductCategory */
/* @var $dataBusinessProductCategoryParent core\models\BusinessProductCategory */
/* @var $dataBusinessProductCategoryChild core\models\BusinessProductCategory */
/* @var $modelBusinessFacility core\models\BusinessFacility */
/* @var $dataBusinessFacility core\models\BusinessFacility */
/* @var $modelBusinessHour core\models\BusinessHour */
/* @var $dataBusinessHour core\models\BusinessHour */
/* @var $modelBusinessHourAdditional core\models\BusinessHourAdditional */
/* @var $dataBusinessHourAdditional core\models\BusinessHourAdditional */
/* @var $modelBusinessDetail core\models\BusinessDetail */
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

if ($status !== null) :

    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

$category = Category::find()
    ->orderBy('name')
    ->asArray()->all();

$productParentCategory = ProductCategory::find()
    ->andWhere(['is_parent' => 1])
    ->orderBy('name')
    ->asArray()->all();

$productCategory = ProductCategory::find()
    ->andWhere(['is_parent' => 0])
    ->orderBy('name')
    ->asArray()->all();

$facility = Facility::find()
    ->orderBy('name')
    ->asArray()->all();

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
                                    <div class="col-md-12">
                                    
                                        <?= Html::label(Yii::t('app', 'Business Category')) ?>
                                        
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">

                                        <?php
                                        $selectedDataCategory = [];

                                        if (!empty($dataBusinessCategory)) {

                                            foreach ($dataBusinessCategory as $businessCategory) {

                                                if (!empty($businessCategory['category_id'])) {

                                                    $selectedDataCategory[$businessCategory['category_id']] = ['selected' => true];
                                                }
                                            }
                                        }

                                        echo $form->field($modelBusinessCategory, 'category_id')->dropDownList(
                                            ArrayHelper::map(
                                                $category,
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
                                    
                                        <?= Html::label(Yii::t('app', 'Product Category')) ?>
                                        
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">

                                        <?php
                                        $selectedDataProductParent = [];

                                        if (!empty($dataBusinessProductCategoryParent)) {

                                            foreach ($dataBusinessProductCategoryParent as $businessProductCategoryParent) {

                                                $selectedDataProductParent[$businessProductCategoryParent['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelBusinessProductCategory, 'product_category_id[parent]')->dropDownList(
                                            ArrayHelper::map(
                                                $productParentCategory,
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

                                        if (!empty($dataBusinessProductCategoryChild)) {

                                            foreach ($dataBusinessProductCategoryChild as $businessProductCategoryChild) {

                                                $selectedDataProductChild[$businessProductCategoryChild['product_category_id']] = ['selected' => true];
                                            }
                                        }

                                        echo $form->field($modelBusinessProductCategory, 'product_category_id[child]')->dropDownList(
                                            ArrayHelper::map(
                                                $productCategory,
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
                                    
                                        <?= Html::label(Yii::t('app', 'Facility')) ?>
                                        
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">

                                        <?php
                                        $selectedDataFacility = [];

                                        if (!empty($dataBusinessFacility)) {

                                            foreach ($dataBusinessFacility as $businessFacility) {

                                                if (!empty($businessFacility['facility_id'])) {

                                                    $selectedDataFacility[$businessFacility['facility_id']] = ['selected' => true];
                                                }
                                            }
                                        }

                                        echo $form->field($modelBusinessFacility, 'facility_id')->dropDownList(
                                            ArrayHelper::map(
                                                $facility,
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
                                    <div class="col-md-12">
                                    
                                    	<?= Html::label(Yii::t('app', 'Business Hour')) ?>
                                        <?= Html::button(Yii::t('app', 'Set All'), ['class' => 'btn btn-primary btn-xs set-all-business-hour']) ?>
                                        
                                    </div>
                                </div>

                                <?php
                                $days = Yii::$app->params['days'];
                                $hours = Yii::$app->params['hours'];
                                
                                $is24Hour = [];
                                
                                foreach ($days as $i => $day):
                                
                                    $i++;    
                                    $countAdditional = 0;
                                    $is24Hour[$i - 1] = false;
                                    $dayName = 'day' . $i;
                                    
                                    foreach ($dataBusinessHour as $businessHour) {

                                        if ($businessHour['day'] == $i) {

                                            $modelBusinessHour->is_open = $businessHour['is_open'];
                                            $modelBusinessHour->open_at = $businessHour['open_at'];
                                            $modelBusinessHour->close_at = $businessHour['close_at'];

                                            if ($modelBusinessHour->open_at == '00:00:00' && $modelBusinessHour->close_at == '24:00:00') {

                                                $is24Hour[$i - 1] = true;
                                            }
                                            
                                            break;
                                        }
                                    } ?>
                                        
									<div>    									
                                        <div class="row">
                                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2">

                                                <?= Yii::t('app', $day) ?>

                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">

                                                <?= $form->field($modelBusinessHour, '[day' . $i . ']is_open')
                                                    ->checkbox([
                                                        'label' => Yii::t('app', 'Open'),
                                                        'class' => 'business-hour-is-open day-' . $i,
                                                        'data-day' => $i,
                                                    ]); ?>

                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                <div class="form-group">

                                                    <?= Html::checkbox('always24', $is24Hour[$i - 1], [
                                                        'label' => Yii::t('app', '24 Hours'),
                                                        'data-day' => $i,
                                                        'class' => 'business-hour-24h',
                                                        'disabled' => !$modelBusinessHour->is_open,
                                                        'id' => 'business-hour-24h-' . $i
                                                    ]); ?>

                                                </div>
                                            </div>

                                            <div class="visible-xs clearfix"></div>

                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">

                                                <?= $form->field($modelBusinessHour, '[day' . $i . ']open_at')
                                                    ->dropDownList($hours, [
                                                        'prompt' => '',
                                                        'class' => 'business-hour-time open',
                                                        'style' => 'width: 100%',
                                                        'disabled' => !$modelBusinessHour->is_open,
                                                    ]); ?>

                                            </div>
                                            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">

                                                <?= $form->field($modelBusinessHour, '[day' . $i . ']close_at')
                                                    ->dropDownList($hours, [
                                                        'prompt' => '',
                                                        'class' => 'business-hour-time close',
                                                        'style' => 'width: 100%',
                                                        'disabled' => !$modelBusinessHour->is_open,
                                                    ]); ?>

                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-3 col-xs-4">
                                            	
                                            	<?= Html::hiddenInput('day', $i, ['class' => 'daysCount']) ?>
                                            	
                                                <?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-default add-business-hour-day' . $i, 'data-day' => $i]) ?>
                                                <?= Html::button('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Delete'), ['class' => 'btn btn-default delete-business-hour-day' . $i, 'data-day' => $i]) ?>
                                                
                                            </div>
                                        </div>
                                        
                                        <?php
                                        if (!empty($dataBusinessHourAdditional[$dayName])) {
                                            
                                            foreach ($dataBusinessHourAdditional[$dayName] as $businessHourAdditional) {
                                                     
                                                $countAdditional++;
                                                $modelBusinessHourAdditional->open_at = $businessHourAdditional['open_at'];
                                                $modelBusinessHourAdditional->close_at = $businessHourAdditional['close_at']; ?>
                                            
                                                <div class="data-hour-form">
                                                    <div class="row">
                                                        <div class="col-lg-2 col-lg-offset-5 col-md-3 col-sm-3 col-xs-4">
                                            
                                                            <?= $form->field($modelBusinessHourAdditional, '[day' . $businessHourAdditional['day'] . '][' . $countAdditional . ']open_at')
                                                                ->dropDownList($hours,[
                                                                    'prompt' => '',
                                                                    'class' => 'business-hour-time-additional open-additional',
                                                                    'style' => 'width: 100%',
                                                                    'disabled' => !$modelBusinessHour->is_open,
                                                                ]); ?>
                                            
                                                        </div>
                                                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                                            
                                                            <?= $form->field($modelBusinessHourAdditional, '[day' . $businessHourAdditional['day'] . '][' . $countAdditional . ']close_at')
                                                                ->dropDownList($hours,[
                                                                    'prompt' => '',
                                                                    'class' => 'business-hour-time-additional close-additional',
                                                                    'style' => 'width: 100%',
                                                                    'disabled' => !$modelBusinessHour->is_open,
                                                                ]); ?>
                                            
                                                        </div>
                                                    </div>
                                                    
                                                    <?= Html::hiddenInput('BusinessHourAdditionalExisted[day' . $businessHourAdditional['day'] . '][]', $businessHourAdditional['id'], ['class' => 'deleted-hour', 'data-count' => $countAdditional]) ?>
                                                    
                                                </div>
                                                
                                        <?php
                                            }    
                                        } ?>
                                    
                                    </div>
                                    
                                <?php
                                endforeach; ?>

                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                    
                                        <?= Html::label(Yii::t('app', 'Price Range')) ?>
                                        
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-5">
    
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
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-center">
                                        -
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-5">
    
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
                                <div class="col-lg-12">

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

<div class="additional-hour-temp-form hide">
    <div class="data-hour-form">
        <div class="row">
        	
        	<?php
        	$modelBusinessHourAdditional = new BusinessHourAdditional(); ?>
        
            <div class="col-lg-2 col-lg-offset-5 col-md-3 col-sm-3 col-xs-4">

                <?= $form->field($modelBusinessHourAdditional, '[dayidx][index]open_at')
                    ->dropDownList($hours,[
                        'prompt' => '',
                        'class' => 'business-hour-time-additional open-additional',
                        'style' => 'width: 100%',
                        'disabled' => 'disabled',
                    ]); ?>

            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">

                <?= $form->field($modelBusinessHourAdditional, '[dayidx][index]close_at')
                    ->dropDownList($hours,[
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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

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

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '';

foreach ($is24Hour as $i => $status24Hour) {
    
    $i++;
    
    if ($status24Hour) {
        
        $jscript .= '
            $(".field-businesshour-day" + "' . $i . '" + "-open_at").hide();
            $(".field-businesshour-day" + "' . $i . '" + "-close_at").hide();

            $(".field-businesshour-day" + "' . $i . '" + "-open_at").parent().append("<div class=\"24h-temp\">' . Yii::t('app','24 Hours') . '</div>");

            $(".add-business-hour-day" + "' . $i . '").hide();
            $(".delete-business-hour-day" + "' . $i . '").hide();
        ';
    }
}

$jscript .= '
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

    $(".business-hour-time.open").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Open') . '"
    });

    $(".business-hour-time.close").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Close') . '"
    });

    $(".deleted-hour").parent().find(".business-hour-time-additional.open-additional").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Open') . '"
    });

    $(".deleted-hour").parent().find(".business-hour-time-additional.close-additional").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Time Close') . '"
    });

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

    $(".set-all-business-hour").on("click", function() {

        $(".business-hour-is-open").each(function() {

            var thisObj = $(this);
            var rootParentThisObj = thisObj.parent().parent().parent().parent().parent();

            var businessHourIsOpenDay1 = $(".business-hour-is-open.day-1");
            var rootParentbusinessHourIsOpen = $(".business-hour-is-open.day-1").parent().parent().parent().parent().parent();

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

    $(".daysCount").each(function() {
        
        var thisObj = $(this);
        
        var deletedHour = thisObj.parent().parent().siblings(".data-hour-form").last().find(".deleted-hour");

        var indexHourCount = (deletedHour.length ? deletedHour.data("count") : 0);

        $(".business-hour-is-open.day-" + thisObj.val()).on("ifChecked", function(e) {

            var elemDay = $(this).data("day");
    
            $("#business-hour-24h-" + elemDay).iCheck("enable");

            $("#businesshour-day"  + elemDay + "-open_at").removeAttr("disabled");
            $("#businesshour-day"  + elemDay + "-close_at").removeAttr("disabled");
            
            for (var counter = 1; counter <= indexHourCount; counter++) {

                $("#businesshouradditional-day"  + elemDay + "-" + counter + "-open_at").removeAttr("disabled");
                $("#businesshouradditional-day"  + elemDay + "-" + counter + "-close_at").removeAttr("disabled");
            }
        });
    
        $(".business-hour-is-open.day-" + thisObj.val()).on("ifUnchecked", function(e) {
    
            var elemDay = $(this).data("day");
    
            $("#business-hour-24h-" + elemDay).iCheck("disable");
            $("#business-hour-24h-" + elemDay).iCheck("uncheck");
            $("#businesshour-day"  + elemDay + "-open_at").attr("disabled","disabled");
            $("#businesshour-day"  + elemDay + "-open_at").val(null).trigger("change");

            $("#businesshour-day"  + elemDay + "-close_at").attr("disabled","disabled");
            $("#businesshour-day"  + elemDay + "-close_at").val(null).trigger("change");
    
            for (var counter = 1; counter <= indexHourCount; counter++) {
                
                $("#businesshouradditional-day"  + elemDay + "-" + counter + "-open_at").attr("disabled","disabled");
                $("#businesshouradditional-day"  + elemDay + "-" + counter + "-open_at").val(null).trigger("change");

                $("#businesshouradditional-day"  + elemDay + "-" + counter + "-close_at").attr("disabled","disabled");
                $("#businesshouradditional-day"  + elemDay + "-" + counter + "-close_at").val(null).trigger("change");
            }
        });
    
        $("#business-hour-24h-" + thisObj.val()).on("ifChecked", function(e) {
    
            var elemDay = $(this).data("day");

            var deletedHourContent = thisObj.parent().parent().siblings().find(".deleted-hour");
    
            $("#businesshour-day"  + elemDay + "-open_at").val("00:00:00").trigger("change");
            $("#businesshour-day"  + elemDay + "-close_at").val("24:00:00").trigger("change");

            $(".field-businesshour-day" + elemDay + "-open_at").hide();
            $(".field-businesshour-day" + elemDay + "-close_at").hide();

            $(".field-businesshour-day" + elemDay + "-open_at").parent().append("<div class=\"24h-temp\">' . Yii::t('app','24 Hours') . '</div>");

            $(".add-business-hour-day" + elemDay).hide();
            $(".delete-business-hour-day" + elemDay).hide();

            if (deletedHourContent.length) {
        
                deletedHourContent.attr("name", (deletedHourContent.attr("name").replace("Existed", "Deleted")));
                thisObj.parent().parent().siblings().find(".row").remove();
                thisObj.parent().parent().siblings().removeClass("data-hour-form").addClass("data-hour-form-deleted");
            } else {

                thisObj.parent().parent().siblings(".data-hour-form").remove();
            }

            indexHourCount = 0;
        });
    
        $("#business-hour-24h-" + thisObj.val()).on("ifUnchecked",function(e) {
    
            var elemDay = $(this).data("day");
    
            $("#businesshour-day"  + elemDay + "-open_at").val(null).trigger("change");
            $("#businesshour-day"  + elemDay + "-close_at").val(null).trigger("change");

            $(".field-businesshour-day" + elemDay + "-open_at").parent().find(".24h-temp").remove();

            $(".field-businesshour-day" + elemDay + "-open_at").show();
            $(".field-businesshour-day" + elemDay + "-close_at").show();

            $(".add-business-hour-day" + elemDay).show();
            $(".delete-business-hour-day" + elemDay).show();
        });

        thisObj.parent().find(".add-business-hour-day" + thisObj.val()).on("click", function() {

            var elemDay = $(this).data("day");
            
            indexHourCount++;
    
            var formBusinessHour = $(".additional-hour-temp-form").clone();
    
            formBusinessHour = replaceComponent(formBusinessHour, "businesshouradditional-dayidx-index-open_at", "index", indexHourCount);
            formBusinessHour = replaceComponent(formBusinessHour, "businesshouradditional-dayidx-index-close_at", "index", indexHourCount);

            formBusinessHour = replaceComponent(formBusinessHour, "businesshouradditional-dayidx-" + indexHourCount + "-open_at", "idx", elemDay);
            formBusinessHour = replaceComponent(formBusinessHour, "businesshouradditional-dayidx-" + indexHourCount + "-close_at", "idx", elemDay);
    
            thisObj.parent().parent().parent().append(formBusinessHour.html());

            if ($(".business-hour-is-open.day-" + elemDay).is(":checked")) {

                $("#businesshouradditional-day"  + elemDay + "-" + indexHourCount + "-open_at").removeAttr("disabled");
                $("#businesshouradditional-day"  + elemDay + "-" + indexHourCount + "-close_at").removeAttr("disabled");
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

        thisObj.parent().find(".delete-business-hour-day" + thisObj.val()).on("click", function() {
            
            var lastData = thisObj.parent().parent().siblings(".data-hour-form").last();

            if (lastData.find(".deleted-hour").length) {

                lastData.find(".row").siblings().attr("name", (lastData.find(".row").siblings().attr("name").replace("Existed", "Deleted")));
                lastData.find(".row").remove();
                lastData.removeClass("data-hour-form").addClass("data-hour-form-deleted");
            } else {

                lastData.remove();
            }
            
            if (indexHourCount > 0) {

                indexHourCount--;    
            }

            return false;
        });
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>