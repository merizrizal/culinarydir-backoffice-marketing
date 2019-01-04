<?php

/* @var $this yii\web\View */
/* @var $model core\models\BusinessDelivery */

$this->title = 'Update ' . Yii::t('app', 'Delivery Methods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['business/view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delivery Methods'), 'url' => ['index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => $model->deliveryMethod->delivery_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="business-delivery-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBusiness' => $modelBusiness,
    ]) ?>

</div>
