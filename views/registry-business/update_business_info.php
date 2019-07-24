<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\City;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $statusApproval string */

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

$this->title = 'Update ' . Yii::t('app', 'Business Information') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Data Application'), 'url' => ['index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-' . strtolower($statusApproval), 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Business Information');

echo $ajaxRequest->component(); ?>

<div class="registry-business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="registry-business-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-business-form',
                        'action' => ['update-business-info', 'id' => $model->id, 'statusApproval' => strtolower($statusApproval)],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Business Information') ?></h4>
                        </div>

                        <div class="x_content">

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
                                <div class="col-xs-12 col-sm-3">

                                    <?= $form->field($model, 'address_type')->dropDownList(['Gang' => 'Gang', 'Jalan' => 'Jalan', 'Komplek' => 'Komplek'], [
                                        'prompt' => Yii::t('app', 'Address Type'),
                                        'style' => 'width: 100%'
                                    ]) ?>

                                </div>
                                <div class="col-xs-12 col-sm-5">
                                    <?= $form->field($model, 'address')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Address')]) ?>
                                </div>
                                <div class="col-xs-12 col-sm-4">
                                    <?= $form->field($model, 'address_info')->textarea(['rows' => 3, 'placeholder' => Yii::t('app', 'Address Info')]) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-4 col-sm-3">

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
                                <div class="col-xs-4 col-sm-3">
                                    <?= $form->field($model, 'district_id')->textInput(['style' => 'width: 100%']) ?>
                                </div>
                                <div class="col-xs-4 col-sm-3">
                                    <?= $form->field($model, 'village_id')->textInput(['style' => 'width: 100%']) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-9 col-sm-6">
                                    <?= $form->field($model, 'coordinate')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Coordinate')]) ?>
                                </div>
                                <div class="col-xs-3 col-sm-3">
                                    <?= Html::a('<i class="fa fa-map-marker-alt"></i> ' . Yii::t('app', 'Open Map'), 'https://www.google.co.id/maps/@-6.9171962,107.6185384,14.75z?hl=en', ['class' => 'btn btn-primary btn-block direct', 'target' => '_blank']) ?>
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
                                <div class="col-xs-12">
                                    <?= $form->field($model, 'note')->textarea(['rows' => 13, 'placeholder' => Yii::t('app', 'Note')]) ?>
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
    function district(executeRemote, afterSuccess) {

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

    $("#registrybusiness-address_type").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Address Type') . '",
        minimumResultsForSearch: Infinity
    });

    $("#registrybusiness-city_id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'City ID') . '"
    });

    district();

    village();

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

    $("#registrybusiness-city_id").on("select2:select", function() {

        district(true, function() {

            $("input#registrybusiness-village_id").val(null).trigger("change");
            village();
        });
    });

    $("#registrybusiness-district_id").on("select2:select", function() {

        village(true);
    });
';

$this->registerJs($jscript); ?>