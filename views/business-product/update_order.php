<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $dataBusinessProduct array */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'BusinessProduct',
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

$this->title = 'Update ' . Yii::t('app', 'Product Order') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['business/view-member', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['index', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Product Order');

echo $ajaxRequest->component(); ?>

<div class="business-product-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="business-product-form">

                    <?php
                    ActiveForm::begin([
                        'id' => 'business-product-form',
                        'action' => ['update-order', 'id' => $model->id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Product Order') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                    
                                    	<table class="table table-responsive table-striped table-border">
                                    		<tbody>
                                        		<tr>
                                        			<th><?= Yii::t('app', 'Name') ?></th>
                                        			<th><?= Yii::t('app', 'Product Category') ?></th>
                                        			<th><?= Yii::t('app', 'Price') ?></th>
                                        			<th><?= Yii::t('app', 'Not Active') ?></th>
                                        			<th><?= Yii::t('app', 'Order') ?></th>
                                        		</tr>

                                                <?php
                                                $productOrder = range(0, count($dataBusinessProduct));
                                                unset($productOrder[0]);
                                                
                                                if (!empty($dataBusinessProduct)):
                                                
                                                    foreach ($dataBusinessProduct as $businessProduct): ?>
        												
        												<tr>
                                                    		<td><?= $businessProduct['name']; ?></td>
                                                			<td><?= $businessProduct['businessProductCategory']['productCategory']['name'] ?></td>
                                                			<td><?= Yii::$app->formatter->asCurrency($businessProduct['price']); ?></td>
                                                			<td><?= Html::checkbox('not_active[' . $businessProduct['id'] . ']', $businessProduct['not_active']) ?></td>
                                                			<td>
                                                			
                                                				<?= Html::dropDownList('order[' . $businessProduct['id'] .']', $businessProduct['order'], $productOrder, [
                                                				    'class' => 'business-product-order',
                                                				    'style' => 'width: 100%'
                                                				]); ?>
                                                				
                                                			</td>
                                                    	</tr>
        
                                                    <?php
                                                    endforeach;
                                                else:
                                                    
                                                    echo '<div class="col-xs-12 mb-10">' . Yii::t('app', 'Data Not Available') . '</div>';
                                                endif; ?>
                                            
                                            </tbody>
										</table>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">

                                    <?php
                                    echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['index', 'id' => $model->id], ['class' => 'btn btn-default']); ?>

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
    $(".business-product-order").select2({
        theme: "krajee",
        minimumResultsForSearch: Infinity
    });
';

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>