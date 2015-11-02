# Dataigniter
A library to use Datatables in Codeigniter with full support for server side process
### How to install
Simply copy /libraries/Dataigniter.php in your /application/libraries/
### How to load
Like any other CI libraries
```php
$this->load->library('Datainginter');
```
### How to use
First of all you need a controller to load the main table view
###### application/controllers/users.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

	public function index()
	{
		$this->load->view('users');
	}
	
}
```
and the related view (in this sample all file are loaded via cdn, except [yadtcf](https://github.com/vedmack/yadcf))
###### application/views/users.php
```html
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Starter Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" rel="stylesheet">

    <!-- Bootstrap core JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
    <script src="/assets/js/jquery.dataTables.yadcf.js"></script>
  </head>

  <body>

    <div class="container">
      <a href="#" id="reset_all">Reset all</a>
      <table id="example" class="display" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>user_id</th>
            <th>Name</th>
            <th>Surname</th>
            <th>Working place</th>
            <th>Status</th>
            <th>Last login</th>
          </tr>
        </thead>

        <tfoot>
          <tr>
            <th>user_id</th>
            <th>Name</th>
            <th>Surname</th>
            <th>Working place</th>
            <th>Status</th>
            <th>Last login</th>
          </tr>
        </tfoot>
      </table>

    </div><!-- /.container -->

  <script>
  $(document).ready(function() {

    var oTable = $('#example').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type": "POST",
        "url": "/datatables/users"
      },
    });

    yadcf.init(
      oTable, 
      [
        { "column_number": 0, "filter_type": "text" },
        { "column_number": 1, "filter_type": "text" },
        { "column_number": 2, "filter_type": "text" },
        { "column_number": 3, "filter_type": "select" },
        { "column_number": 4, "filter_type": "select" },
        { "column_number": 5, "filter_type": "range_date" },
      ],
      'footer'
    );

    $("#reset_all").click(function(e) { e.preventDefault(); yadcf.exResetAllFilters(t); });
    
  });
  </script>

  </body>
</html>
```
after this you need a controller where manage datatable request, in this sample /datatables/users
###### /application/controllers/datatables.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datatables extends CI_Controller {

  public function index()
  {
    show_404();
  }

  public function users()
  {
    $this->load->database();
    $this->load->helper('common'); // for a simple date formatter function

    $columns = array(
      array('dt' => 0, 'db' => 'user_:id'),
      array('dt' => 1, 'db' => 'name'),
      array('dt' => 2, 'db' => 'surname'),
      array('dt' => 3, 'db' => 'working_place'),
      array(
        'dt' => 4, 
        'db' => 'status',
        'pre_formatter' => function($v, $config) { return ($v) ? $v : 1; },
        'post_formatter' => function($v, $row)
        {
            $ico = ($v) ? 'active.png' : 'inactive.png';
            return '<img src="/assets/img/'.$ico.'" alt=""/>';
        }
      ),
      array(
        'dt' => 5, 
        'db' => 'last_login',
        'pre_formatter' => function($v, $config)
        {
          if( ! $v) { return; }
          $t = explode($config['range_delimiter'], $v);
          $t[0] = format_date($t[0].' 00:00:00', 'm/d/Y H:i:s', 'Y-m-d H:i:s');
          $t[1] = format_date($t[1].' 23:59:59', 'm/d/Y H:i:s', 'Y-m-d H:i:s');
          return implode($config['range_delimiter'], $t);

        },
        'post_formatter' => function($v, $row) { return format_date($v, 'Y-m-d H:i:s', 'm/d/Y'); }
      )
    );

    $config = array(
      'request'         => $this->input->post(), // or get from /application/users/index
      'table'           => 'datatable_users',
      'where_result'    => FALSE,
      'where_all'       => 'place_id = 447',
      'columns'         => $columns,
      'range_delimiter' => '-yadcf_delim-'
    );

    $this->load->library('Datainginter', $config);
    echo json_encode($this->datainginter->get_data());
  } 

}
```
Done
