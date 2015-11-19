<?php

class ADODB_sqlite extends ADOConnection{
    var $link;

    function __construct(){
    }

    function Connect($hostname, $username, $password, $database_name){
        $this->link = sqlite_open($hostname);

        if(!$this->link){
            exit($this->ErrorMsg());
        }
    }

    function Execute($sql){
        $rs = new ADORecordSet_sqlite();

        $rs->result = sqlite_query($this->link, $sql);
        if(!$rs->result){
            exit($this->ErrorMsg());
        }

        parent::LogSQL($sql);

        return $rs;
    }

    function SelectLimit($sql, $numrow, $offset = 0){
        $sql.= ' LIMIT '.$numrow.' OFFSET '.$offset;
        return $this->Execute($sql);
    }

    function Insert_ID($table = null, $column = null){
        return sqlite_last_insert_rowid($this->link);
    }

    function qstr($str){
        $str = '\''.sqlite_escape_string($str).'\'';
        return str_replace('\"', '"', $str);
    }

    function Close(){
        sqlite_close($this->link);
    }

    function ErrorMsg(){
        return '['.sqlite_last_error($this->link).'] '.sqlite_error_string(sqlite_last_error($this->link));
    }
}

class ADORecordSet_sqlite extends ADORecordSet{
    var $result;

    function __construct(){
    }

    function FetchRow(){
        return sqlite_fetch_array($this->result, SQLITE_ASSOC);
    }

    function RecordCount(){
        return sqlite_num_rows($this->result);
    }

    function Close(){
    }
}

?>