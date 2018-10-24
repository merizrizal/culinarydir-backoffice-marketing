<?php
return [
    'params' => [
        'navigation' => [
            'createApplication'=> [
                'label' => 'Create Application',
                'iconClass' => 'fa fa-edit',
                'url' => ['marketing/registry-business/create'],
                'isDirect' => false,
            ],
            'pndgApplication'=> [
                'label' => 'Pending Application',
                'iconClass' => 'fa fa-hourglass-half',
                'url' => ['marketing/registry-business/index-pndg'],
                'isDirect' => false,
            ],
            'icorctApplication'=> [
                'label' => 'Incorrect Application',
                'iconClass' => 'fa fa-exclamation-circle',
                'url' => ['marketing/registry-business/index-icorct'],
                'isDirect' => false,
            ],
            'rjctApplication'=> [
                'label' => 'Reject Application',
                'iconClass' => 'fa fa-window-close',
                'url' => ['marketing/registry-business/index-rjct'],
                'isDirect' => false,
            ],
            'member'=> [
                'label' => 'Member',
                'iconClass' => 'fa fa-users',
                'url' => ['marketing/business/member'],
                'isDirect' => false,
            ],
        ]
    ]
];