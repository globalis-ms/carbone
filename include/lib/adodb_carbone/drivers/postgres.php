<?php

class ADODB_postgres extends ADOConnection{
    var $link;

    function __construct(){
    }

    function Connect($hostname, $username, $password, $database_name){
        $this->link = pg_connect('host='.$hostname.' port=5432 dbname='.$database_name.' user='.$username.' password='.$password);

        if(!$this->link){
           echo $this->ErrorMsg();
           return FALSE;
        }
        
        return TRUE;
    }

    function Execute($sql){
        $rs = new ADORecordSet_postgres();

        $rs->result = pg_query($this->link, $sql);
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

    function Insert_ID($table, $column){
        $rs = $this->Execute('SELECT last_value FROM '.$table.'_'.$column.'_seq');

        if($row = $rs->FetchRow()){
            list(, $tmp) = each($row);
            $rs->Close();
            return $tmp;
        }else{
            return false;
        }
    }

    function qstr($str){
        $str = '\''.pg_escape_string($str).'\'';
        return str_replace('\"', '"', $str);
    }

    function Close(){
        if(!pg_close($this->link)){
            exit($this->ErrorMsg());
        }
        parent::LogSQL('---');
    }

    function ErrorMsg(){
        return pg_last_error($this->link);
    }
}

class ADORecordSet_postgres extends ADORecordSet{
    var $result;

    function __construct(){
    }

    function FetchRow(){
        return pg_fetch_assoc($this->result);
    }

    function RecordCount(){
        return pg_num_rows($this->result);
    }

    function Close(){
        pg_free_result($this->result);
    }
}

?>