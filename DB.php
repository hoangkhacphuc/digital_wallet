<?php
    $conn = db_connect();

    function db_connect()
    {
        $conn = mysqli_connect('localhost', DB_User, DB_Pass, DB_Name);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return $conn;
    }
    
    function db_insert($table, $data)
    {
        $str_keys = "";
        $str_values = "";
        foreach ($data as $key => $value)
        {
            $str_keys .= "`$key`,";
            $str_values .= "'$value',";
        }
        $str_keys = substr($str_keys, 0, -1);
        $str_values = substr($str_values, 0, -1);
        $query = "INSERT INTO `$table` ($str_keys) VALUES ($str_values)";
        $result = $GLOBALS['conn']->query($query);
        if (!$result)
            return false;

        $result = mysqli_insert_id($GLOBALS['conn']);
        $result = db_select($table, "`id` = $result");

        return $result;
    }
    
    function db_update($table, $data, $where)
    {
        $str_updates = "";
        foreach ($data as $key => $value)
        {
            $str_updates .= "`$key` = '$value',";
        }
        $str_updates = substr($str_updates, 0, -1);
        $query = "UPDATE `$table` SET $str_updates WHERE $where";
        $result = $GLOBALS['conn']->query($query);
        return $result;
    }
    
    function db_delete($table, $where)
    {
        $query = "DELETE FROM `$table` WHERE $where";
        $result = $GLOBALS['conn']->query($query);
        return $result;
    }
    
    function db_select($table,$where)
    {
        $query = "SELECT * FROM `$table` WHERE $where";
        $result = $GLOBALS['conn']->query($query);
        $arr = array();
        if ($result && $result->num_rows > 0) {
    
            while($row = $result->fetch_assoc()) {
                array_push($arr, $row);
            }
        }
        return $arr;
    }
    
    function db_select_query($query)
    {
        $result = $GLOBALS['conn']->query($query);
        $arr = array();
        if ($result->num_rows > 0) {
    
            while($row = $result->fetch_assoc()) {
                array_push($arr, $row);
            }
        }
        return $arr;
    }
?>