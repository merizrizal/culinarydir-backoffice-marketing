<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\User;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $userLevel String */

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

$this->title = 'Add User';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' =>  ['member']];
$this->params['breadcrumbs'][] = ['label' => $model['name'], 'url' => ['view-member', 'id' => $model['id']]];
$this->params['breadcrumbs'][] = ['label' => 'Choose User', 'url' => ['choose-business-user', 'id' => $model['id']]];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(); ?>

<div class="business-update">
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="business-form">
					<div class="x_content">
						
						<?php
                        $form = ActiveForm::begin([
                            'id' => 'business-form',
                            'action' => ['add-business-user'],
                            'options' => [

                            ],
                            'fieldConfig' => [
                                'template' => '{input}{error}',
                            ]
                        ]);
                        	
                        	
                    	    foreach ($model['businessContactPeople'] as $dataContactPerson):
                    	        
                    	        $newModelUser = new User();
                    	        $newModelUser->user_level_id = $userLevel;
                    	        $newModelUser->email = $dataContactPerson['person']['email'];
                    	        $newModelUser->full_name = $dataContactPerson['person']['first_name'] . ' ' . $dataContactPerson['person']['last_name']; ?>
                    	
                            	<div class="form-group">
                            		<div class="row">
                            			<div class="col-xs-12">
                            				<div class="row mt-10">
                            					<div class="col-md-4 col-xs-6">
                    								
                    								<?= $form->field($newModelUser, 'email', [
                                                        'enableAjaxValidation' => true
                                                    ])->textInput(['maxlength' => true, 'placeholder' => 'email']) ?>
                    								
                            					</div>
                            					<div class="col-md-4 col-xs-6">
                            						
                            						<?= $form->field($model, 'username', [
                                                        'enableAjaxValidation' => true
                                                    ])->textInput(['maxlength' => true, 'placeholder' => 'username']) ?>
                            						
                            					</div>
                            					<div class="col-md-4 col-xs-12">
                            						
                            					</div>
                            				</div>
                            			</div>
                            		</div>
                            	</div>
                            	
                            	<hr>
                        	
                    		<?php
                        	endforeach;
                        	
                        ActiveForm::end(); ?>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>