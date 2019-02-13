<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $modelBusinessProductCategory core\models\BusinessProductCategory */
/* @var $dataBusinessProductCategory array */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'Business',
]);

$ajaxRequest->form();

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) {
    
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);
    
    $notif->theScript();
    echo $notif->renderDialog();
}

$this->title = 'Update ' . Yii::t('app', 'Product Category Order') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['business/view-member', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['business-product/index', 'id' => $model['id']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Category'), 'url' => ['index', 'id' => $model['id']]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Product Category Order');

echo $ajaxRequest->component(); ?>

<div class="business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="business-form">

                    <?php
                    ActiveForm::begin([
                        'id' => 'business-form',
                        'action' => ['update-order', 'id' => $model->id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Product Category') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <div class="row">

                                            <?php
                                            $productCategoryOrder = range(0, count($dataBusinessProductCategory));
                                            unset($productCategoryOrder[0]);
                                            
                                            if (!empty($dataBusinessProductCategory)):
                                            
                                                foreach ($dataBusinessProductCategory as $businessProductCategory):
                                                    
                                                    $modelBusinessProductCategory->order = $businessProductCategory['order'];
                                                    echo Html::hiddenInput('product_category_id[' . $businessProductCategory['id'] . ']', $businessProductCategory['product_category_id']); ?>
    
                                                    <div class="col-xs-6 col-sm-4">
                                                        <div class="thumbnail">
                                                    		<div class="row mt-10 mb-10">
                                                				<div class="col-xs-12">
                                                        			<?= $businessProductCategory['productCategory']['name']; ?>
                                                        		</div>
                                                    		</div>
                                                    		<div class="row mb-10">
                                                    			<div class="col-xs-8">
                                                        			<?= Html::checkbox('is_active[' . $businessProductCategory['id'] . ']', $businessProductCategory['is_active'], ['label' => Yii::t('app', 'Is Active')]) ?>
                                                        		</div>
                                                    			<div class="col-xs-4">
                                                    				<?= Html::dropDownList('order[' . $businessProductCategory['id'] .']', $businessProductCategory['order'], $productCategoryOrder, ['class' => 'product-category-order']); ?>
                                                        		</div>
                                                        	</div>
                                                        </div>
                                                    </div>
    
                                                <?php
                                                endforeach;
                                            else:
                                            
                                                echo '<div class="col-xs-12 mb-10">' . Yii::t('app', 'Data Not Available') . '</div>';
                                            endif; ?>
                                                

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">

                                    <?php
                                    echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['index', 'id' => $model['id']], ['class' => 'btn btn-default']); ?>

                                </div>
                            </div>

                        </div>

                    <?php
                    ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".product-category-order").select2({
        theme: "krajee",
        minimumResultsForSearch: Infinity
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>