<?php

namespace backoffice\modules\marketing\controllers;

use Yii;
use core\models\Person;
use core\models\RegistryBusinessContactPerson;
use yii\filters\VerbFilter;

/**
 * DataController
 */
class DataController extends \backoffice\controllers\BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [

                    ],
                ],
            ]);
    }

    public function actionFormContactPerson() {

        $this->ajaxLayout = '@backoffice/views/layouts/ajax-zero';

        return $this->render('form_contact_person', [
            'modelPerson' => new Person(),
            'modelRegistryBusinessContactPerson' => new RegistryBusinessContactPerson(),
        ]);
    }
}