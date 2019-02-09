<?php

/* @var $this yii\web\View */
/* @var $model core\models\BusinessProductCategory */
/* @var $modelBusiness core\models\Business */

$this->title = 'Create ' . Yii::t('app', 'Product Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['business/view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['business-product/index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Category'), 'url' => ['index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="business-product-category-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBusiness' => $modelBusiness
    ]) ?>

</div>