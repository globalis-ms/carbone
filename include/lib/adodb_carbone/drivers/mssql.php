<?php

class ADODB_mssql extends ADOConnection{
    var $link;

    function ADODB_mssql(){
    }

    function Connect($hostname, $username, $password, $database_name){
        $this->link = mssql_connect($hostname, $username, $password);

        if(!$this->link){
            exit($this->ErrorMsg());
        }

        if(!mssql_select_db($database_name, $this->link)){
            exit($this->ErrorMsg());
        }
    }

    function Execute($sql){
        $rs = new ADORecordSet_mssql();

        $rs->result = mssql_query($sql, $this->link);
        if(!$rs->result){
            exit($this->ErrorMsg());
        }

        parent::LogSQL($sql);

        return $rs;
    }

    function SelectLimit($sql, $numrow, $offset = 0){
        $select = preg_replace('#^select#i', 'SELECT TOP '.($numrow + $offset), $sql);

        if(preg_match('#order by .*$#i', $sql, $match_order)){
            $order = $match_order[0];
        }else{
            $order = '';
        }

        $sql = 'SELECT * FROM ';
        $sql.= '(SELECT TOP '.$numrow.' * FROM ';
        $sql.= '('.$select.') ';

        if(preg_match('#desc$#i', $order)){
            $sql.= 'AS t1 '.preg_replace('#desc$#i', 'ASC', $order);
        }elseif(preg_match('#asc$#i', $order)){
            $sql.= 'AS t1 '.preg_replace('#asc$#i', 'DESC', $order);
        }else{
            $sql.= 'AS t1 '.$order.' DESC';
        }

        $sql.= ') AS t2 '.$order;

        return $this->Execute($sql);
    }

    function Insert_ID($table = null, $column = null){
        $sql = 'SELECT @@IDENTITY';
        return $this->getOne($sql);
    }

    function qstr($str){
        $str = '\''.str_replace('\'', '\'\'', $str).'\'';
        return str_replace('\"', '"', $str);
    }

    function Close(){
        if(!mssql_close($this->link)){
            exit($this->ErrorMsg());
        }
    }

    function ErrorMsg(){
        return mssql_get_last_message();
    }
}

class ADORecordSet_mssql extends ADORecordSet{
    var $result;

    function ADORecordSet_mssql(){
    }

    function FetchRow(){
        return mssql_fetch_assoc($this->result);
    }

    function RecordCount(){
        return mssql_num_rows($this->result);
    }

    function Close(){
        mssql_free_result($this->result);
    }
}

?>