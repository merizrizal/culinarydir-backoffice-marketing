<?php

use yii\helpers\Html;
use kartik\file\FileInput;
use kartik\number\NumberControl;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\BusinessProduct */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelBusiness core\models\Business */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'BusinessProduct',
]);

$ajaxRequest->form();

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) :
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

echo $ajaxRequest->component(); ?>

<div class="row">
    <div class="col-sm-12">
        <div class="x_panel">
            <div class="business-product-form">

                <?php 
                $form = ActiveForm::begin([
                    'id' => 'business-product-form',
                    'action' => $model->isNewRecord ? ['create', 'id' => $modelBusiness['id']] : ['update', 'id' => $model->id, 'bid' => $modelBusiness['id']],
                    'options' => [
    
                    ],
                    'fieldConfig' => [
                        'parts' => [
                            '{inputClass}' => 'col-lg-12'
                        ],
                        'template' => '
                            <div class="row">
                                <div class="col-lg-3">
                                    {label}
                                </div>
                                <div class="col-lg-6">
                                    <div class="{inputClass}">
                                        {input}
                                    </div>
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

                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                        <?= $form->field($model, 'price', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-4'
                            ],
                        ])->widget(NumberControl::className(), [
                            'maskedInputOptions' => Yii::$app->params['maskedInputOptions']
                        ]) ?>

                        <?= $form->field($model, 'image')->widget(FileInput::classname(), [
                            'options' => [
                                'accept' => 'image/*'
                            ],
                            'pluginOptions' => [
                                'initialPreview' => [
                                    Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/business_product/', 'image', 200, 200), ['class'=>'file-preview-image']),
                                ],
                                'showRemove' => false,
                                'showUpload' => false,
                            ]
                        ]); ?>

                        <?= $form->field($model, 'not_active')->checkbox(['value' => true], false) ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                
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
</div><!-- /.row -->

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJs(Yii::$app->params['checkbox-radio-script']()); ?>