<?php

namespace App\Constants;
$action = [
    'remove'     => 'remove',
    'approve'    => 'approve',    
    'viewed'     => 'viewed',
    'disapprove' => 'disapprove',
    'create'     => 'create',
    'move'       => 'move',
    'pending'    => 'pending',
    'in-process' => 'in-process',
    'start'      => 'start',
    'done'       => 'done',
    'access'     => 'access',
    'disaccess'  => 'disaccess',
    'project-management' => 'project-management',
];
$types = [
    'task'       => 'task',
    'project'    => 'project',
    'messenger'  => 'messenger',
    'access_project'  => 'access_project',
];
define('ACTION', $action);
define('TYPES', $types);