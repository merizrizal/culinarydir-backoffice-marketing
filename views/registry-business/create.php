<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */

$this->title = 'Create ' . Yii::t('app', 'Registry Business');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Membership'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => !empty($this->params['type']) ? Yii::t('app', 'My Registry') : Yii::t('app', 'All Registry'), 'url' => (empty($this->params['type']) ? ['index'] : ['index', 'type' => $this->params['type']])];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registry-business-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelRegistryBusinessCategory' => $modelRegistryBusinessCategory,
        'dataRegistryBusinessCategory' => $dataRegistryBusinessCategory,
        'modelRegistryBusinessProductCategory' => $modelRegistryBusinessProductCategory,
        'dataRegistryBusinessProductCategoryParent' => $dataRegistryBusinessProductCategoryParent,
        'dataRegistryBusinessProductCategoryChild' => $dataRegistryBusinessProductCategoryChild,
        'modelRegistryBusinessHour' => $modelRegistryBusinessHour,
        'dataRegistryBusinessHour' => $dataRegistryBusinessHour,
        'modelRegistryBusinessFacility' => $modelRegistryBusinessFacility,
        'dataRegistryBusinessFacility' => $dataRegistryBusinessFacility,
        'modelRegistryBusinessImage' => $modelRegistryBusinessImage,
        'dataRegistryBusinessImage' => $dataRegistryBusinessImage,
        'type' => $this->params['type'],
    ]) ?>

</div>