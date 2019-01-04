<?php

/* @var $this yii\web\View */
/* @var $model core\models\BusinessPayment */

$this->title = 'Create ' . Yii::t('app', 'Payment Methods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['business/view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="business-payment-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBusiness' => $modelBusiness,
    ]) ?>

</div>