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
    public $defaultRoute = 'registry-business/create';
    public $name = 'Marketing';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Yii::configure($this, require __DIR__ . '/config/navigation.php');
    }
}
