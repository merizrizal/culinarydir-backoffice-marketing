<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$form = new ActiveForm([
    'fieldConfig' => [
        'template' => '{input}{error}',
    ]
]); ?>

<div class="main-form">
    <div class="mb-20">
        <div class="row">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[1]first_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'First Name')]) ?>
            </div>

            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[1]last_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Last Name')]) ?>
            </div>

            <div class="col-md-4 col-xs-12">
                <?= $form->field($modelRegistryBusinessContactPerson, '[1]is_primary_contact')->checkbox() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[1]phone')->widget(MaskedInput::className(), [
                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => Yii::t('app', 'Phone'),
                    ],
                ]) ?>
            </div>

            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[1]email', [
                    'enableAjaxValidation' => true
                ])->textInput([
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ]) ?>
            </div>
        </div>
    </div>
</div>

<div class="second-form">

</div>

<div class="row">
    <div class="col-md-12">
        <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Add'), null, ['class' => 'btn btn-default add-contact-person']) ?>
    </div>
</div>

<div class="temp-form hide">
    <div class="mb-10">
        <hr>
        <div class="row mt-10">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[index]first_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'First Name')]) ?>
            </div>

            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelPerson, '[index]last_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Last Name')]) ?>
            </div>

            <div class="col-md-4 col-xs-12">
                <?= $form->field($modelRegistryBusinessContactPerson, '[index]is_primary_contact')->checkbox() ?>
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
                <?= $form->field($modelPerson, '[index]email', [
                    'enableAjaxValidation' => true
                ])->textInput([
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var indexCount = 1;

    var addValidator = function(index) {

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
                yii.validation.required(value, messages, {"message":"Nama Belakang tidak boleh kosong."});
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
                yii.validation.required(value, messages, {"message":"Telepon tidak boleh kosong."});
                yii.validation.string(value, messages, {"message":"Telepon harus berupa string.","max":16,"tooLong":"Telepon harus memiliki paling banyak 16 karakter.","skipOnEmpty":1});
            }
        });

        $("#registry-business-form").yiiActiveForm("add", {
            "id":"person-" + index + "-email",
            "name":"[" + index + "]email",
            "container":".field-person-" + index + "-email",
            "input":"#person-" + index + "-email",
            "enableAjaxValidation":true,
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {"message":"Email tidak boleh kosong."});
                yii.validation.string(value, messages, {"message":"Email harus berupa string.","max":64,"tooLong":"Email harus memiliki paling banyak 64 karakter.","skipOnEmpty":1});
                yii.validation.email(value, messages, {"pattern":/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/,"fullPattern":/^[^@]*<[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/,"allowName":false,"message":"Email bukan alamat email yang valid.","enableIDN":false,"skipOnEmpty":1});
            }
        });
    };

    addValidator(indexCount); ' .

    Yii::$app->params['checkbox-radio-script'](null, null, '#registrybusinesscontactperson-1-is_primary_contact') . '

    $(".add-contact-person").on("click", function() {

        indexCount++;

        var formContactPerson = $(".temp-form").clone();

        var replaceIndex = function(contentClone, component, index) {

            var inputClass = contentClone.find(".field-" + component).attr("class");
            inputClass = inputClass.replace("index", index);
            contentClone.find("#" + component).parent().attr("class", inputClass);

            var inputName = contentClone.find("#" + component).attr("name");
            inputName = inputName.replace("index", index);
            contentClone.find("#" + component).attr("name", inputName);

            var inputId = contentClone.find("#" + component).attr("id");
            inputId = inputId.replace("index", index);
            contentClone.find("#" + component).attr("id", inputId);

            return contentClone;
        };

        formContactPerson = replaceIndex(formContactPerson, "person-index-first_name", indexCount);
        formContactPerson = replaceIndex(formContactPerson, "person-index-last_name", indexCount);
        formContactPerson = replaceIndex(formContactPerson, "registrybusinesscontactperson-index-is_primary_contact", indexCount);
        formContactPerson = replaceIndex(formContactPerson, "person-index-phone", indexCount);
        formContactPerson = replaceIndex(formContactPerson, "person-index-email", indexCount);

        $(".second-form").append(formContactPerson.html());

        addValidator(indexCount);

        $("#person-" + indexCount + "-phone").inputmask({"mask":["999-999-9999","9999-999-9999","9999-9999-9999","9999-99999-9999"]});' .

        Yii::$app->params['checkbox-radio-script'](null, null, '#registrybusinesscontactperson-" + indexCount + "-is_primary_contact') . '
    });
';

$this->registerJs($jscript); ?>