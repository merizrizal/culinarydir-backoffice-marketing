<?php

/* @var $this yii\web\View */
/* @var $model core\models\BusinessPayment */

$this->title = 'Update ' . Yii::t('app', 'Payment Methods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['business/view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => $model->paymentMethod->payment_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="business-payment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBusiness' => $modelBusiness,
    ]) ?>

</div>
