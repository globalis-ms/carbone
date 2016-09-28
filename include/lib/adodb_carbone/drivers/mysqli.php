<?php

class ADODB_mysqli extends ADOConnection{
    var $link;
    var $last_insert_id;

    function __construct(){
    }

    function Connect($hostname, $username, $password, $database_name){
        if(!($this->link = @mysqli_connect($hostname, $username, $password, $database_name))){
            if(CFG_DEBUG)
                echo($this->ErrorMsg().' [Could\'t connect]');
            return FALSE;
        }
        return TRUE;
    }
    
    function Execute($sql){
        $rs = new ADORecordSet_mysqli();

        $rs->result = mysqli_query($this->link, $sql);

        if(!$rs->result){
            if(CFG_DEBUG)
                echo($this->ErrorMsg().' [SQL Query] '.$sql);
            else
                echo($this->ErrorMsg().' [SQL Query] ');
        }

        if(CFG_DEBUG) {
            // On sauvegarde le last_insert_id sinon ce sera toujours 0 du fait de la requête de profiling...
            $this->last_insert_id = mysqli_insert_id($this->link);
            //echo microtime().'->'.$this->last_insert_id."<br/>";
            $rs->time = mysqli_query($this->link, "SELECT query_id, SUM(duration) FROM information_schema.profiling GROUP BY query_id ORDER BY query_id DESC LIMIT 1");
            $rs->time = mysqli_fetch_array($rs->time);
            $sql = sprintf("%.3f", $rs->time[1]).' sec -> '.$sql;
        }
        
        parent::LogSQL($sql);

        return $rs;
    }

    function SelectLimit($sql, $numrow, $offset = 0){
        $sql.= ' LIMIT '.$offset.','.$numrow;
        return $this->Execute($sql);
    }

    function Insert_ID($table = null, $column = null){
        if(CFG_DEBUG)
            return $this->last_insert_id;
        else
            return mysqli_insert_id($this->link);
    }

    function qstr($str){
        $str = '\''.mysqli_real_escape_string($this->link, $str).'\'';
        return str_replace('\"', '"', $str);
    }

    function Close(){
        if(!mysqli_close($this->link)){
            exit($this->ErrorMsg());
        }
        parent::LogSQL('---');
    }

    function ErrorMsg(){
        if (!empty($this->link))
            return '['.mysqli_errno($this->link).'] '.mysqli_error($this->link);
    }
}

class ADORecordSet_mysqli extends ADORecordSet{
    var $result;

    function __construct(){
    }

    function FetchRow(){
        return mysqli_fetch_assoc($this->result);
    }

    function RecordCount(){
        return mysqli_num_rows($this->result);
    }

    function Close(){
        mysqli_free_result($this->result);
    }
}

?>