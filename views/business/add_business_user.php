<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\User */
/* @var $modelBusiness core\models\Business */
/* @var $modelUser core\models\User */
/* @var $dataUser Array */
/* @var $modelPerson core\models\Person */
/* @var $modelBusinessContactPerson core\models\BusinessContactPerson */
/* @var $dataContactPerson Array */
/* @var $userLevel Array */
/* @var $selected String */
/* @var $userSource String */

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
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => 'Choose User', 'url' => ['choose-business-user', 'id' => $modelBusiness['id']]];
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
                        'action' => ['add-business-user', 'id' => $modelBusiness['id'], 'selected' => $selected, 'userSource' => $userSource],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_content">
                        	<div class="form-group">

                            	<?php
                            	if ($userSource == 'Contact-Person'):

                            	    foreach ($dataUser as $i => $user):

                            	        $dataBusinessContactPerson = $modelBusiness['businessContactPeople'][$i];

                            	        $isExist = !empty($dataBusinessContactPerson['person']['userPerson']);

                            	        $modelUser->user_level_id = $userLevel['id'];
                            	        $modelUser->email = $user['email'];
                            	        $modelUser->full_name = !empty($user['full_name']) ? $user['full_name'] : $user['first_name'] . ' ' . $user['last_name'];
                            	        $modelUser->not_active = !empty($user['not_active']) ? $user['not_active'] : false;
                            	        $modelUser->username = !empty($user['username']) ? $user['username'] : null; ?>

                                		<div class="row">
                                			<div class="col-xs-12">
                                				<label>User <?= $i + 1 ?></label>
                                				<div class="row mt-10">
                                					<div class="col-md-4 col-xs-6">

                        								<?= $form->field($modelUser, '[' . $i . ']email', [
                        								    'enableAjaxValidation' => !$isExist
                                                        ])->textInput(['maxlength' => true, 'placeholder' => 'email']) ?>

                                					</div>
                                					<div class="col-md-4 col-xs-6">

                                						<?= $form->field($modelUser, '[' . $i . ']username', [
                                                            'enableAjaxValidation' => !$isExist
                                                        ])->textInput(['maxlength' => true, 'placeholder' => 'username', 'disabled' => $isExist]) ?>

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
                            							<?= $form->field($modelUser, '[' . $i . ']password')->passwordInput(['maxlength' => true, 'placeholder' => 'password', 'disabled' => $isExist]) ?>
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

                                    	<?php
                                    	if ($isExist): ?>

                                        	<div class="row mt-10">
                                        		<div class="col-xs-12">
                                        			<strong class="text-danger"><?= Yii::t('app', 'This contact person has already added as user, do you want to merge it?') ?></strong>&nbsp;&nbsp;&nbsp;
                                        			<?= Html::checkbox('is_merge[' . $i . ']', false, ['label' => 'Merge']) ?>
                                        		</div>
                                        	</div>
                                        	<div class="row mb-10">
                                            	<div class="col-xs-12 mb-10">
                                            		<?= Html::label(Yii::t('app', 'Username')) . ' : ' . $dataBusinessContactPerson['person']['userPerson']['user']['username'] ?>
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

                                    	<?php
                                    	endif; ?>

                                    	<hr>

                            		<?php
                                	endforeach;
                            	elseif ($userSource == 'User-Asikmakan'):

                                    foreach ($dataContactPerson as $j => $contactPerson):

                                        $dataUser = $model[$j];

                                        $isExist = !empty($dataUser->userPerson->person->businessContactPeople[0]);

                            	        $modelPerson->first_name = $contactPerson['first_name'];
                            	        $modelPerson->last_name = $contactPerson['last_name'];
                            	        $modelPerson->phone = $contactPerson['phone'];
                            	        $modelPerson->email = $contactPerson['email'];

                            	        $modelBusinessContactPerson->position = !empty($contactPerson['position']) ? $contactPerson['position'] : null;
                            	        $modelBusinessContactPerson->is_primary_contact = !empty($contactPerson['is_primary_contact']) ? $contactPerson['is_primary_contact'] : null;
                            	        $modelBusinessContactPerson->note = !empty($contactPerson['note']) ? $contactPerson['note'] : null; ?>

                                        <div class="row mt-10">
                                            <div class="col-md-4 col-xs-6">

                                                <?= $form->field($modelPerson, '[' . $j .']first_name')->textInput([
                                                    'maxlength' => true,
                                                    'placeholder' => Yii::t('app', 'First Name')
                                                ]) ?>

                                            </div>
                                            <div class="col-md-4 col-xs-6">

                                                <?= $form->field($modelPerson, '[' . $j .']last_name')->textInput([
                                                    'maxlength' => true,
                                                    'placeholder' => Yii::t('app', 'Last Name')
                                                ]) ?>

                                            </div>
                                            <div class="col-md-4 col-xs-12">

                                            	<?= $form->field($modelBusinessContactPerson, '[' . $j . ']position')->dropDownList([
                                            	       'Owner' => 'Owner',
                                            	       'Manager' => 'Manager',
                                            	       'Staff' => 'Staff'
                                            	    ],
                                        	        [
                                    	               'prompt' => '',
                                        	           'class' => 'contact-person-position',
                                        	           'style' => 'width: 100%'
                                        	        ]); ?>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 col-xs-6">

                                                <?= $form->field($modelPerson, '[' . $j .']phone')->widget(MaskedInput::className(), [
                                                    'mask' => ['999-999-9999', '9999-999-9999', '9999-9999-9999', '9999-99999-9999'],
                                                    'options' => [
                                                        'class' => 'form-control',
                                                        'placeholder' => Yii::t('app', 'Phone')
                                                    ],
                                                ]) ?>

                                            </div>
                                            <div class="col-md-4 col-xs-6">

                                                <?= $form->field($modelPerson, '[' . $j .']email')->textInput([
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Email'
                                                ]) ?>

                                            </div>
                                            <div class="col-md-4 col-xs-6">
                                            	<?= $form->field($modelBusinessContactPerson, '[' . $j .']is_primary_contact')->checkbox() ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                        	<div class="col-md-8 col-xs-12">

                                                <?= $form->field($modelBusinessContactPerson, '[' . $j .']note')->textarea([
                                                    'rows' => 2,
                                                    'placeholder' => Yii::t('app', 'Note')
                                                ]) ?>

                                            </div>
                                        </div>

                                        <?php
                                    	if ($isExist): ?>

                                    		<div class="row mt-10 mb-10">
                                        		<div class="col-xs-12">
                                        			<strong class="text-danger"><?= Yii::t('app', 'This user has already added as contact person, do you want to merge it?') ?></strong>&nbsp;&nbsp;&nbsp;
                                        			<?= Html::checkbox('is_merge[' . $j . ']', false, ['label' => 'Merge']) ?>
                                        		</div>
                                        	</div>
                                        	<div class="row mb-10">
                                        		<div class="col-sm-3 col-xs-6">
                                            		<?= Html::label(Yii::t('app', 'Username')) ?><br>
                                	                <?= $dataUser['username']; ?>
                                                </div>
                                                <div class="col-sm-3 col-xs-6">
                                                	<?= Html::label(Yii::t('app', 'Email')) ?><br>
                                                	<?= $dataUser['email']; ?>
                                                </div>
                                                <div class="col-sm-3 col-xs-6">
                                            		<?= Html::label(Yii::t('app', 'Name')) ?><br>
                                            		<?= $dataUser['full_name']; ?>
                                            	</div>
                                            </div>

                                    	<?php
                                    	endif; ?>

                                		<hr>

                                	<?php
                                    endforeach;
                            	endif; ?>

                        	</div>
                    	</div>

                    	<div class="form-group">
                    		<div class="row mt-30">
                    			<div class="col-lg-12">

                                	<?php
                                	echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                	echo Html::a('<i class="fa fa-times"></i> Cancel', ['choose-business-user', 'id' => $modelBusiness['id']], ['class' => 'btn btn-default']); ?>

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

    $(".contact-person-position").select2({
        theme: "krajee",
        minimumResultsForSearch: "Infinity",
        placeholder: "' . Yii::t('app', 'Position') . '"
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>