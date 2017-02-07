<?php
$settings = array(
    'test_index' => array(
        'route' => '/',
        'action' => 'index',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/DefaultController',
        'access' => ''
    ),
    'test_load' => array(
        'route' => '/load',
        'action' => 'load',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/DefaultController',
        'access' => ''
    ),
    'auth_registration' => array(
        'route' => '/registration',
        'action' => 'registration',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/AuthController',
        'access' => ''
    ),
    'auth_login' => array(
        'route' => '/login',
        'action' => 'login',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/AuthController',
        'access' => ''
    ),
    'comment_create' => array(
        'route' => '/comment/create',
        'action' => 'create',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/CommentController',
        'access' => ''
    ),
    'comment_rating' => array(
        'route' => '/comment/rating',
        'action' => 'rating',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/CommentController',
        'access' => ''
    ),
    'comment_update' => array(
        'route' => '/comment/update',
        'action' => 'update',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/CommentController',
        'access' => ''
    ),
    'comment_delete' => array(
        'route' => '/comment/delete',
        'action' => 'delete',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/CommentController',
        'access' => ''
    ),
    'comment_nested' => array(
        'route' => '/comment/create-nested',
        'action' => 'createNestedComment',
        'template' => null,
        'requirements' => [],
        'controller' => 'Controller/CommentController',
        'access' => ''
    )

);


return $settings;