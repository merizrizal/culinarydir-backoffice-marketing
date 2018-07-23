<?php

namespace backoffice\modules\marketing;

use Yii;

/**
 * marketing module definition class
 */
class MarketingModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'backoffice\modules\marketing\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Yii::$app->params['navigation'] = require __DIR__ . '/config/navigation.php';
    }
}
