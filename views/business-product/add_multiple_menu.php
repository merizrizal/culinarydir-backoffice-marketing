<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\number\NumberControl;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use core\models\BusinessProduct;
use core\models\BusinessProductCategory;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $modelBusinessProduct core\models\BusinessProduct */
/* @var $dataBusinessProduct Array */

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

$this->title = 'Create ' . Yii::t('app', 'Product');
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
                        'action' => ['add-multiple-menu', 'id' => $model->id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Product') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <div class="main-form">
                                	
                                        	<?php
                                	        if (!empty($dataBusinessProduct)):
                                	            
                                	            foreach ($dataBusinessProduct as $i => $businessProduct):
                                	                
                                	                $modelBusinessProduct->business_id = $businessProduct['business_id'];
                                	                $modelBusinessProduct->name = $businessProduct['name'];
                                	                $modelBusinessProduct->description = $businessProduct['description'];
                                	                $modelBusinessProduct->price = $businessProduct['price'];
                                	                $modelBusinessProduct->business_product_category_id = $businessProduct['business_product_category_id']; ?>
                                	                
                                	                <div class="mb-40 data-form">
                                                        <div class="row mt-10">
                                                            <div class="col-md-4 col-xs-6">
                                                                <?= $form->field($modelBusinessProduct, '[' . $i .']name')->textInput(['placeholder' => Yii::t('app', 'Name')]) ?>
                                                            </div>
                                                            <div class="col-md-4 col-xs-6">
                                                            	<?= $form->field($modelBusinessProduct, '[' . $i . ']price')->widget(NumberControl::className(), ['maskedInputOptions' => Yii::$app->params['maskedInputOptions']]) ?>
                                                            </div>
                                                            <div class="col-md-4 col-xs-12">
                                                            
                                                            	<?= $form->field($modelBusinessProduct, '[' . $i . ']business_product_category_id')->dropDownList(
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
                                                
                                                        <div class="row">
                                                        	<div class="col-md-8 col-xs-12">
                                                        	
                                                                <?= $form->field($modelBusinessProduct, '[' . $i .']description')->textarea([
                                                                    'rows' => 2, 
                                                                    'placeholder' => Yii::t('app', 'Description')
                                                                ]) ?>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                            	<?php
                                	            endforeach;
                                	        endif; ?>
                                	        
                            	        </div>
                            	        
                            	        <div class="row">
                                            <div class="col-md-12">
                                            
                                                <?= Html::button('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-default add-menu']) ?>
                                                <?= Html::button('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Delete'), ['class' => 'btn btn-default delete-menu']); ?>
                                                
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
$modelBusinessProduct = new BusinessProduct(); ?>

<div class="temp-form hide">
    <div class="mb-40 data-form">
        <div class="row mt-10">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelBusinessProduct, '[index]name')->textInput(['placeholder' => Yii::t('app', 'Name')]) ?>
            </div>
            <div class="col-md-4 col-xs-6">
            	<?= $form->field($modelBusinessProduct, '[index]price')->widget(NumberControl::className()) ?>
            </div>
            <div class="col-md-4 col-xs-12">
            
            	<?= $form->field($modelBusinessProduct, '[index]business_product_category_id')->dropDownList(
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

        <div class="row">
        	<div class="col-md-8 col-xs-12">
        	
                <?= $form->field($modelBusinessProduct, '[index]description')->textarea([
                    'rows' => 2, 
                    'placeholder' => Yii::t('app', 'Description')
                ]) ?>
                
            </div>
        </div>
    </div>
</div>

<?php
$jscript = '
    var indexCount = ' . count($dataBusinessProduct) . ';

    $(".main-form").find(".product-category").select2({
        theme: "krajee",
        placeholder: "' . Yii::t('app', 'Product Category') . '",
        minimumResultsForSearch: "Infinity"
    });

    function addValidator(index) {

        $("#business-product-form").yiiActiveForm("add", {
            "id":"businessproduct-" + index + "-name",
            "name":"[" + index + "]name",
            "container":".field-businessproduct-" + index + "-name",
            "input":"#businessproduct-" + index + "-name",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {"message":"Nama Menu tidak boleh kosong."});
            }
        });

        $("#business-product-form").yiiActiveForm("add", {
            "id":"businessproduct-" + index + "-price",
            "name":"businessproduct-" + index + "-price",
            "container":".field-businessproduct-" + index + "-price",
            "input":"#businessproduct-" + index + "-price",
            "validate":function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {"message":"Harga menu tidak boleh kosong."});
            }
        });
    };

    function replaceComponent(contentClone, component, content, index) {

        var inputClass = contentClone.find(".field-" + component).attr("class");
        inputClass = inputClass.replace(content, index);
        contentClone.find("#" + component).parent().attr("class", inputClass);
        
        var inputName = contentClone.find("#" + component).attr("name");
        inputName = inputName.replace(content, index);
        contentClone.find("#" + component).attr("name", inputName);

        var inputId = contentClone.find("#" + component).attr("id");
        inputId = inputId.replace(content, index);
        contentClone.find("#" + component).attr("id", inputId);
        
        return contentClone;
    };

    $(".add-menu").on("click", function() {

        var formMenu = $(".temp-form").clone();

        var numberControlSettings = {
            "displayId":"businessproduct-" + indexCount + "-price-disp",
            "maskedInputOptions": {
                "alias":"numeric",
                "digits":2,
                "groupSeparator":".",
                "radixPoint":",",
                "autoGroup":true,
                "autoUnmask":false,
                "prefix":"Rp ",
                "allowMinus":false
            }
        };

        formMenu = replaceComponent(formMenu, "businessproduct-index-name", "index", indexCount);
        formMenu = replaceComponent(formMenu, "businessproduct-index-business_product_category_id", "index", indexCount);
        formMenu = replaceComponent(formMenu, "businessproduct-index-description", "index", indexCount);

        formMenu.find(".field-businessproduct-index-price").attr("class", formMenu.find(".field-businessproduct-index-price").attr("class").replace("index", indexCount));
        formMenu.find("#businessproduct-index-price").attr("name", formMenu.find("#businessproduct-index-price").attr("name").replace("index", indexCount));
        formMenu.find("#businessproduct-index-price").attr("id", formMenu.find("#businessproduct-index-price").attr("id").replace("index", indexCount));
        formMenu.find("#businessproduct-index-price-disp").attr("name", formMenu.find("#businessproduct-index-price-disp").attr("name").replace("index", indexCount));
        formMenu.find("#businessproduct-index-price-disp").attr("id", formMenu.find("#businessproduct-index-price-disp").attr("id").replace("index", indexCount));

        $(".main-form").append(formMenu.html());

        addValidator(indexCount);

        $(".main-form").find(".product-category").select2({
            theme: "krajee",
            placeholder: "' . Yii::t('app', 'Product Category') . '",
            minimumResultsForSearch: "Infinity"
        });

        $("#businessproduct-" + indexCount + "-price").numberControl(numberControlSettings);

        indexCount++;

        return false;
    });

    $(".delete-menu").on("click", function() {

        $(".main-form").children(".data-form").last().remove();

        if (indexCount > 0) {

            indexCount--;
        }
        
        return false;
    });
';

$this->registerJs($jscript); ?>