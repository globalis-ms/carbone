<?php

class ADODB_mysql extends ADOConnection{
    var $link;

    function __construct(){
    }

    function Connect($hostname, $username, $password, $database_name){
        $this->link = mysql_connect($hostname, $username, $password, true);

        if(!$this->link){
            echo $this->ErrorMsg();
            return FALSE;
        }

        if(!mysql_select_db($database_name, $this->link)){
            echo $this->ErrorMsg();
            return FALSE;
        }
        
        return TRUE;
    }

    function Execute($sql){
        $rs = new ADORecordSet_mysql();

        $rs->result = mysql_query($sql, $this->link);
        if(!$rs->result){
            if(CFG_DEBUG)
                echo($this->ErrorMsg().' [SQL Query] '.$sql);
            else
                echo($this->ErrorMsg().' [SQL Query] ');
        }
        
        parent::LogSQL($sql);

        return $rs;
    }

    function SelectLimit($sql, $numrow, $offset = 0){
        $sql.= ' LIMIT '.$offset.','.$numrow;
        return $this->Execute($sql);
    }

    function Insert_ID($table = null, $column = null){
        return mysql_insert_id($this->link);
    }

    function qstr($str){
        $str = '\''.mysql_real_escape_string($str, $this->link).'\'';
        return str_replace('\"', '"', $str);
    }

    function Close(){
        if(!mysql_close($this->link)){
            exit($this->ErrorMsg());
        }
        parent::LogSQL('---');
    }

    function ErrorMsg(){
        return '['.mysql_errno($this->link).'] '.mysql_error($this->link);
    }
}

class ADORecordSet_mysql extends ADORecordSet{
    var $result;

    function __construct(){
    }

    function FetchRow(){
        return mysql_fetch_assoc($this->result);
    }

    function RecordCount(){
        return mysql_num_rows($this->result);
    }

    function Close(){
        mysql_free_result($this->result);
    }
}

?>