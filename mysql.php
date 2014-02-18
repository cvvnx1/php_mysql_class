<?php

class mysql{
	// Define public variable
  public $version = '';
  public $querynum = 0;
  public $link;

/*
mysql connection system functions
  use for connect to a mysql sock
  and select db, watch connection status, close connections

  sample code:
    

*/
  // connect function
  function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE, $dbcharset2 = ''){
    $func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';

    if(!$this->link = @$func($dbhost, $dbuser, $dbpw, 1)){
      $halt && $this->notice('Can not connect to MySQL server');
    }else{
      if($this->version() > '4.1'){
        global $charset, $dbcharset;
        $dbcharset = $dbcharset2 ? $dbcharset2 : $dbcharset;
        $dbcharset = !$dbcharset && in_array(strtolower($charset),array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
        $serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
        $serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
        $serverset && mysql_query("SET $serverset", $this->link);
      }
    $dbname && @mysql_select_db($dbname, $this->link);
    }
  }

  // version function -- get mysql version information
  function version(){
    if(empty($this->version)){
      $this->version = mysql_get_server_info($this->link);
    }
    return $this->version;
  }

  // select_db function -- select mysql database
  function select_db($dbname){
    return mysql_select_db($dbname, $this->link);
  }

  // error function -- fetch the last connect error imformation
  function error(){
    return (($this->link) ? mysql_error($this->link) : mysql_error());
  }

  // errno function -- fetch the last connect error code
  function errno(){
    return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
  }

  // close function -- close mysql connection
  function close(){
    return mysql_close($this->link);
  }

/*
mysql query and count functions
  use for execute query in a connected mysql sock
  and watch affected rows, watch insert id

  sample code:
    

*/

  // query function -- execute mysql query @$this->link
  function query($sql, $type = ''){
    $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
    if(!($query = $func($sql, $this->link))){
      if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY'){
        $this->close();
        $this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, TRUE, $dbcharset);
        $this->query($sql, 'RETEY'.$type);
      }elseif($type != 'SILENT' && substr($type, 5) != 'SILENT'){
        $this->notice('MySQL Query Error', $sql);
      }
    }
    $this->querynum++;
    return $query;
  }

  // affected_rows function -- fetch the affected rows of last operation
  function affected_rows(){
    return mysql_affected_rows($this->link);
  }

  // insert_id function -- fetch insert id of last insert operation
  function insert_id(){
    return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"),0);
  }

/*
result status functions
  use for watch result status:
    row number of result
    field number of result

  sample code:
    

*/

  // num_rows function -- return row number of mysql query result
  function num_rows($query){
    return mysql_num_rows($query);
  }

  // num_fields function -- return field number of mysql query result
  function num_fields($query){
    return mysql_num_fields($query);
  }

  // free_result function -- release result memory
  function free_result($query){
    return mysql_free_result($query);
  }

/*
fetch data from result functions
  use for fetch data from query result:
    fetch result
    fetch array
    fetch first
    fetch row
    fetch field

  sample code:
    

*/

  // result function -- return mysql query result
  function result($query, $row = 0){
    $query = @mysql_result($query, $row);
    return $query;
  }

  // fetch_array function -- fetch associative array from result
  function fetch_array($query, $result_type = MYSQL_ASSOC){
    return mysql_fetch_array($query, $result_type);
  }

  // fetch first function
  function fetch_first($sql){
    return $this->fetch_array($this->query($sql));
  }

  // fetch_row function -- fetch row array from result
  function fetch_row($query){
    return mysql_fetch_row($query);
  }

  // fetch_fields function -- fetch field array from result
  function fetch_fields($query){
    return mysql_fetch_field($query);
  }

/*
class 
  use for execute query in a connected mysql sock
  and watch affected rows, watch insert id

  sample code:
    

*/

  // notice function -- return error information for this class
  function notice($message = 'run mysql have a error at ', $sql = ''){
    return $message.$sql;
  }

}

?>