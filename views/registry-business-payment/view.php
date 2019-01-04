<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusinessPayment */
/* @var $statusApproval backoffice\modules\marketing\controllers\RegistryBusinessController */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryBusinessPayment',
]);

$ajaxRequest->view();

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

$this->title = $model->paymentMethod->payment_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Data Application'), 'url' =>  ['registry-business/index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = ['label' => $model->registryBusiness->name, 'url' => ['registry-business/view-' . strtolower($statusApproval), 'id' => $model->registry_business_id, 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['index', 'id' => $model->registry_business_id, 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(); ?>

<div class="registry-business-payment-view">

    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">

                    <?= Html::a('<i class="fa fa-upload"></i> Create', ['create', 'id' => $model->registry_business_id, 'statusApproval' => $statusApproval], ['class' => 'btn btn-success']) ?>

                    <?= Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['update', 'id' => $model->id, 'statusApproval' => $statusApproval], ['class' => 'btn btn-primary']) ?>

                    <?= Html::a('<i class="fa fa-trash-alt"></i> Delete',['delete', 'id' => $model->id, 'statusApproval' => $statusApproval], [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',
                            'data-not-ajax' => 1,
                            'model-id' => $model->id,
                            'model-name' => $model->paymentMethod->payment_name,
                        ]) ?>

                    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['index', 'id' => $model->registry_business_id, 'statusApproval' => $statusApproval], ['class' => 'btn btn-default']) ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

                        <?= DetailView::widget([
                            'model' => $model,
                            'options' => [
                                'class' => 'table'
                            ],
                            'attributes' => [
                                'paymentMethod.payment_name',
                                'note:ntext',
                                [
                                    'attribute' => 'is_active',
                                    'format' => 'raw',
                                    'value' => Html::checkbox('is_active', $model->is_active, ['value' => $model->is_active, 'disabled' => 'disabled']),
                                ],
                            ],
                        ]) ?>

                </div>
            </div>
        </div>
    </div>

</div>

<?php
$modalDialog = new ModalDialog([
    'clickedComponent' => 'a#delete',
    'modelAttributeId' => 'model-id',
    'modelAttributeName' => 'model-name',
]);

$modalDialog->theScript(false);

echo $modalDialog->renderDialog();

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = Yii::$app->params['checkbox-radio-script']()
    . '$(".iCheck-helper").parent().removeClass("disabled");
';

$this->registerJs($jscript); ?>