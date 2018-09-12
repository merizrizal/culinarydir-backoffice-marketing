<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BusinessProduct */

$this->title = 'Create ' . Yii::t('app', 'Product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['business/view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="business-product-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBusiness' => $modelBusiness,
    ]) ?>

</div>