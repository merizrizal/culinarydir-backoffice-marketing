<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $modelUser core\models\User */
/* @var $userLevel Array */
/* @var $selected String */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

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
						
					<?php
                    $form = ActiveForm::begin([
                        'id' => 'business-form',
                        'action' => ['add-business-user', 'id' => $model['id'], 'selected' => $selected],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>
                    
                        <div class="x_content">
                        	<div class="form-group">
                    	
                            	<?php
                        	    foreach ($model['businessContactPeople'] as $i => $dataContactPerson):
                        	        
                        	        $modelUser->user_level_id = $userLevel['id'];
                        	        $modelUser->email = $dataContactPerson['person']['email'];
                        	        $modelUser->full_name = $dataContactPerson['person']['first_name'] . ' ' . $dataContactPerson['person']['last_name']; ?>
                        			
                            		<div class="row">
                            			<div class="col-xs-12">
                            				<label>User <?= $i + 1 ?></label>
                            				<div class="row mt-10">
                            					<div class="col-md-4 col-xs-6">
                    								
                    								<?= $form->field($modelUser, '[' . $i . ']email', [
                                                        'enableAjaxValidation' => true
                                                    ])->textInput(['maxlength' => true, 'placeholder' => 'email']) ?>
                    								
                            					</div>
                            					<div class="col-md-4 col-xs-6">
                            						
                            						<?= $form->field($modelUser, '[' . $i . ']username', [
                                                        'enableAjaxValidation' => true
                                                    ])->textInput(['maxlength' => true, 'placeholder' => 'username']) ?>
                            						
                            					</div>
                            					<div class="col-md-4 col-xs-12">
                            						
                            						<?= $form->field($modelUser, '[' . $i . ']user_level_id')->dropDownList([$userLevel['id'] => $userLevel['nama_level']], [
                                                        'style' => 'width: 100%',
                                                        'class' => 'user-level-field',
                                                    ]) ?>
                            						
                            					</div>
                            				</div>
                            				<div class="row mt-10">
                            					<div class="col-md-4 col-xs-6">
                        							<?= $form->field($modelUser, '[' . $i . ']password')->passwordInput(['maxlength' => true, 'placeholder' => 'password']) ?>
                            					</div>
                            					<div class="col-md-4 col-xs-6">
                        							<?= $form->field($modelUser, '[' . $i . ']full_name')->textInput(['maxlength' => true]) ?>
                            					</div>
                            					<div class="col-md-4 col-xs-6">
                        							<?= $form->field($modelUser, '[' . $i . ']not_active')->checkbox(['value' => true]) ?>
                            					</div>
                            				</div>
                            			</div>
                            		</div>
                                	
                                	<hr>
                            	
                        		<?php
                            	endforeach; ?>
                            	
                        	</div>
                    	</div>
                    	
                    	<div class="form-group">
                    		<div class="row mt-30">
                    			<div class="col-lg-12">
                    	
                                	<?php
                                	echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                	echo Html::a('<i class="fa fa-times"></i> Cancel', ['choose-business-user', 'id' => $model['id']], ['class' => 'btn btn-default']); ?>

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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".user-level-field").select2({
        theme: "krajee",
        minimumResultsForSearch: "Infinity"
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>