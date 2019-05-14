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
/* @var $modelPerson core\models\Person */
/* @var $modelBusinessContactPerson core\models\BusinessContactPerson */
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

                            	    foreach ($modelBusiness['businessContactPeople'] as $i => $dataContactPerson):

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
                                	endforeach;
                            	elseif ($userSource == 'User-Asikmakan'):

                        	        if (!empty($model)):

                                        foreach ($model as $j => $dataUser):

                                            $userFullname = explode(' ', $dataUser['full_name']);

                                	        $modelPerson->first_name = $userFullname[0];
                                	        $modelPerson->last_name = $userFullname[1];
                                	        $modelPerson->phone = $dataUser['userPerson']['person']['phone'];
                                	        $modelPerson->email = $dataUser['userPerson']['person']['email'];

                                	        if (!empty($dataUser->userPerson->person->businessContactPeople)) {

                                	            foreach ($dataUser->userPerson->person->businessContactPeople as $dataBusinessContactPerson) {

                                	                if ($dataBusinessContactPerson['business_id'] == $modelBusiness['id']) {

                                	                    $modelBusinessContactPerson = $dataBusinessContactPerson;
                                	                }
                                	            }
                                	        } ?>

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
                                        endforeach;
                                    endif;
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