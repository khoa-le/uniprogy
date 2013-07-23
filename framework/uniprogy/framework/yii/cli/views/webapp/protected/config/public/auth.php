<?php
return array (
  'guest' => 
  array (
    'type' => 2,
    'description' => 'Guest',
    'bizRule' => NULL,
    'data' => NULL,
  ),
  'administrator' => 
  array (
    'type' => 2,
    'description' => 'Administrator',
    'bizRule' => NULL,
    'data' => NULL,
    'children' => 
    array (
      0 => 'guest',
    ),
  ),
);
