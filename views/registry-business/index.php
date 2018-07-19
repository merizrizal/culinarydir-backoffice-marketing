<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use core\models\User;
use core\models\District;
use core\models\Village;
use core\models\MembershipType;

/* @var $this yii\web\View */
/* @var $searchModel core\models\search\RegistryBusinessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'RegistryBusiness',
    'createUrl' => (empty($this->params['type']) ? ['create'] : ['create', 'type' => $this->params['type']])
]);

$ajaxRequest->index();

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

$this->title = (!empty($this->params['type']) ? Yii::t('app', 'My Registry') : Yii::t('app', 'All Registry'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Membership'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<?= $ajaxRequest->component(true) ?>

<div class="registry-business-index">

    <?php
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]); ?>

    <?= GridView::widget([
        'id' => 'grid-view-registry-business',
        'dataProvider' => $dataProvider,
        'pjax' => false,
        'bordered' => false,
        'panelHeadingTemplate' => '
            <div class="kv-panel-pager pull-right" style="text-align:right">
                {pager}{summary}
            </div>
            <div class="clearfix"></div>
            ' . (empty($this->params['type']) ? '
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    ' . Html::dropDownList('RegistryBusinessSearch[user_in_charge]', (!empty(Yii::$app->request->get('RegistryBusinessSearch')['user_in_charge']) ? Yii::$app->request->get('RegistryBusinessSearch')['user_in_charge'] : null),
                            ArrayHelper::map(
                                User::find()->orderBy('full_name')->asArray()->all(),
                                'id',
                                function($data) {
                                    return $data['full_name'];
                                }
                            ),
                            [
                                'id' => 'registrybusiness-user_in_charge',
                                'class' => 'form-control',
                                'prompt' => Yii::t('app', 'User'),
                            ]
                    ) . '
                </div>
                <div class="col-lg-4 col-md-4">
                    ' . Html::dropDownList('RegistryBusinessSearch[district_id]', (!empty(Yii::$app->request->get('RegistryBusinessSearch')['district_id']) ? Yii::$app->request->get('RegistryBusinessSearch')['district_id'] : null),
                            ArrayHelper::map(
                                District::find()->orderBy('name')->asArray()->all(),
                                'id',
                                function($data) {
                                    return $data['name'];
                                }
                            ),
                            [
                                'id' => 'registrybusiness-district_id',
                                'class' => 'form-control',
                                'prompt' => Yii::t('app', 'District'),
                            ]
                    ) . '
                </div>
                <div class="col-lg-4 col-md-4">
                    ' . Html::dropDownList('RegistryBusinessSearch[village_id]', (!empty(Yii::$app->request->get('RegistryBusinessSearch')['village_id']) ? Yii::$app->request->get('RegistryBusinessSearch')['village_id'] : null),
                            ArrayHelper::map(
                                Village::find()->orderBy('name')->asArray()->all(),
                                'id',
                                function($data) {
                                    return $data['name'];
                                }
                            ),
                            [
                                'id' => 'registrybusiness-village_id',
                                'class' => 'form-control',
                                'prompt' => Yii::t('app', 'Village'),
                            ]
                    ) . '
                </div>
            </div>' : '')
        ,
        'panelFooterTemplate' => '
            <div class="kv-panel-pager pull-right" style="text-align:right">
                {summary}{pager}
            </div>
            {footer}
            <div class="clearfix"></div>'
        ,
        'panel' => [
            'heading' => '',
        ],
        'toolbar' => [
            [
                'content' => Html::a('<i class="fa fa-sync-alt"></i>', (empty($this->params['type']) ? ['index'] : ['index', 'type' => $this->params['type']]), [
                'id' => 'refresh',
                'class' => 'btn btn-success',
                'data-placement' => 'top',
                'data-toggle' => 'tooltip',
                'title' => 'Refresh'
                ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'created_at:date',
            'name',
            'unique_name',

            [
                'attribute' => 'membershipType.name',
                'format' => 'raw',
                'filter' =>  ArrayHelper::map(
                                MembershipType::find()->orderBy('order')->asArray()->all(),
                                'name',
                                function($data) {
                                    return $data['name'];
                                }
                            ),
            ],

            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' =>  ['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Incorrect' => 'Incorrect'],
                'value' => function ($model, $index, $widget) {
                    return $model->status;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '
                    <div class="btn-container hide">
                        <div class="visible-lg visible-md">
                            <div class="btn-group btn-group-md" role="group" style="width: 160px">
                                {view}{update}{delete}{resubmit}
                            </div>
                        </div>
                        <div class="visible-sm visible-xs">
                            <div class="btn-group btn-group-lg" role="group" style="width: 208px">
                                {view}{update}{delete}{resubmit}
                            </div>
                        </div>
                    </div>',
                'buttons' => [
                    'view' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-search-plus"></i>', (empty($this->params['type']) ? ['view', 'id' => $model->id] : ['view', 'id' => $model->id, 'type' => $this->params['type']]), [
                            'id' => 'view',
                            'class' => 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'View',
                        ]);
                    },
                    'update' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-pencil-alt"></i>', (empty($this->params['type']) ? ['update', 'id' => $model->id] : ['update', 'id' => $model->id, 'type' => $this->params['type']]), [
                            'id' => 'update',
                            'class' => 'btn btn-success',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Edit',
                        ]);
                    },
                    'delete' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-trash-alt"></i>', (empty($this->params['type']) ? ['delete', 'id' => $model->id] : ['delete', 'id' => $model->id, 'type' => $this->params['type']]), [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'data-not-ajax' => 1,
                            'title' => 'Delete',
                            'model-id' => $model->id,
                            'model-name' => $model->name,
                        ]);
                    },
                    'resubmit' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-share-square"></i>', (empty($this->params['type']) ? ['resubmit', 'id' => $model->id] : ['resubmit', 'id' => $model->id, 'type' => $this->params['type']]), [
                            'id' => 'resubmit',
                            'class' => 'btn btn-default',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Resubmit',
                        ]);
                    },
                ]
            ],
        ],
        'tableOptions' => [
            'class' => 'table table-striped table-hover'
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['id' => $model['id'], 'class' => 'row-grid-view-registry-business', 'style' => 'cursor: pointer;'];
        },
        'pager' => [
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
            'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
            'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
        ],
    ]); ?>

</div>

<?= $modalDialog->renderDialog() ?>

<?php
$jscript = ''
    . $modalDialog->getScript() . '

    $("div.container.body").off("click");
    $("div.container.body").on("click", function(event) {

        if ($(event.target).parent(".row-grid-view-registry-business").length > 0) {

            $("td").not(event.target).popover("destroy");
        } else {
            $(".popover.in").popover("destroy");
        }
    });

    $(".row-grid-view-registry-business").popover({
        trigger: "click",
        placement: "top",
        container: ".row-grid-view-registry-business",
        html: true,
        selector: "td",
        content: function () {
            var content = $(this).parent().find(".btn-container").html();

            return $(content);
        }
    });

    $(".row-grid-view-registry-business").on("shown.bs.popover", function(event) {

        $(\'[data-toggle="tooltip"]\').tooltip();

        var popoverId = $(event.target).attr("aria-describedby");

        $(document).on("click", "#" + popoverId + " a", function(event) {

            if ($(this).attr("data-not-ajax") == undefined) {
                ajaxRequest($(this));
            }

            return false;
        });
    });

    $("#registrybusiness-user_in_charge").select2({
        theme: "krajee",
    });

    $("#registrybusiness-district_id").select2({
        theme: "krajee",
    });

    $("#registrybusiness-village_id").select2({
        theme: "krajee",
    });
';

$this->registerJs($jscript); ?>