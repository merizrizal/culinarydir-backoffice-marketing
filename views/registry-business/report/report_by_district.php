<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel core\models\search\BusinessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'Business',
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

$this->title = 'Report by Kecamatan';
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component() ?>

<!--<div class="business-index">-->
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="registry-business-form">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'business-form',
                                'action' => ['registry-business/report-by-district'],
                                'options' => [
                                ],
                            ]); ?>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-1">
                                            <label class="control-label" for="tanggal_from">Tanggal</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <?= DatePicker::widget([
                                                'name' => 'tanggal_from',
                                                'name2' => 'tanggal_to',
                                                'type' => DatePicker::TYPE_RANGE,
                                                'separator' => ' - ',
                                                'options' => [
                                                    'id' => 'tanggal_from',
                                                    'placeholder' => 'From'
                                                ],
                                                'options2' => [
                                                    'id' => 'tanggal_to',
                                                    'placeholder' => 'To'
                                                ],
                                                'pluginOptions' => Yii::$app->params['datepickerOptions'],
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="x_content">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-offset-1 col-lg-6 col-md-offset-1 col-md-6 col-sm-offset-1 col-sm-6 col-xs-offset-1 col-xs-6">
                                                <?= Html::submitButton('<i class="fa fa-check"></i> Tampilkan Laporan', ['class' => 'btn btn-primary']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 text-right">
                            <?= $tanggal; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="example">
                            <?php
                                if (!empty($data)): ?>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Kecamatan</th>
                                            <th>Wilayah</th>
                                            <th>Member</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($data as $d): ?>
                                                <tr>
                                                    <td></td>
                                                    <?php
                                                        foreach($d as $name): ?>
                                                            <td><?= $name['district']['name'] ?></td>
                                                            <td><?= $name['district']['region']['name'] ?></td>
                                                    <?php
                                                            break;
                                                        endforeach; ?>
                                                    <td><?= count($d) ?></td>
                                                </tr>
                                        <?php
                                            endforeach; ?>
                                    </tbody>
                            <?php else: ?>
                                <tbody>
                                    <tr>
                                        <td class="text-center">Silahkan Melakukan Filter Data Dahulu</td>
                                    </tr>
                                 </tbody>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--</div>-->

<?php
$jscript = '
    $("#tanggal_from").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    $("#tanggal_to").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
';

$this->registerJs($jscript);?>
