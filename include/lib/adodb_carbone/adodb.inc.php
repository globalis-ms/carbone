<?php

function ADONewConnection($type) {
    require_once 'drivers/'.$type.'.php';

    $classe = 'ADODB_'.$type;

    if(!class_exists($classe)){
        exit('Driver de base de données introuvable');
    }else{
        return new $classe();
    }
}

class ADOConnection {
    function GetOne($sql) {
        $rs = $this->Execute($sql);
        if($row = $rs->FetchRow()){
            list(, $tmp) = each($row);
            return $tmp;
        }else{
            return FALSE;
        }
    }

    function GetRow($sql) {
        $rs = $this->Execute($sql);
        if($row = $rs->FetchRow()){
            return $row;
        }else{
            return FALSE;
        }
    }

    function LogSQL($sql) {
        global $session;
        if(CFG_DEBUG) {
            if(defined('CFG_PATH_FILE_LOG_SQL')) {
                if(!file_exists(CFG_PATH_FILE_LOG_SQL))
                    @touch(CFG_PATH_FILE_LOG_SQL);

                if(is_writable(CFG_PATH_FILE_LOG_SQL)){
                    if(!preg_match('#(INSERT INTO|UPDATE|FROM) '.CFG_TABLE_SESSION.'#i', $sql)){
                        //$data = '['.date('d-m-Y H:i:s').'@'.@$session->session_id.'] '.$sql."\n";
                        $data = '['.date('d-m-Y H:i:s').'@'.@$session->session_id.'] '.str_replace( PHP_EOL, '', $sql)."\n";
                        $fp = fopen(CFG_PATH_FILE_LOG_SQL, 'a');
                        fwrite($fp, $data);
                        fclose($fp);
                    }
                }
            }
        }
    }
}

class ADORecordSet{
}

?>