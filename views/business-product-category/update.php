<?php

/* @var $this yii\web\View */
/* @var $model core\models\BusinessProductCategory */
/* @var $modelBusiness core\models\Business */

$this->title = 'Update ' . Yii::t('app', 'Product Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['business/view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['business-product/index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Category'), 'url' => ['index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => $model->productCategory->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update'; ?>

<div class="business-product-category-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBusiness' => $modelBusiness
    ]) ?>

</div>
