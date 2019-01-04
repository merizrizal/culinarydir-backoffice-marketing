<?php

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusinessPayment */
/* @var $modelRegistryBusiness core\models\RegistryBusiness */
/* @var $statusApproval backoffice\modules\marketing\controllers\RegistryBusinessController */

$url = $statusApproval == 'pndg' ? 'registry-business/view-pndg' : 'registry-business/view-icorct';

$this->title = 'Create ' . Yii::t('app', 'Payment Methods');
$this->params['breadcrumbs'][] = ['label' => $modelRegistryBusiness['name'], 'url' => [$url, 'id' => $modelRegistryBusiness['id'], 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Methods'), 'url' => ['index', 'id' => $modelRegistryBusiness['id'], 'statusApproval' => $statusApproval]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="registry-business-payment-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelRegistryBusiness' => $modelRegistryBusiness,
        'statusApproval' => $statusApproval,
    ]) ?>

</div>