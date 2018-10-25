<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use sycomponent\AjaxRequest;
use sycomponent\NotificationDialog;
use sycomponent\Tools;

/* @var $this yii\web\View */
/* @var $model core\models\Business */
/* @var $modelBusinessImage core\models\BusinessImage */
/* @var $dataBusinessImage core\models\BusinessImage */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'Business',
]);

$ajaxRequest->form();

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) :

    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

$this->title = 'Update ' . Yii::t('app', 'Gallery Photo') . ' : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member'), 'url' => ['member']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-member', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update ' . Yii::t('app', 'Gallery Photo');

echo $ajaxRequest->component(); ?>

<div class="business-update">
    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="business-form">

                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'business-form',
                        'action' => ['update-gallery-photo', 'id' => $model->id],
                        'options' => [

                        ],
                        'fieldConfig' => [
                            'template' => '{input}{error}',
                        ]
                    ]); ?>

                        <div class="x_title">
                            <h4><?= Yii::t('app', 'Gallery Photo') ?></h4>
                        </div>

                        <div class="x_content">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-2">

                                        <div class="row">

                                            <?php
                                            foreach ($dataBusinessImage as $businessImage): ?>

                                                <div class="col-xs-3">
                                                    <div class="thumbnail">
                                                        <div class="image view view-first">
                                                        
                                                            <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/registry_business/', $businessImage['image'], 200, 150), ['style' => 'width: 100%; display: block;']);  ?>
                                                            
                                                            <div class="mask">
                                                                <p>&nbsp;</p>
                                                                <div class="tools tools-bottom">
                                                                    <a class="show-image direct" href="<?= Yii::getAlias('@uploadsUrl') . '/img/registry_business/' . $businessImage['image'] ?>"><i class="fa fa-search"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-10">
                                                        
                                                        	<?= Html::dropDownList('category['. $businessImage['id'] .']', !empty($businessImage['category']) ? $businessImage['category'] : null, ['Ambience' => 'Suasana', 'Menu' => 'Menu'], ['class' => 'photo-category']) ?>
                                                        	
                                                        	<div class="clearfix" style="margin-bottom: 5px"></div>
                                                        
                                                            <?= Html::checkbox('BusinessImageDelete[]', false, ['class' => 'form-control', 'label' => 'Delete', 'value' => $businessImage['id']]) ?>
                                                            
                                                            <div class="clearfix"></div>
                                                            
                                                            <?= Html::checkbox('profile['. $businessImage['id'] .']', ($businessImage['type'] == 'Profile'), ['class' => 'form-control', 'label' => 'Set as Profile']) ?>
                                                            
                                                            <div class="clearfix"></div>
                                                            
                                                            <?= Html::radio('thumbnail', $businessImage['is_primary'], ['class' => 'form-control', 'label' => 'Set as Thumbnail', 'value' => $businessImage['id']]) ?>
                                                            
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php
                                            endforeach; ?>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-2">
                                    
                                        <?= Html::label(Yii::t('app', 'Foto')) ?>
                                        
                                    </div>
                                    <div class="col-sm-10">

                                        <?= $form->field($modelBusinessImage, 'image[]')->widget(FileInput::classname(), [
                                            'options' => [
                                                'accept' => 'image/*',
                                                'multiple' => true,
                                            ],
                                            'pluginOptions' => [
                                                'showRemove' => true,
                                                'showUpload' => false,
                                            ]
                                        ]); ?>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">

                                    <?php
                                    echo Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-primary']);
                                    echo Html::a('<i class="fa fa-times"></i> Cancel', ['view-member', 'id' => $model->id], ['class' => 'btn btn-default']); ?>

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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/jquery.magnific-popup.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".photo-category").select2({
        theme: "krajee",
        minimumResultsForSearch: -1
    });

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

$this->registerJs(Yii::$app->params['checkbox-radio-script']() . $jscript); ?>