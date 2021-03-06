<?php

/* @var $this yii\web\View */
/* @var $model core\models\BusinessPromo */
/* @var $modelBusiness core\models\Business */

$this->title = 'Create ' . Yii::t('app', 'Promo');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $modelBusiness['name'], 'url' => ['business/view-member', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Promo'), 'url' => ['index', 'id' => $modelBusiness['id']]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="business-promo-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelBusiness' => $modelBusiness,
    ]) ?>

</div>