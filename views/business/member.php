<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use sycomponent\AjaxRequest;
use sycomponent\ModalDialog;
use sycomponent\NotificationDialog;
use core\models\District;
use core\models\Village;
use core\models\MembershipType;

/* @var $this yii\web\View */
/* @var $searchModel core\models\search\BusinessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$ajaxRequest = new AjaxRequest([
    'modelClass' => 'Business',
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

$this->title = Yii::t('app', 'Member');
$this->params['breadcrumbs'][] = $this->title;

echo $ajaxRequest->component(false); ?>

<div class="business-index">

    <?php
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);

    echo GridView::widget([
        'id' => 'grid-view-business',
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
                    ' . Html::dropDownList('BusinessSearch[businessLocation.district_id]', (!empty(Yii::$app->request->get('BusinessSearch')['businessLocation.district_id']) ? Yii::$app->request->get('BusinessSearch')['businessLocation.district_id'] : null),
                            ArrayHelper::map(
                                District::find()->orderBy('name')->asArray()->all(),
                                'id',
                                function($data) {
                                    return $data['name'];
                                }
                            ),
                            [
                                'id' => 'business-district_id',
                                'class' => 'form-control',
                                'prompt' => Yii::t('app', 'District'),
                            ]
                    ) . '
                </div>
                <div class="col-lg-4 col-md-4">
                    ' . Html::dropDownList('BusinessSearch[businessLocation.village_id]', (!empty(Yii::$app->request->get('BusinessSearch')['businessLocation.village_id']) ? Yii::$app->request->get('BusinessSearch')['businessLocation.village_id'] : null),
                            ArrayHelper::map(
                                Village::find()->orderBy('name')->asArray()->all(),
                                'id',
                                function($data) {
                                    return $data['name'];
                                }
                            ),
                            [
                                'id' => 'business-village_id',
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
                'content' => Html::a('<i class="fa fa-sync-alt"></i>', ['member'], [
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '
                    <div class="btn-container hide">
                        <div class="visible-lg visible-md">
                            <div class="btn-group btn-group-md" role="group" style="width: 80px">
                                {view-member}{upgrade-member}
                            </div>
                        </div>
                        <div class="visible-sm visible-xs">
                            <div class="btn-group btn-group-lg" role="group" style="width: 104px">
                                {view-member}{upgrade-member}
                            </div>
                        </div>
                    </div>',
                'buttons' => [
                    'view-member' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-search-plus"></i>', ['view-member', 'id' => $model->id], [
                            'id' => 'view',
                            'class' => 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'View',
                        ]);
                    },
                    'upgrade-member' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-level-up-alt"></i>', ['upgrade-member', 'id' => $model->id], [
                            'id' => 'upgrade-membership',
                            'class' => 'btn btn-default',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Upgrade Membership',
                        ]);
                    },
                ]
            ],
        ],
        'tableOptions' => [
            'class' => 'table table-striped table-hover'
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['id' => $model['id'], 'class' => 'row-grid-view-business', 'style' => 'cursor: pointer;'];
        },
        'pager' => [
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
            'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
            'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
        ],
    ]); ?>

</div>

<?php 
echo $modalDialog->renderDialog();

$jscript = ''
    . $modalDialog->getScript() . '

    $("div.container.body").off("click");
    $("div.container.body").on("click", function(event) {

        if ($(event.target).parent(".row-grid-view-business").length > 0) {

            $("td").not(event.target).popover("destroy");
        } else {
            $(".popover.in").popover("destroy");
        }
    });

    $(".row-grid-view-business").popover({
        trigger: "click",
        placement: "top",
        container: ".row-grid-view-business",
        html: true,
        selector: "td",
        content: function () {
            var content = $(this).parent().find(".btn-container").html();

            return $(content);
        }
    });

    $(".row-grid-view-business").on("shown.bs.popover", function(event) {

        $(\'[data-toggle="tooltip"]\').tooltip();

        var popoverId = $(event.target).attr("aria-describedby");

        $(document).on("click", "#" + popoverId + " a", function(event) {

            if ($(this).attr("data-not-ajax") == undefined) {
                ajaxRequest($(this));
            }

            return false;
        });
    });

    $("#business-district_id").select2({
        theme: "krajee",
    });

    $("#business-village_id").select2({
        theme: "krajee",
    });
';

$this->registerJs($jscript); ?>