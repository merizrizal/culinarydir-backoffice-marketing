<?php

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusinessPayment */
/* @var $modelRegistryBusiness core\models\RegistryBusiness */
/* @var $statusApproval string */

$this->title = 'Update ' . Yii::t('app', 'Payment Methods');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Data Application'), 'url' =>  ['registry-business/index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = ['label' => $modelRegistryBusiness['name'], 'url' => ['registry-business/view-' . strtolower($statusApproval), 'id' => $modelRegistryBusiness['id'], 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['index', 'id' => $modelRegistryBusiness['id'], 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = ['label' => $model->paymentMethod->payment_name, 'url' => ['view', 'id' => $model->id, 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="registry-business-payment-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelRegistryBusiness' => $modelRegistryBusiness,
        'statusApproval' => $statusApproval,
    ]) ?>

</div>
