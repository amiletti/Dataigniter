<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Datainginter {

  protected $CI;
  protected $config;

  public function __construct($config = array())
  {
    $this->CI     =& get_instance();
    $this->config = $config;
    $this->init_data_input();
  }

  public function init_data_input()
  {
    $request = $this->config['request'];
    $columns = $this->config['columns'];

    foreach($request['columns'] as $k => $v)
    {
      foreach($columns as $k2 => $v2)
      {
        if($v2['dt'] == $v['data'] && isset($v2['pre_formatter']))
        {
          $t = $v2['pre_formatter']($request['columns'][$k]['search']['value'], $this->config);
          $this->config['request']['columns'][$k]['search']['value'] = $t;
        }
      }
    }

    return;
  }

  public function data_output($columns, $data)
  {
    $out = array();

    foreach($data as $k => $v)
    {
      $row = array();

      foreach($columns as $k2 => $v2)
      {
        if(isset($v2['post_formatter']))
        {
          $row[$v2['dt']] = $v2['post_formatter']($v[$v2['db']], $v);
        }
        else
        {
          $row[$v2['dt']] = $v[$v2['db']];
        }
      }

      $out[] = $row;
    }

    return $out;
  }

  public function limit()
  {
    $request = $this->config['request'];
    $columns = $this->config['columns'];
    
    $limit = '';
    if(isset($request['start']) && $request['length'] != -1)
    {
      $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
    }

    return $limit;
  }

  public function order()
  {
    $request = $this->config['request'];
    $columns = $this->config['columns'];
    
    $order = '';

    if(isset($request['order']) && count($request['order']))
    {
      $order_by   = array();
      $dt_columns = $this->pluck($columns, 'dt');

      foreach($request['order'] as $k => $v)
      {
        $column_idx     = intval($v['column']);
        $request_column = $request['columns'][$column_idx];

        $column_idx = array_search($request_column['data'], $dt_columns);
        $column     = $columns[$column_idx];

        if($request_column['orderable'] == 'true')
        {
          $dir = $v['dir'] === 'asc' ? 'ASC' : 'DESC';
          $order_by[] = '`'.$column['db'].'` '.$dir;
        }
      }

      $order = 'ORDER BY '.implode(', ', $order_by);
    }

    return $order;
  }

  public function where()
  {
    $request = $this->config['request'];
    $columns = $this->config['columns'];

    $global_search = array();
    $column_search = array();
    $dt_columns    = $this->pluck($columns, 'dt');

    if(isset($request['search']) && $request['search']['value'] != '')
    {
      $str = $request['search']['value'];

      foreach($request['columns'] as $k => $v)
      {
        $column_idx     = array_search($v['data'], $dt_columns);
        $column         = $columns[$column_idx];

        if($v['searchable'] == 'true')
        {
          $global_search[] = "`".$column['db']."` ".$this->bind($str);
        }
      }
    }

    if(isset($request['columns']))
    {
      foreach($request['columns'] as $k => $v)
      {
        $column_idx     = array_search($v['data'], $dt_columns);
        $column         = $columns[$column_idx];

        $str = $v['search']['value'];
        if($v['searchable'] == 'true' && $str != '')
        {
          $column_search[] = "`".$column['db']."` ".$this->bind($str);
        }
      }
    }

    $where = '';

    if(count($global_search))
    {
      $where = '('.implode(' OR ', $global_search).')';
    }

    if(count($column_search))
    {
      $where = ($where === '') ? implode(' AND ', $column_search) : $where.' AND '.implode(' AND ', $column_search);
    }

    if($where !== '') { $where = 'WHERE '.$where; }

    return $where;
  }

  public function get_data()
  {
    $columns      = $this->config['columns'];
    $table        = $this->config['table'];
    $where_result = (isset($this->config['where_result'])) ? $this->config['where_result'] : '';
    $where_all    = (isset($this->config['where_all'])) ? $this->config['where_all'] : '';

    $local_where_result = array();
    $local_where_all    = array();
    $where_all_sql      = '';

    $limit = $this->limit();
    $order = $this->order();
    $where = $this->where();

    $where_result = $this->flatten($where_result);
    $where_all    = $this->flatten($where_all);

    if($where_result)
    {
      $where = ($where) ? $where .' AND '.$where_result : 'WHERE '.$where_result;
    }

    if($where_all)
    {
      $where = ($where) ? $where .' AND '.$where_all : 'WHERE '.$where_all;
      $where_all_sql = 'WHERE '.$where_all;
    }

    $q = "SELECT `".implode("`, `", $this->pluck($columns, 'db'))."`
          FROM {$table}
          {$where}
          {$order}
          {$limit}";
    $data = $this->CI->db->query($q)->result_array();
    //echo $this->CI->db->last_query();

    $q = "SELECT COUNT(*) AS n FROM {$table} {$where}";
    $r = $this->CI->db->query($q)->row();
    $records_filtered = ($r) ? $r->n : 0;

    $q = "SELECT COUNT(*) AS n FROM {$table} {$where_all_sql}";
    $r = $this->CI->db->query($q)->row();
    $records_total = ($r) ? $r->n : 0;

    return array(
      "draw"            => isset($request['draw']) ? intval( $request['draw'] ) : 0,
      "recordsFiltered" => intval($records_filtered),
      "recordsTotal"    => intval($records_total),
      "data"            => $this->data_output($columns, $data)
    );
  }

  public function pluck($columns, $property)
  {
    $ret = array();
    foreach($columns as $k => $v) { $ret[] = $v[$property]; }

    return $ret;
  }

  public function bind($val)
  {
    $q = '';

    if(is_numeric($val))
    {
      $q = " = ".$this->CI->db->escape($val)." ";
    }
    else if(strrpos($val, $this->config['range_delimiter']) !== FALSE)
    {
      $t = explode($this->config['range_delimiter'], $val);
      
      if($t[0] && $t[1])
      {
        $q = " BETWEEN ".$this->CI->db->escape($t[0])." AND ".$this->CI->db->escape($t[1])." ";
      }
      else if($t[0] && ! $t[1])
      {
        $q = " > ".$this->CI->db->escape($t[0])." ";
      }
      else if($t[1] && ! $t[0])
      {
        $q = " < ".$this->CI->db->escape($t[1])." ";
      }
    }
    else
    {
      $q = " LIKE '%".$this->CI->db->escape_like_str($val)."%'";
    }

    return $q;
  }

  public function flatten($where, $join = ' AND ')
  {
    if( ! $where )
    {
      return '';
    }
    else if($where && is_array($where))
    {
      return implode($join, $where);
    }

    return $where;
  }

}