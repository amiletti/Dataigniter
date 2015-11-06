<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['users'] = array(
  'table'           => 'users',
  'where_result'    => FALSE,
  'where_all'       => FALSE,
  'range_delimiter' => '-yadcf_delim-',
  'columns'         => array(
    array('dt' => 0, 'db' => 'user_id'),
    array('dt' => 1, 'db' => 'first_name'),
    array('dt' => 2, 'db' => 'last_name'),
    array(
      'dt' => 3, 
      'db' => 'email',
      'post_formatter' => function($v, $row) { return ($v) ? '<a href="mailto:'.$v.'">'.$v.'</a>' : ''; }
    ),
    array('dt' => 4, 'db' => 'country'),
    array('dt' => 5, 'db' => 'ip_address'),
    array(
      'dt' => 6, 
      'db' => 'logged_at',
      'pre_formatter' => function($v, $config)
      {
        if( ! $v) { return ''; }
        $t = explode($config['range_delimiter'], $v);
        $t[0] = ($t[0]) ? DateTime::createFromFormat('d-m-Y', $t[0])->format('Y-m-d') : '';
        $t[1] = ($t[1]) ? DateTime::createFromFormat('d-m-Y', $t[1])->format('Y-m-d') : '';
        return implode($config['range_delimiter'], $t);
      },
      'post_formatter' => function($v, $row) { return ($v) ? date("d-m-Y H:i:s", strtotime($v)) : ''; }
    )
  )
);

// $config['other_table'] = '';

