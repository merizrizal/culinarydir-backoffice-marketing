<?php

use yii\helpers\Html;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use sycomponent\Tools;
use backoffice\components\AppComponent;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */
/* @var $statusApproval backoffice\modules\marketing\controllers\RegistryBusinessController */
/* @var $actionButton backoffice\modules\marketing\controllers\RegistryBusinessController */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryBusiness',
]);

$ajaxRequest->view();

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

$this->title = $model['name'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Data Application'), 'url' =>  ['index-' . strtolower($statusApproval)]];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(); ?>

<div class="registry-business-view">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">

                    <?php
                    if (!empty($actionButton)) {
                        
                        foreach ($actionButton as $valActionButton) {
                            
                            echo $valActionButton($model);
                        }
                    }

                    echo ' ' . Html::a('<i class="fa fa-times"></i> Cancel', ['index-' . strtolower($statusApproval)], ['class' => 'btn btn-default']); ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

                    <div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Membership Type') ?></strong> : <?= $model['membershipType']['name'] ?> | <strong><?= Yii::t('app', 'Status') ?></strong> : <?= $model['applicationBusiness']['logStatusApprovals'][0]['statusApproval']['name'] ?></h4>
                        </div>
                        <div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'User In Charge') ?></strong> : <?= $model['userInCharge']['full_name'] ?></h4>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Business Information') ?></strong></h4>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Name')) ?><br>
                            <?= $model['name'] ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Unique Name')) ?><br>
                            <?= $model['unique_name'] ?>
                            
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Address Type')) ?><br>
                            <?= $model['address_type'] ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Address')) ?><br>
                            <?= $model['address'] ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Address Info')) ?><br>
                            <?= $model['address_info'] ?>
                            
                        </div>
                    </div>

                    <div class="row mb-20">
						<div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'City ID')) ?><br>
                            <?= $model['city']['name'] ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'District ID')) ?><br>
                            <?= $model['district']['name'] ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Village ID')) ?><br>
                            <?= $model['village']['name'] ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Coordinate')) ?><br>
                            <?= $model['coordinate'] ?>
                            
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Email')) ?><br>
                            <?= !empty($model['email']) ? $model['email'] : '-' ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Phone1')) ?><br>
                            <?= !empty($model['phone1']) ? $model['phone1'] : '-' ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Phone2')) ?><br>
                            <?= !empty($model['phone2']) ? $model['phone2'] : '-' ?>
                            
                        </div>
                        <div class="col-xs-6 col-sm-3">
                        
                            <?= Html::label(Yii::t('app', 'Phone3')) ?><br>
                            <?= !empty($model['phone3']) ? $model['phone3'] : '-' ?>
                            
                        </div>
                    </div>
                    
                    <div class="row mb-20">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'About')) ?><br>
                            <?= !empty($model['about']) ? $model['about'] : '-' ?>
                        
                        </div>
                    </div>
                    
                    <div class="row mb-20">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Note')) ?><br>
                            <?= !empty($model['note']) ? $model['note'] : '-' ?>
                        
                        </div>
                    </div>

                    <div class="row mb-20">
                        <div class="col-xs-12">

                            <?= Html::label(Yii::t('app', 'Business Location')) ?><br>

                            <?php
                            $coordinate = explode(',', $model['coordinate']);

                            if (!empty($coordinate) && count($coordinate) > 1) {

                                $appComponent = new AppComponent;

                                echo $appComponent->map([
                                    'latitude' => $coordinate[0],
                                    'longitude' => $coordinate[1],
                                ]);
                            } ?>

                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Marketing Information') ?></strong></h4>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Business Category')) ?>
                        
                        </div>
                    </div>

                    <div class="row">

                        <?php
                        if (!empty($model['registryBusinessCategories'])) {
                            
                            foreach ($model['registryBusinessCategories'] as $dataRegistryBusinessCategory) {

                                echo '
                                    <div class="col-xs-4 col-sm-2">
                                        ' . $dataRegistryBusinessCategory['category']['name'] . '
                                    </div>';
                            }
                        } ?>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Product Category')) ?>
                            
                        </div>
                    </div>
                    
                    <div class="row">

                        <?php
                        $productCategoryParent = [];
                        $productCategoryChild = [];

                        if (!empty($model['registryBusinessProductCategories'])) {
                            
                            foreach ($model['registryBusinessProductCategories'] as $dataRegistryBusinessProductCategory) {

                                if ($dataRegistryBusinessProductCategory['productCategory']['is_parent']) {

                                    $productCategoryParent[$dataRegistryBusinessProductCategory['product_category_id']] = $dataRegistryBusinessProductCategory['productCategory']['name'];
                                } else {

                                    $productCategoryChild[$dataRegistryBusinessProductCategory['product_category_id']] = $dataRegistryBusinessProductCategory['productCategory']['name'];
                                }
                            }

                            if (!empty($productCategoryParent)) {

                                echo '
                                    <div class="col-xs-12">
                                        - ' . Html::label(Yii::t('app', 'Product Category General')) . ' -
                                    </div>';

                                foreach ($productCategoryParent as $productCategory) {

                                    echo '
                                        <div class="col-xs-4 col-sm-2">
                                            ' . $productCategory . '
                                        </div>';
                                }
                            }

                            if (!empty($productCategoryChild)) {

                                echo '
                                    <div class="col-xs-12">
                                        - ' . Html::label(Yii::t('app', 'Product Category Specific')) . ' -
                                    </div>';

                                foreach ($productCategoryChild as $productCategory) {

                                    echo '
                                        <div class="col-xs-4 col-sm-2">
                                            ' . $productCategory . '
                                        </div>';
                                }
                            }
                        } ?>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Business Hour')) ?>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">

                            <?php
                            $days = Yii::$app->params['days'];

                            if (!empty($model['registryBusinessHours'])):
                            
                                foreach ($model['registryBusinessHours'] as $dataRegistryBusinessHour):

                                    $is24Hour = (($dataRegistryBusinessHour['open_at'] == '00:00:00') && ($dataRegistryBusinessHour['close_at'] == '24:00:00')); ?>

                                    <div class="row">
                                        <div class="col-xs-4 col-sm-2">
                                        
                                            <?= Html::label(Yii::t('app', $days[$dataRegistryBusinessHour['day'] - 1])) ?>
                                            
                                        </div>
                                        <div class="col-xs-4 col-sm-4">
                                        	
                                        	<?php
                                        	echo ($is24Hour ? Yii::t('app','24 Hours') : Yii::$app->formatter->asTime($dataRegistryBusinessHour['open_at'], 'short') . ' - ' . Yii::$app->formatter->asTime($dataRegistryBusinessHour['close_at'], 'short'));
                                            
                                        	if (!empty($dataRegistryBusinessHour['registryBusinessHourAdditionals'])) {
                                                
                                        	    foreach ($dataRegistryBusinessHour['registryBusinessHourAdditionals'] as $dataRegistryBusinessHourAdditional) {
                                                        
                                        	        echo ', ' . Yii::$app->formatter->asTime($dataRegistryBusinessHourAdditional['open_at'], 'short') . ' - ' . Yii::$app->formatter->asTime($dataRegistryBusinessHourAdditional['close_at'], 'short');
                                        	    }
                                            } ?>
                                            
                                        </div>
                                    </div>
                                    
                                <?php
                                endforeach;
                            endif; ?>

                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Note')) ?><br>
                            <?= !empty($model['note_business_hour']) ? $model['note_business_hour'] : '-' ?>
                            
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Average Spending')) ?>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4 col-sm-2">
                        
                            <?= Html::label(Yii::t('app', 'Price Min')) ?><br>
                            <?= Yii::$app->formatter->asCurrency($model['price_min']); ?>
                            
                        </div>
                        <div class="col-xs-4 col-sm-2">
                        
                            <?= Html::label(Yii::t('app', 'Price Max')) ?><br>
                            <?= Yii::$app->formatter->asCurrency($model['price_max']); ?>
                            
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Facility')) ?>
                            
                        </div>
                    </div>
                    
                    <div class="row">

                        <?php
                        if (!empty($model['registryBusinessFacilities'])) {
                            
                            foreach ($model['registryBusinessFacilities'] as $dataRegistryBusinessFacility) {

                                echo '
                                    <div class="col-xs-4 col-sm-2">
                                        ' . $dataRegistryBusinessFacility['facility']['name'] . '
                                    </div>';
                            }
                        } ?>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-xs-12">
                        
                            <?= Html::label(Yii::t('app', 'Photo')) ?>
                            
                        </div>
                    </div>
                    
                    <div class="row">

                        <?php
                        if (!empty($model['registryBusinessImages'])):
                        
                            foreach ($model['registryBusinessImages'] as $dataRegistryBusinessImage): ?>

                                <div class="col-xs-6 col-sm-3">
                                    <div class="thumbnail">
                                        <div class="image view view-first">
                                        
                                            <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataRegistryBusinessImage['image'], 200, 150), ['style' => 'width: 100%; display: block;']);  ?>
                                            
                                            <div class="mask">
                                                <p>&nbsp;</p>
                                                <div class="tools tools-bottom">
                                                    <a class="show-image direct" href="<?= Yii::getAlias('@uploadsUrl') . '/img/registry_business/' . $dataRegistryBusinessImage['image'] ?>"><i class="fa fa-search"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php
                            endforeach;
                        endif; ?>

                    </div>
                    
                    <hr>
                    
                    <div class="row">
                    	<div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Contact Person') ?></strong></h4>
                        </div>
                    </div>
                    
                    <hr>
                		
    				<?php
				    if (!empty($model['registryBusinessContactPeople'])): ?>
			            
				        <table class="table table-responsive">
				        	<tr>
			            		<th><?= Html::label(Yii::t('app', 'Name')) ?></th>
			            		<th><?= Html::label(Yii::t('app', 'Position')) ?></th>
			            		<th><?= Html::label(Yii::t('app', 'Email')) ?></th>
			            		<th><?= Html::label(Yii::t('app', 'Phone')) ?></th>
			            		<th><?= Html::label(Yii::t('app', 'Note')) ?></th>
			            		<th><?= Html::label(Yii::t('app', 'Is Primary Contact')) ?></th>
			            	</tr>
				        
    				    	<?php
    				    	foreach ($model['registryBusinessContactPeople'] as $dataRegistryBusinessContactPerson): ?>
    			                
        			            <tr>
        			            	<td><?= $dataRegistryBusinessContactPerson['person']['first_name'] . ' ' . $dataRegistryBusinessContactPerson['person']['last_name']; ?></td>
    			            		<td><?= $dataRegistryBusinessContactPerson['position']; ?></td>
    			            		<td><?= !empty($dataRegistryBusinessContactPerson['person']['email']) ? $dataRegistryBusinessContactPerson['person']['email'] : '-'; ?></td>
    			            		<td><?= !empty($dataRegistryBusinessContactPerson['person']['phone']) ? $dataRegistryBusinessContactPerson['person']['phone'] : '-'; ?></td>
    			            		<td><?= !empty($dataRegistryBusinessContactPerson['note']) ? $dataRegistryBusinessContactPerson['note'] : '-'; ?></td>
    			            		<td><?= !empty($dataRegistryBusinessContactPerson['is_primary_contact']) ? ' YA ' : ' TIDAK ' ?></td>
        			            </tr>
    			                
    			            <?php
    			            endforeach; ?>
			            
			            </table>
		            
		            <?php
			        else: ?>
			         	
			         	<div class="row mb-20">
			         		<div class="col-xs-12">
    			         		
	         		  			<?= Yii::t('app', 'Data Not Available') ?>
    			         		  
		         		  	</div>
			         	</div>
			         	
			         	<hr>
		            
		            <?php
				    endif;
				    
                    if (!empty($actionButton)) {
                        
                        foreach ($actionButton as $valActionButton) {
                            
                            echo $valActionButton($model, 'dropup');
                        }
                    }

                    echo ' ' . Html::a('<i class="fa fa-times"></i> Cancel', ['index-' . strtolower($statusApproval)], ['class' => 'btn btn-default']); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$modalDialog = new ModalDialog([
    'clickedComponent' => 'a#delete',
    'modelAttributeId' => 'model-id',
    'modelAttributeName' => 'model-name',
]);

$modalDialog->theScript(false);

echo $modalDialog->renderDialog();

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".thumbnail").magnificPopup({
        delegate: "a.show-image",
        type: "image",
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
        },
        image: {
            tError: "The image could not be loaded."
        }
    });
';

$this->registerJs($jscript); ?>