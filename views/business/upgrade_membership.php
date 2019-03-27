<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\MembershipType;

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

$this->title = Yii::t('app', 'Upgrade Membership') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['member']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-member', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Upgrade Membership');

echo $ajaxRequest->component(); ?>

<div class="business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="business-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'business-form',
                        'action' => ['upgrade-membership', 'id' => $model->id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Upgrade Membership') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="row">
                                <div class="col-md-12">
                                
                                    <?= $form->field($model, 'membership_type_id', [
                                        'template' => '
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    {input}
                                                    {error}
                                                </div>
                                            </div>
                                        ',
                                    ])->radioList(
                                        ArrayHelper::map(
                                            MembershipType::find()->orderBy('order')->asArray()->all(),
                                            'id',
                                            function($data) {
                                                
                                                return $data['name'];
                                            }
                                        ), [
                                        'item' => function ($index, $label, $name, $checked, $value) {

                                            return '
                                                <div class="col-xs-12 col-sm-4">
                                                    <label>' .
                                                        Html::radio($name, $checked, ['value' => $value]) . ' ' . $label . '
                                                    </label>
                                                </div>';
                                        }
                                    ]) ?>
                                    
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-12">

                                    <?php
                                    echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['view-member', 'id' => $model->id], ['class' => 'btn btn-default']); ?>

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

$this->registerJs(Yii::$app->params['checkbox-radio-script']()); ?>