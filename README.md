#php_mysql_class


##Example

  <?php  
    require("mysql.inc.php");
  
  // inital object ins as mysql class  
    $ins = new mysql;  
  
  // connect to mysql server  
    $ins->connect("hostname","user","psw","dbname");  
  
  // print mysql server version  
    print $ins->version();  
  
  // set sql statement -- no need ";"  
    $sql = "SELECT * FROM table";  
  
  // mysql query  
    $result = $ins->query($sql);  
  
  // a good method for fetch data from mysql query result  
    for ($i=0;$i<$ins->num_rows($result);$i++){  
      $row=$ins->fetch_row($result);  
      for ($k=0;$k<$ins->num_fields($result);$k++){  
        print $row[$k];  
        print "\n";  
      }  
      print "row end......................\n";  
    }  
  
  // if you need to release result memory resource, and the result cannot be used  
    $ins->free_result($result);  
  
  // close mysql connection  
    $ins->close();  
?>  
