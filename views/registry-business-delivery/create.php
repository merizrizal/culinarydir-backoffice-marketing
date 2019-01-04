<?php

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusinessDelivery */
/* @var $modelRegistryBusiness core\models\RegistryBusiness */
/* @var $statusApproval backoffice\modules\marketing\controllers\RegistryBusinessController */

$url = $statusApproval == 'pndg' ? 'registry-business/view-pndg' : 'registry-business/view-icorct';

$this->title = 'Create ' . Yii::t('app', 'Delivery Methods');
$this->params['breadcrumbs'][] = ['label' => $modelRegistryBusiness['name'], 'url' => [$url, 'id' => $modelRegistryBusiness['id'], 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delivery Methods'), 'url' => ['index', 'id' => $modelRegistryBusiness['id'], 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="registry-business-delivery-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelRegistryBusiness' => $modelRegistryBusiness,
        'statusApproval' => $statusApproval,
    ]) ?>

</div>