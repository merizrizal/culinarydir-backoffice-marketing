<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\Business */

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

$this->title = 'Choose User';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' =>  ['member']];
$this->params['breadcrumbs'][] = ['label' => $model['name'], 'url' => ['view-member', 'id' => $model['id']]];
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

                if ($(".contact-person").children().hasClass("checked")) {
                    
                    $("#wizard-create-application-p-1").html($(".contact-person-steps").html());
                    $("#wizard-create-application-t-1").children(".desc").html("' . Yii::t('app', 'Contact Person') . '"); ' .

                    Yii::$app->params['checkbox-radio-script']() . '
                } else if ($(".user-asikmakan").children().hasClass("checked")) {
                    
                    $("#wizard-create-application-p-1").html($(".user-asikmakan-steps").html());
                    $("#wizard-create-application-t-1").children(".desc").html("User Asikmakan");
                }
            } else if (priorIndex == lastCount) {
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").removeClass("buttonDisabled");
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#next\"]").parent().show();
    
                $("#wizard-create-application.wizard > .actionBar").find("a[href=\"#finish\"]").parent().hide();
            }
        },
        onFinished: function(event, currentIndex) {
    
            $("#business-form").trigger("submit");
        },
        labels: {
            finish: "<i class=\"fa fa-save\"></i> Save",
            next: "<i class=\"fa fa-angle-double-right\"></i> Next",
            previous: "<i class=\"fa fa-angle-double-left\"></i> Previous"
        }
    });
';

$this->registerJs($jscript); ?>

<div class="business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="business-form">
                    <div class="x_content">
                    
                    	<?php
                        ActiveForm::begin([
                            'id' => 'business-form',
                            'action' => ['add-business-user', 'id' => $model['id']],
                            'options' => [

                            ],
                            'fieldConfig' => [
                                'template' => '{input}{error}',
                            ]
                        ]); ?>

                            <div id="wizard-create-application">
                                <h1><?= Yii::t('app', 'User Source') ?></h1>
                                <div>
                                
                                    <?= Html::radioList('userSource', null, ['Contact-Person' => Yii::t('app', 'Contact Person'), 'User-Asikmakan' => 'User Asikmakan'], [
                                        'item' => function ($index, $label, $name, $checked, $value) {
                                        
                                            return '
                                                <div class="col-xs-12 col-sm-4">
                                                    <label class="' . strtolower($value) . '">' .
                                                        Html::radio($name, $checked, ['value' => $value]) . ' ' . $label . '
                                                    </label>
                                                </div>';
                                        }
                                    ]) ?>
                                    
                                </div>
                                <h1></h1>
                                <div></div>
                            </div>
                            
                        <?php
                        ActiveForm::end(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="contact-person-steps hide">

	<?php
    if (!empty($model['businessContactPeople'])):
    
        foreach ($model['businessContactPeople'] as $i => $dataBusinessContactPerson):
        	
            $is_primary = !empty($dataBusinessContactPerson['is_primary_contact']) ? ' - ' . Yii::t('app', 'Primary Contact') : ''; ?>
            
            <div class="row mb-20">
        		<div class="col-xs-12 mb-10">
        			<strong><?= Yii::t('app', 'Contact') . ' ' . ($i + 1) . $is_primary ?></strong>
        			<?= Html::checkbox('selectedUser[' . $i . ']', false, ['value' => $dataBusinessContactPerson['id'], 'label' => 'Add This User']) ?>
            	</div>
        		<div class="col-sm-3 col-xs-6 mb-10">
            		<?= Html::label(Yii::t('app', 'Name')) ?><br>
	                <?= $dataBusinessContactPerson['person']['first_name'] . ' ' . $dataBusinessContactPerson['person']['last_name']; ?>
                </div>
                <div class="col-sm-3 col-xs-6 mb-10">
                	<?= Html::label(Yii::t('app', 'Position')) ?><br>
                	<?= $dataBusinessContactPerson['position']; ?>
                </div>
                <div class="col-sm-3 col-xs-6">
            		<?= Html::label(Yii::t('app', 'Email')) ?><br>
            		<?= !empty($dataBusinessContactPerson['person']['email']) ? $dataBusinessContactPerson['person']['email'] : '-'; ?>
            	</div>
            	<div class="col-sm-3 col-xs-6">
            		<?= Html::label(Yii::t('app', 'Phone')) ?><br>
            		<?= !empty($dataBusinessContactPerson['person']['phone']) ? $dataBusinessContactPerson['person']['phone'] : '-'; ?>
            	</div>
            </div>
            
            <div class="row mb-20">
            	<div class="col-xs-12">
            		<?= Html::label(Yii::t('app', 'Note')) . '<br>'; ?>
            		<?= !empty($dataBusinessContactPerson['note']) ? $dataBusinessContactPerson['note'] : '-'; ?>
            	</div>
            </div>
            
            <hr>
            
        <?php
        endforeach;
    else: ?>
     	
     	<div class="row mb-20">
     		<div class="col-xs-12">
	  			<?= Yii::t('app', 'Data Not Available') ?>
 		  	</div>
     	</div>
     	
     	<hr>
     	
    <?php
    endif; ?>
    
</div>

<div class="user-asikmakan-steps hide">
	Asikmakan
</div>

<?php
$this->registerCssFile(Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/demo/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile(Yii::$app->urlManager->baseUrl . '/media/css/jquery.steps.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$cssscript = '
    .wizard > .content > .body ul > li {
        display: block;
    }
';

$this->registerCss($cssscript);

$this->registerJsFile(Yii::$app->urlManager->baseUrl . '/media/plugins/jquery-steps/build/jquery.steps.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJs(Yii::$app->params['checkbox-radio-script']()); ?>