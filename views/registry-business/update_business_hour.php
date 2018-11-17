<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\RegistryBusinessHourAdditional;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $dataRegistryBusinessHour core\models\RegistryBusinessHour */
/* @var $modelRegistryBusinessHour core\models\RegistryBusinessHour */
/* @var $dataRegistryBusinessHourAdditional core\models\RegistryBusinessHourAdditional */
/* @var $modelRegistryBusinessHourAdditional core\models\RegistryBusinessHourAdditional */
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

$this->title = 'Update ' . Yii::t('app', 'Operational Hours') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Data Application'), 'url' => ['index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-' . strtolower($statusApproval), 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Operational Hours');

echo $ajaxRequest->component(); ?>

<div class="registry-business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="registry-business-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-business-form',
                        'action' => ['update-business-hour', 'id' => $model->id, 'statusApproval' => strtolower($statusApproval)],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Operational Hours') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row mb-10">
                                    <div class="col-md-12">
                                        <label class="control-label"><?= Yii::t('app', 'Business Hour') ?></label>
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
                                                    
                                                    <?= Html::hiddenInput('RegistryBusinessHourAdditionalExisted[day' . $registryBusinessHourAdditional['day'] . '][]', $registryBusinessHourAdditional['id'], ['class' => 'deleted-hour', 'data-count' => $countAdditional]) ?>
                                                    
                                                </div>
                                        
                            				<?php
                                            endforeach;
                                        endif; ?>
                                    
                                    </div>
                                    
                                <?php
                                endforeach; ?>

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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
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
        
        var deletedHour = thisObj.parent().parent().siblings(".data-hour-form").last().find(".deleted-hour");

        var indexHourCount = (deletedHour.length ? deletedHour.data("count") : 0);

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

            var deletedHourContent = thisObj.parent().parent().siblings().find(".deleted-hour");
    
            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").val("00:00:00").trigger("change");
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").val("24:00:00").trigger("change");

            $(".field-registrybusinesshour-day" + thisObj.val() + "-open_at").hide();
            $(".field-registrybusinesshour-day" + thisObj.val() + "-close_at").hide();

            $("#add-business-hour-day" + thisObj.val()).hide();
            $("#delete-business-hour-day" + thisObj.val()).hide();
            
            if (deletedHourContent.length) {
        
                deletedHourContent.attr("name", (deletedHourContent.attr("name").replace("Existed", "Deleted")));
                thisObj.parent().parent().siblings(".data-hour-form").find(".row").remove();
                thisObj.parent().parent().siblings(".data-hour-form").removeClass("data-hour-form").addClass("data-hour-form-deleted");
            } else {

                thisObj.parent().parent().siblings(".data-hour-form").remove();
            }

            indexHourCount = 0;
        });
    
        $("#business-hour-24h-" + thisObj.val()).on("ifUnchecked",function(e) {
    
            $("#registrybusinesshour-day"  + thisObj.val() + "-open_at").val(null).trigger("change");
            $("#registrybusinesshour-day"  + thisObj.val() + "-close_at").val(null).trigger("change");

            $(".field-registrybusinesshour-day" + thisObj.val() + "-open_at").show();
            $(".field-registrybusinesshour-day" + thisObj.val() + "-close_at").show();

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