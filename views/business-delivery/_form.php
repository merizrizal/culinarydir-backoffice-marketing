<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\DeliveryMethod;

/* @var $this yii\web\View */
/* @var $model core\models\BusinessDelivery */
/* @var $form yii\widgets\ActiveForm */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'BusinessDelivery',
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

echo $ajaxRequest->component() ?>

<div class="row">
    <div class="col-xs-12">
        <div class="x_panel">
            <div class="business-delivery-form">

                <?php $form = ActiveForm::begin([
                        'id' => 'business-delivery-form',
                        'action' => $model->isNewRecord ? ['create', 'id' => $modelBusiness['id']] : ['update', 'id' => $model->id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'parts' => [
                                '{inputClass}' => 'col-lg-6'
                            ],
                            'template' => '
                                <div class="row">
                                    <div class="col-lg-3">
                                        {label}
                                    </div>
                                    <div class="{inputClass}">
                                        {input}
                                    </div>
                                    <div class="col-lg-3">
                                        {error}
                                    </div>
                                </div>',
                        ]
                ]); ?>

                    <div class="x_title">

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                
                                    <?php
                                    if (!$model->isNewRecord)
                                        echo Html::a('<i class="fa fa-upload"></i> Create', ['create', 'id' => $modelBusiness['id']], ['class' => 'btn btn-success']); ?>
                                
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="x_content">

                        <?= $form->field($model, 'delivery_method_id')->dropDownList(
                            ArrayHelper::map(
                                DeliveryMethod::find()
                                    ->andWhere(['not_active' => false])
                                    ->orderBy(['id' => SORT_ASC])
                                    ->asArray()->all(),
                                'id',
                                function ($data) {
                                    return $data['delivery_name'];
                                }
                            ),
                        [
                            'prompt' => '',
                            'style' => 'width: 100%'
                        ]) ?>
                    
                        <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
                        
                        <?= $form->field($model, 'is_active')->checkbox(['value' => true], false) ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-offset-3 col-lg-6">
                                
                                    <?php
                                    $icon = '<i class="fa fa-save"></i> ';
                                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['index', 'id' => $modelBusiness['id']], ['class' => 'btn btn-default']); ?>
                                
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

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#businessdelivery-delivery_method_id").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Product Category') . '"
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>