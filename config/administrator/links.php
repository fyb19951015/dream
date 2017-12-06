<?php

use App\Models\Link;

return [
    'title' => '资源推荐',
    'single' => '资源推荐',

    'model' => Link::class,

    'permission' => function () {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('Founder');
    },

    'columns' => [
        'id' => [
            'title' => 'ID'
        ],
        'title' => [
            'title' => '名称',
            'sortable' => false,
        ],
        'operation' => [
            'title' => '管理',
            'sortable' => false,
        ],
    ],

    'edit_fields'=> [
        'title'=>[
            'title'=>'名称'
        ],
        'link'=>[
            'title'=>'连接'
        ],
    ],
    'filters'=>[
        'id'=>[
            'title'=>'标签 ID'
        ],
        'title'=>[
            'title'=>'名称'
        ]
    ],
];