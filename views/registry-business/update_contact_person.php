<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\Person;
use core\models\RegistryBusinessContactPerson;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $modelRegistryBusinessContactPerson core\models\RegistryBusinessContactPerson */
/* @var $dataRegistryBusinessContactPerson core\models\RegistryBusinessContactPerson */
/* @var $modelPerson core\models\Person */
/* @var $statusApproval backoffice\modules\marketing\controllers\RegistryBusinessController */

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

$this->title = 'Update ' . Yii::t('app', 'Contact Person') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Data Application'), 'url' => ['index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-' . strtolower($statusApproval), 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Contact Person');

echo $ajaxRequest->component(); ?>

<div class="registry-business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="registry-business-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'registry-business-form',
                        'action' => ['update-contact-person', 'id' => $model->id, 'statusApproval' => strtolower($statusApproval)],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Contact Person') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <div class="main-form">
                                	
                                        	<?php
                                	        if (!empty($dataRegistryBusinessContactPerson)):
                                	            
                                	            foreach ($dataRegistryBusinessContactPerson as $i => $registryBusinessContactPerson):
                                	                
                                	                $modelPerson->first_name = $registryBusinessContactPerson['first_name'];
                                	                $modelPerson->last_name = $registryBusinessContactPerson['last_name'];
                                	                $modelRegistryBusinessContactPerson->position = $registryBusinessContactPerson['position'];
                                	                $modelPerson->phone = $registryBusinessContactPerson['phone'];
                                	                $modelPerson->email = $registryBusinessContactPerson['email'];
                                	                $modelRegistryBusinessContactPerson->is_primary_contact = $registryBusinessContactPerson['is_primary_contact'];
                                	                $modelRegistryBusinessContactPerson->note = $registryBusinessContactPerson['note']; ?>
                                	                
                                	                <div class="mb-40 data-form">
                                                        <div class="row mt-10">
                                                            <div class="col-md-4 col-xs-6">
                                                            
                                                                <?= $form->field($modelPerson, '[' . $i .']first_name')->textInput([
                                                                    'maxlength' => true, 
                                                                    'placeholder' => Yii::t('app', 'First Name')
                                                                ]) ?>
                                                                
                                                            </div>
                                                            <div class="col-md-4 col-xs-6">
                                                            
                                                                <?= $form->field($modelPerson, '[' . $i .']last_name')->textInput([
                                                                    'maxlength' => true, 
                                                                    'placeholder' => Yii::t('app', 'Last Name')
                                                                ]) ?>
                                                                
                                                            </div>
                                                            <div class="col-md-4 col-xs-12">
                                                            
                                                            	<?= $form->field($modelRegistryBusinessContactPerson, '[' . $i . ']position')
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
                                                                        'placeholder' => Yii::t('app', 'Phone')
                                                                    ],
                                                                ]) ?>
                                                                
                                                            </div>
                                                            <div class="col-md-4 col-xs-6">
                                                            
                                                                <?= $form->field($modelPerson, '[' . $i .']email')->textInput([
                                                                    'class' => 'form-control',
                                                                    'placeholder' => 'Email'
                                                                ]) ?>
                                                                
                                                            </div>
                                                            <div class="col-md-4 col-xs-6">
                                                            
                                                            	<?= $form->field($modelRegistryBusinessContactPerson, '[' . $i .']is_primary_contact')->checkbox([
                                                            	    'class' => 'is-primary-checkbox'
                                                            	]) ?>
                                                            	
                                                            </div>
                                                        </div>
                                                
                                                        <div class="row">
                                                        	<div class="col-md-8 col-xs-12">
                                                        	
                                                                <?= $form->field($modelRegistryBusinessContactPerson, '[' . $i .']note')->textarea([
                                                                    'rows' => 2, 
                                                                    'placeholder' => Yii::t('app', 'Note')
                                                                ]) ?>
                                                                
                                                            </div>
                                                        </div>
                                                        
                                                        <?= Html::hiddenInput('RegistryBusinessContactPersonExisted[' . $i . ']', $registryBusinessContactPerson['person_id'], ['class' => 'deleted-contact']); ?>
                                                        
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
                            </div>
							
							<hr>
								
                            <div class="form-group">
                                <div class="row mt-30">
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
$modelPerson = new Person();
$modelRegistryBusinessContactPerson = new RegistryBusinessContactPerson(); ?>

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
            
            	<?= $form->field($modelRegistryBusinessContactPerson, '[index]is_primary_contact')->checkbox() ?>
            	
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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var indexCount = ' . count($dataRegistryBusinessContactPerson) . ';

    $(".main-form").find(".contact-person-position").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Position') . '",
        minimumResultsForSearch: "Infinity"
    });

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

        var lastData = $(".main-form").children(".data-form").last();
        
        if (lastData.find(".deleted-contact").length) {

            var inputName = lastData.find(".deleted-contact").attr("name");

            inputName = inputName.replace("Existed", "Deleted");
            lastData.find(".deleted-contact").attr("name", inputName);
            lastData.children().not(".deleted-contact").remove();
        } else {
        
            lastData.remove();    
        }

        lastData.removeClass("data-form").addClass("data-form-deleted");

        if (indexCount > 0) {

            indexCount--;
        }
        
        return false;
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script'](null, null, '.is-primary-checkbox') . $jscript); ?>
