<?php
 return array (
  'name' => 'My Website',
  'components' => 
  array (
    'db' => 
    array (
      'connectionString' => 'mysql:host=localhost;dbname=',
      'username' => '',
      'password' => '',
      'tablePrefix' => 'unip_',
    ),
    'urlManager' => 
    array (
      'urlFormat' => 'path',
      'showScriptName' => false,
      'rules' => 
      array (
        'page/<view>' => 'base/page',
      ),
    ),
  ),
);