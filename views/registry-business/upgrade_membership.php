<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use sycomponent\Tools;
use backend\components\AppComponent;
use core\models\MembershipType;

/* @var $this yii\web\View */
/* @var $model core\models\RegistryBusiness */

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryBusiness',
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

$this->title = Yii::t('app', 'Upgrade Membership') . ' : ' . $modelBusiness->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Approved Member'), 'url' => ['business/index']];
$this->params['breadcrumbs'][] = ['label' => (!empty($this->params['type']) ? Yii::t('app', 'My Member') : Yii::t('app', 'All Member')), 'url' => (empty($this->params['type']) ? ['business/index'] : ['business/index', 'type' => $this->params['type']])];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => (empty($this->params['type']) ? ['business/view', 'id' => $modelBusiness->id] : ['business/view', 'id' => $modelBusiness->id, 'type' => $this->params['type']])];
$this->params['breadcrumbs'][] = Yii::t('app', 'Upgrade Membership'); ?>

<?= $ajaxRequest->component() ?>

<div class="registry-business-form">

    <div class="row">
        <div class="col-sm-12">
            <div class="x_panel">

                <?php $form = ActiveForm::begin([
                        'id' => 'registry-business-form',
                        'action' => (empty($type) ? ['upgrade-membership', 'id' => $modelBusiness->id] : ['upgrade-membership', 'id' => $modelBusiness->id, 'type' => $type]),
                        'options' => [

                        ]
                ]); ?>

                <div class="x_content">

                    <div class="clearfix" style="margin-top: 15px"></div>

                    <div class="row">
                        <div class="col-lg-12">
                            <h4><strong><?= Yii::t('app', 'Current Membership Type') ?></strong> : <?= $modelBusiness->membershipType->name ?></h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <h4><strong><?= Yii::t('app', 'User In Charge') ?></strong> : <?= $modelBusiness->userInCharge->full_name ?></h4>
                            <hr>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <h4><strong><?= Yii::t('app', 'Upgrade Membership Type') ?></strong></h4>
                                <hr>
                            </div>
                        </div>
                    </div>

                    <?php
                    $model->membership_type_id = null; ?>

                    <?= $form->field($model, 'membership_type_id', [
                        'template' => '
                            <div class="row">
                                <div class="col-md-12">
                                    {input}
                                    {error}
                                </div>
                            </div>
                        ',
                    ])->radioList(
                            ArrayHelper::map(
                                MembershipType::find()->andWhere(['is_free' => true, 'as_archive' => false])->orderBy('order')->asArray()->all(),
                                'id',
                                function($data) {
                                    return $data['name'];
                                }
                            ),
                            [
                                'separator' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'
                            ]) ?>

                    <div class="row">
                        <div class="col-md-12">
                            <h4><strong><?= Yii::t('app', 'Business Information') ?></strong></h4>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-20">

                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'Name')) ?><br>
                            <?= $modelBusiness->name ?>
                        </div>
                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'Unique Name')) ?><br>
                            <?= $modelBusiness->unique_name ?>
                        </div>

                    </div>

                    <div class="row mb-20">

                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'Address Type')) ?><br>
                            <?= $modelBusiness->businessLocation->address_type ?>
                        </div>
                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'Address')) ?><br>
                            <?= $modelBusiness->businessLocation->address ?>
                        </div>
                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'Address Info')) ?><br>
                            <?= $modelBusiness->businessLocation->address_info ?>
                        </div>
                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'City ID')) ?><br>
                            <?= $modelBusiness->businessLocation->city->name ?>
                        </div>

                    </div>

                    <div class="row mb-20">

                        <div class="col-lg-3 col-xs-6">
                            <?= Html::label(Yii::t('app', 'District ID')) ?><br>
                            <?= $modelBusiness->businessLocation->district->name ?>
                        </div>

                        <div class="col-lg-3 col-xs-6">
                            <?= Html::label(Yii::t('app', 'Village ID')) ?><br>
                            <?= $modelBusiness->businessLocation->village->name ?>
                        </div>
                        <div class="col-lg-6 col-xs-6">
                            <?= Html::label(Yii::t('app', 'Coordinate')) ?><br>
                            <?= $modelBusiness->businessLocation->coordinate ?>
                        </div>

                    </div>

                    <div class="row mb-20">

                        <div class="col-lg-3 col-xs-6">
                            <?= Html::label(Yii::t('app', 'Email')) ?><br>
                            <?= $modelBusiness->email ?>
                        </div>

                        <div class="col-lg-3 col-xs-6">
                            <?= Html::label(Yii::t('app', 'Phone1')) ?><br>
                            <?= $modelBusiness->phone1 ?>
                        </div>

                        <div class="col-lg-3 col-xs-6">
                            <?= Html::label(Yii::t('app', 'Phone2')) ?><br>
                            <?= $modelBusiness->phone2 ?>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <?= Html::label(Yii::t('app', 'Phone3')) ?><br>
                            <?= $modelBusiness->phone3 ?>
                        </div>

                    </div>

                    <div class="row mb-20">
                        <div class="col-md-12">

                            <?= Html::label(Yii::t('app', 'Business Location')) ?>

                            <?php
                            $coordinate = explode(',', $modelBusiness->businessLocation->coordinate);

                            if (!empty($coordinate) && count($coordinate) > 1) {

                                $appComponent = new AppComponent;

                                echo $appComponent->map([
                                    'latitude' => $coordinate[0],
                                    'longitude' => $coordinate[1],
                                ]);
                            } ?>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4><strong><?= Yii::t('app', 'Marketing Information') ?></strong></h4>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::label(Yii::t('app', 'Business Category'), null, ['class' => 'control-label']) ?>
                        </div>
                    </div>
                    <div class="row">

                        <?php
                        foreach ($modelBusiness->businessCategories as $businessCategory) {

                            echo '
                                <div class="col-sm-2 col-xs-6">
                                    ' . $businessCategory->category->name . '
                                </div>';
                        } ?>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::label(Yii::t('app', 'Product Category'), null, ['class' => 'control-label']) ?>
                        </div>
                    </div>
                    <div class="row">

                        <?php
                        $productCategoryParent = [];
                        $productCategoryChild = [];

                        if (!empty($modelBusiness->businessProductCategories)) {

                            foreach ($modelBusiness->businessProductCategories as $value) {

                                if (empty($value->productCategory->parent_id)) {

                                    $productCategoryParent[$value->product_category_id] = $value->productCategory->name;
                                } else {

                                    $productCategoryChild[$value->product_category_id] = $value->productCategory->name;
                                }
                            }

                            if (!empty($productCategoryParent)) {

                                echo '
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3 col-xs-6 mt-10">
                                        - ' . Html::label(Yii::t('app', 'Product Category General')) . ' -
                                    </div>
                                    <div class="clearfix"></div>';

                                foreach ($productCategoryParent as $productCategory) {

                                    echo '
                                        <div class="col-sm-2 col-xs-6">
                                            ' . $productCategory . '
                                        </div>';
                                }
                            }

                            if (!empty($productCategoryChild)) {

                                echo '
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3 col-xs-6 mt-10">
                                        - ' . Html::label(Yii::t('app', 'Product Category Specific')) . ' -
                                    </div>
                                    <div class="clearfix"></div>';

                                foreach ($productCategoryChild as $productCategory) {

                                    echo '
                                        <div class="col-sm-2 col-xs-6">
                                            ' . $productCategory . '
                                        </div>';
                                }
                            }
                        } ?>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::label(Yii::t('app', 'Business Hour'), null, ['class' => 'control-label']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">

                            <?php
                            $days = Yii::$app->params['days'];

                            foreach ($modelBusiness->businessHours as $businessHour):

                                $is24Hour = (($businessHour->open_at == '00:00:00') && ($businessHour->close_at == '24:00:00')) ? true : false; ?>

                                <div class="row">
                                    <div class="col-md-2">
                                        <?= Html::label(Yii::t('app', $days[$businessHour->day - 1])) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $is24Hour ? Yii::t('app','24 Hours') : Yii::$app->formatter->asTime($businessHour->open_at) . ' - ' . Yii::$app->formatter->asTime($businessHour->close_at);?>
                                    </div>
                                </div>

                            <?php
                            endforeach; ?>

                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::label(Yii::t('app', 'Price Range'), null, ['class' => 'control-label']) ?>
                        </div>
                    </div>

                    <div class="row mb-20">

                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'Price Min')) ?><br>
                            <?= Yii::$app->formatter->asCurrency(!empty($modelBusiness->businessDetail) ? $modelBusiness->businessDetail->price_min : null); ?>
                        </div>

                        <div class="col-md-3">
                            <?= Html::label(Yii::t('app', 'Price Max')) ?><br>
                            <?= Yii::$app->formatter->asCurrency(!empty($modelBusiness->businessDetail) ? $modelBusiness->businessDetail->price_max : null); ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::label(Yii::t('app', 'Facility'), null, ['class' => 'control-label']) ?>
                        </div>
                    </div>
                    <div class="row">

                        <?php
                        foreach ($modelBusiness->businessFacilities as $businessFacility) {

                            echo '
                                <div class="col-sm-2 col-xs-6">
                                    ' . $businessFacility->facility->name . '
                                </div>';
                        } ?>

                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::label(Yii::t('app', 'Photo'), null, ['class' => 'control-label']) ?>
                        </div>
                    </div>
                    <div class="row">

                        <?php
                        foreach ($modelBusiness->businessImages as $businessImage): ?>

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
                                </div>
                            </div>

                        <?php
                        endforeach; ?>

                    </div>

                    <?= Html::submitButton('<i class="fa fa-level-up-alt"></i> ' . 'Upgrade', ['class' => 'btn btn-primary']) ?>

                    <?= Html::a('<i class="fa fa-times"></i> ' . 'Cancel',
                        (empty($this->params['type']) ? ['business/index'] : ['business/index', 'type' => $this->params['type']]),
                        [
                            'class' => 'btn btn-default',
                        ]) ?>

                </div>

                <?php ActiveForm::end(); ?>

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

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/skins/all.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/Magnific-Popup/dist/magnific-popup.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/icheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
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

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']());?>