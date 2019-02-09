<?php

use yii\helpers\Html;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use sycomponent\Tools;
use backoffice\components\AppComponent;

/* @var $this yii\web\View */
/* @var $model core\models\Business */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'Business',
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' =>  ['member']];
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component();

$actionButton =
    Html::button('<i class="fa fa-pencil-alt"></i> Edit', [
        'type' => 'button',
        'class' => 'btn btn-primary dropdown-toggle',
        'data-toggle' => 'dropdown',
        'aria-haspopup' => 'true',
        'aria-expanded' => 'false',
    ]) . '
    
    <ul class="dropdown-menu">
        <li>' . Html::a(Yii::t('app', 'Business Information'), ['update-business-info', 'id' => $model['id']]) . '</li>
        <li>' . Html::a(Yii::t('app', 'Marketing Information'), ['update-marketing-info', 'id' => $model['id']]) . '</li>
        <li>' . Html::a(Yii::t('app', 'Gallery Photo'), ['update-gallery-photo', 'id' => $model['id']]) . '</li>
        <li>' . Html::a(Yii::t('app', 'Operational Hours'), ['update-business-hour', 'id' => $model['id']]) . '</li>
        <li>' . Html::a(Yii::t('app', 'Contact Person'), ['update-contact-person', 'id' => $model['id']]) . '</li>
        <li>' . Html::a(Yii::t('app', 'Payment Methods'), ['business-payment/index', 'id' => $model['id']])  . '</li>
        <li>' . Html::a(Yii::t('app', 'Delivery Methods'), ['business-delivery/index', 'id' => $model['id']]) . '</li>
    </ul>
'; ?>

<div class="business-view">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">

                    <div class="btn-group">
						<?= $actionButton ?>
                    </div>

                    <?= Html::a('<i class="fas fa-utensils"></i> Menu', ['business-product/index', 'id' => $model['id']], ['class' => 'btn btn-default']) ?>
                    <?= Html::a('<i class="fas fa-percent"></i> Special Discount', ['business-promo/index', 'id' => $model['id']], ['class' => 'btn btn-default']) ?>
                    <?= Html::a('<i class="fa fa-level-up-alt"></i> Upgrade', ['upgrade-membership', 'id' => $model['id']], ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['member'], ['class' => 'btn btn-default']) ?>

                    <div class="clearfix" style="margin-top: 15px"></div>

                    <div class="row">
                        <div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Membership Type') ?></strong> : <?= $model['membershipType']['name'] ?></h4>
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
                            <?= $model['businessLocation']['address_type'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Address')) ?><br>
                            <?= $model['businessLocation']['address'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Address Info')) ?><br>
                            <?= $model['businessLocation']['address_info'] ?>
                        </div>
                    </div>

                    <div class="row mb-20">
                    	<div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'City ID')) ?><br>
                            <?= $model['businessLocation']['city']['name'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'District ID')) ?><br>
                            <?= $model['businessLocation']['district']['name'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Village ID')) ?><br>
                            <?= $model['businessLocation']['village']['name'] ?>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <?= Html::label(Yii::t('app', 'Coordinate')) ?><br>
                            <?= $model['businessLocation']['coordinate'] ?>
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
                            $coordinate = explode(',', $model['businessLocation']['coordinate']);

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
                        if (!empty($model['businessCategories'])) {
                            
                            foreach ($model['businessCategories'] as $dataBusinessCategory) {

                                echo '
                                    <div class="col-xs-4 col-sm-2">
                                        ' . $dataBusinessCategory['category']['name'] . '
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

                        if (!empty($model['businessProductCategories'])) {
                            
                            foreach ($model['businessProductCategories'] as $dataBusinessProductCategory) {

                                if ($dataBusinessProductCategory['productCategory']['type'] == 'General') {

                                    $productCategoryParent[$dataBusinessProductCategory['product_category_id']] = $dataBusinessProductCategory['productCategory']['name'];
                                } else if ($dataBusinessProductCategory['productCategory']['type'] == 'Specific' || $dataBusinessProductCategory['productCategory']['type'] == 'Specific-Menu') {

                                    $productCategoryChild[$dataBusinessProductCategory['product_category_id']] = $dataBusinessProductCategory['productCategory']['name'];
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

                            if (!empty($model['businessHours'])):
                            
                                foreach ($model['businessHours'] as $dataBusinessHour):

                                    $is24Hour = (($dataBusinessHour['open_at'] == '00:00:00') && ($dataBusinessHour['close_at'] == '24:00:00')); ?>

                                    <div class="row">
                                        <div class="col-xs-4 col-sm-2">
                                            <?= Html::label(Yii::t('app', $days[$dataBusinessHour['day'] - 1])) ?>
                                        </div>
                                        <div class="col-xs-4 col-sm-4">
                                        	
                                        	<?php
                                        	echo $is24Hour ? Yii::t('app','24 Hours') : Yii::$app->formatter->asTime($dataBusinessHour['open_at'], 'short') . ' - ' . Yii::$app->formatter->asTime($dataBusinessHour['close_at'], 'short');
                                            
                                            if (!empty($dataBusinessHour['businessHourAdditionals'])) {
                                                
                                                foreach ($dataBusinessHour['businessHourAdditionals'] as $dataBusinessHourAdditional) {
                                                        
                                                    echo ', ' . Yii::$app->formatter->asTime($dataBusinessHourAdditional['open_at'], 'short') . ' - ' . Yii::$app->formatter->asTime($dataBusinessHourAdditional['close_at'], 'short');
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
                    		<?= !empty($model['businessDetail']['note_business_hour']) ? $model['businessDetail']['note_business_hour'] : '-' ?>
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
                            <?= Yii::$app->formatter->asCurrency($model['businessDetail']['price_min']); ?>
                        </div>
                        <div class="col-xs-4 col-sm-2">
                            <?= Html::label(Yii::t('app', 'Price Max')) ?><br>
                            <?= Yii::$app->formatter->asCurrency($model['businessDetail']['price_max']); ?>
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
                        if (!empty($model['businessFacilities'])) {
                            
                            foreach ($model['businessFacilities'] as $dataBusinessFacility) {

                                echo '
                                    <div class="col-xs-4 col-sm-2">
                                        ' . $dataBusinessFacility['facility']['name'] . '
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
                        if (!empty($model['businessImages'])):
                        
                            foreach ($model['businessImages'] as $dataBusinessImage): ?>

                                <div class="col-xs-6 col-sm-3">
                                    <div class="thumbnail">
                                        <div class="image view view-first">
                                            <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $dataBusinessImage['image'], 200, 150), ['style' => 'width: 100%; display: block;']);  ?>
                                            <div class="mask">
                                                <p>&nbsp;</p>
                                                <div class="tools tools-bottom">
                                                    <a class="show-image direct" href="<?= Yii::getAlias('@uploadsUrl') . '/img/registry_business/' . $dataBusinessImage['image'] ?>"><i class="fa fa-search"></i></a>
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
                    
                    <div class="row mb-20">
                    	<div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Contact Person') ?></strong></h4>
                        </div>
                    </div>
                    
                    <hr>
                    
    				<?php
				    if (!empty($model['businessContactPeople'])):
				    
				        foreach ($model['businessContactPeople'] as $i => $dataBusinessContactPerson):
			            	
				            $is_primary = !empty($dataBusinessContactPerson['is_primary_contact']) ? ' - ' . Yii::t('app', 'Primary Contact') : ''; ?>
    			            
			                <div class="row mb-20">
			            		<div class="col-xs-12 mb-10">
			            			<strong><?= Yii::t('app', 'Contact') . ' ' . ($i + 1) . $is_primary ?></strong>
    			            	</div>
			            		<div class="col-sm-3 col-xs-6 mb-10">
    			            		<?= Html::label(Yii::t('app', 'Name')) ?><br>
        			                <?= $dataBusinessContactPerson['person']['first_name'] . ' ' . $dataBusinessContactPerson['person']['last_name']; ?>
    			                </div>
    			                <div class="col-sm-3 col-xs-6 mb-10">
    			                	<?= Html::label(Yii::t('app', 'Position')) ?><br>
    			                	<?= $dataBusinessContactPerson['position']; ?>
    			                </div>
    			                <div class="col-sm-3 col-xs-6">
			                		<?= Html::label(Yii::t('app', 'Email')) ?><br>
    			            		<?= !empty($dataBusinessContactPerson['person']['email']) ? $dataBusinessContactPerson['person']['email'] : '-'; ?>
			                	</div>
			                	<div class="col-sm-3 col-xs-6">
    			            		<?= Html::label(Yii::t('app', 'Phone')) ?><br>
    			            		<?= !empty($dataBusinessContactPerson['person']['phone']) ? $dataBusinessContactPerson['person']['phone'] : '-'; ?>
			                	</div>
			                </div>
			                
			                <div class="row mb-20">
			                	<div class="col-xs-12">
    			            		<?= Html::label(Yii::t('app', 'Note')) . '<br>'; ?>
    			            		<?= !empty($dataBusinessContactPerson['note']) ? $dataBusinessContactPerson['note'] : '-'; ?>
			                	</div>
			                </div>
			                
			                <hr>
			                
			            <?php
			            endforeach;
		            else: ?>
			         	
			         	<div class="row mb-20">
			         		<div class="col-xs-12">
	         		  			<?= Yii::t('app', 'Data Not Available') ?>
		         		  	</div>
			         	</div>
			         	
			         	<hr>
			         	
		            <?php
				    endif; ?>
				    
				    <div class="row">
                    	<div class="col-xs-12">
                            <h4><strong><?= Yii::t('app', 'Online Order') ?></strong></h4>
                        </div>
                    </div>
				    
				    <hr>
				    
				    <div class="row">
                        <div class="col-sm-3 col-xs-5">
                            <?= Html::label(Yii::t('app', 'Payment Methods')) ?>
                        </div>
                        <div class="col-sm-9 col-xs-7">
                            <?= Html::label(Yii::t('app', 'Note')) ?>
                        </div>
                    </div>
                    
                    <div class="row">
                    
                        <?php
                        if (!empty($model['businessPayments'])) {
                            
                            foreach ($model['businessPayments'] as $dataBusinessPayment) {
    
                                echo '
                                    <div class="col-sm-3 col-xs-5 mb-10">
                                        ' . $dataBusinessPayment['paymentMethod']['payment_name'] . '
                                    </div>
                                    <div class="col-sm-9 col-xs-7 mb-10">
                                        ' . (!empty($dataBusinessPayment['note']) ? $dataBusinessPayment['note'] : '-') . '
                                    </div>
                                    <div class="col-sm-offset-3 col-sm-9 col-xs-offset-5 col-xs-7 mb-10">
                                        ' . (!empty($dataBusinessPayment['description']) ? $dataBusinessPayment['description'] : '-') . '
                                    </div>';
                            }
                        } else {
                            
                            echo '
                                <div class="col-sm-3 col-xs-5"> - </div>
                                <div class="col-sm-9 col-xs-7"> - </div>';
                        } ?>
                        
                    </div>

                    <hr>
                    
                    <div class="row">
                        <div class="col-sm-3 col-xs-5">
                            <?= Html::label(Yii::t('app', 'Delivery Methods')) ?>
                        </div>
                        <div class="col-sm-9 col-xs-7">
                            <?= Html::label(Yii::t('app', 'Note')) ?>
                        </div>
                    </div>
                    
                    <div class = "row">
                    
                        <?php
                        if (!empty($model['businessDeliveries'])) {
                            
                            foreach ($model['businessDeliveries'] as $dataBusinessDelivery) {
    
                                echo '
                                    <div class="col-sm-3 col-xs-5 mb-10">
                                        ' . $dataBusinessDelivery['deliveryMethod']['delivery_name'] . '
                                    </div>
                                    <div class="col-sm-9 col-xs-7 mb-10">
                                        ' . (!empty($dataBusinessDelivery['note']) ? $dataBusinessDelivery['note'] : '-') . '
                                    </div>
                                    <div class="col-sm-offset-3 col-sm-9 col-xs-offset-5 col-xs-7 mb-10">
                                        ' . (!empty($dataBusinessDelivery['description']) ? $dataBusinessDelivery['description'] : '-') . '
                                    </div>';
                            }
                        } else {
                            
                            echo '
                                <div class="col-sm-3 col-xs-5"> - </div>
                                <div class="col-sm-9 col-xs-7"> - </div>';
                        } ?>
                        
                    </div>

                    <hr>
				    
				    <div class="btn-group dropup">
                        <?= $actionButton ?>
                    </div>

                    <?= Html::a('<i class="fas fa-utensils"></i> Menu', ['business-product/index', 'id' => $model['id']], ['class' => 'btn btn-default']) ?>
                    <?= Html::a('<i class="fas fa-percent"></i> Special Discount', ['business-promo/index', 'id' => $model['id']], ['class' => 'btn btn-default']) ?>
                    <?= Html::a('<i class="fa fa-level-up-alt"></i> Upgrade', ['upgrade-membership', 'id' => $model['id']], ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<i class="fa fa-times"></i> Cancel', ['member'], ['class' => 'btn btn-default']) ?>

                </div>

            </div>
        </div>
    </div>

</div>

<?php
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