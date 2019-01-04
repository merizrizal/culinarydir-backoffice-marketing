<?php

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusinessDelivery */
/* @var $modelRegistryBusiness core\models\RegistryBusiness */
/* @var $statusApproval backoffice\modules\marketing\controllers\RegistryBusinessController */

$url = $statusApproval == 'pndg' ? 'registry-business/view-pndg' : 'registry-business/view-icorct';

$this->title = 'Update ' . Yii::t('app', 'Delivery Methods');
$this->params['breadcrumbs'][] = ['label' => $modelRegistryBusiness['name'], 'url' => [$url, 'id' => $model->registry_business_id, 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delivery Methods'), 'url' => ['index', 'id' => $model->registry_business_id, 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = ['label' => $model->deliveryMethod->delivery_name, 'url' => ['view', 'id' => $model->id, 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="registry-business-delivery-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelRegistryBusiness' => $modelRegistryBusiness,
        'statusApproval' => $statusApproval,
    ]) ?>

</div>
