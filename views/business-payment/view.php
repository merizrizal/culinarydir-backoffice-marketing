<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\BusinessPayment */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'BusinessPayment',
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $model->business->name, 'url' => ['business/view-member', 'id' => $model->business_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['index', 'id' => $model->business_id]];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(); ?>

<div class="business-payment-view">

    <div class="row">
        <div class="col-xs-12">
            <div class="x_panel">

                <div class="x_content">

                    <?= Html::a('<i class="fa fa-upload"></i> Create', ['create', 'id' => $model->business_id], ['class' => 'btn btn-success']) ?>

                    <?= Html::a('<i class="fa fa-pencil-alt"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

                    <?= Html::a('<i class="fa fa-trash-alt"></i> Delete', ['delete', 'id' => $model->id], [
                        'id' => 'delete',
                        'class' => 'btn btn-danger',
                        'data-not-ajax' => 1,
                        'model-id' => $model->id,
                        'model-name' => $model->paymentMethod->payment_name,
                    ]) ?>

                    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['index', 'id' => $model->business_id], ['class' => 'btn btn-default']) ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => [
                            'class' => 'table'
                        ],
                        'attributes' => [
                            'paymentMethod.payment_name',
                            'note:ntext',
                            'description:ntext',
                            [
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => Html::checkbox('is_active', $model->is_active, ['value' => $model->is_active, 'disabled' => 'disabled']),
                            ]
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