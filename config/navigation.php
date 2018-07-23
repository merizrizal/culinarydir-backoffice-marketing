<?php
return [
    'membership' => [
        'label' => 'Membership',
        'iconClass' => 'fa fa-star',
        'navigation' => [
            'allRegistry' => [
                'label' => 'All Registry',
                'url' => ['marketing/registry-business/index'],
            ],
            'myRegistry' => [
                'label' => 'My Registry',
                'url' => ['marketing/registry-business/index', 'type' => 'my'],
            ],
        ]
    ],
];