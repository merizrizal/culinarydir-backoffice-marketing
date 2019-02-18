<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\BusinessProductCategory;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $modelBusinessProduct core\models\BusinessProduct */
/* @var $productCategoryId String */
/* @var $selected string */

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

$this->title = 'Update ' . Yii::t('app', 'Product Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['business/member']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['business/view-member', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['index', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(); ?>

<div class="business-product-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="business-product-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'business-product-form',
                        'action' => ['update-selected-menu', 'id' => $model->id, 'selected' => $selected],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= 'Update ' . Yii::t('app', 'Product Category') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <div class="row mt-10">
                                            <div class="col-md-6 col-xs-12">
                                            
                                            	<?php
                                            	if (!empty($productCategoryId)) {
                                            	   
                                            	    $modelBusinessProduct->business_product_category_id = $productCategoryId;
                                            	}
                                            
                                            	echo $form->field($modelBusinessProduct, 'business_product_category_id')->dropDownList(
                                            	    ArrayHelper::map(
                                            	        BusinessProductCategory::find()
                                                	        ->joinWith(['productCategory'])
                                                	        ->andWhere(['OR', ['product_category.type' => 'Menu'], ['product_category.type' => 'Specific-Menu']])
                                                	        ->andWhere(['business_product_category.business_id' => $model['id']])
                                                	        ->andWhere(['business_product_category.is_active' => true])
                                                	        ->orderBy('business_product_category.order')
                                                	        ->asArray()->all(),
                                            	        'id',
                                            	        function($data) {
                                            	            
                                            	            return $data['productCategory']['name'];
                                            	        }
                                        	        ),
                                        	        [ 
                                    	               'prompt' => Yii::t('app', 'Product Category'),
                                        	           'class' => 'product-category',
                                        	           'style' => 'width: 100%'
                                        	        ]); ?>
                                            	
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
							
							<hr>
								
                            <div class="form-group">
                                <div class="row mt-30">
                                    <div class="col-lg-12">

                                        <?php
                                        echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                        echo Html::a('<i class="fa fa-times"></i> Cancel', ['index', 'id' => $model->id], ['class' => 'btn btn-default']); ?>

                                    </div>
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
$jscript = '
    $(".product-category").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Product Category') . '",
        minimumResultsForSearch: "Infinity"
    });
';

$this->registerJs($jscript); ?>