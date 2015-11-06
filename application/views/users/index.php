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

    <title>Dataigniter sample</title>

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
      <div class="text-right reset_button">
        <a href="#" id="reset_all" class="btn btn-danger">Reset all filters</a>
      </div>
      
      <table id="users" class="display" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>user_id</th>
            <th>first_name</th>
            <th>last_name</th>
            <th>email</th>
            <th>country</th>
            <th>ip_address</th>
            <th>logged_at</th>
          </tr>
        </thead>

        <tfoot>
          <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>

    </div><!-- /.container -->

    <style>
      /* Simple css ruel here for convenience */
      .reset_button { margin:50px 0; }
      table { width:100%; }
      .yadcf-filter-wrapper input[type='text'] { width:100%; }
      tfoot { display: table-header-group; }
      table.dataTable tfoot th { padding:5px; }
      .yadcf-filter-reset-button { display:none; }
    </style>

  <script>
  $(document).ready(function() {

    var oTable = $('#users').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type": "POST",
        "url": "/index.php/users/dataigniter"
      }
    });

    yadcf.init(
      oTable, 
      [
        { "column_number": 0, "filter_type": "text" },
        { "column_number": 1, "filter_type": "text" },
        { "column_number": 2, "filter_type": "text" },
        { "column_number": 3, "filter_type": "text" },
        { "column_number": 4, "filter_type": "text" },
        { "column_number": 5, "filter_type": "text" },
        { "column_number": 6, "filter_type": "range_date", "date_format": "dd-mm-yyyy" }
      ],
      'footer'
    );

    $("#reset_all").click(function(e) { e.preventDefault(); yadcf.exResetAllFilters(oTable); });
    
  });
  </script>

  </body>
</html>
