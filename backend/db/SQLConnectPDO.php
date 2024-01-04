<?php

namespace db;

use \utils\LogUtils;
use PDO;

class SQLConnectPDO {

    public static function GetConnection() {
        $iniFile = CONF_DB_FILE;

        $data = parse_ini_file($iniFile, true);

        $db_host = $data['DBConfig']['db_host'];
        $dbuser = $data['DBConfig']['dbuser'];
        $dbpassword = $data['DBConfig']['dbpassword'];
        $dbname = $data['DBConfig']['dbname'];
		$dbtimeout = $data['DBConfig']['dbtimeout'];

        $db = new \PDO(
            "sqlsrv:Server=" . $db_host .
            ";Database=" . $dbname .
            ";ConnectionPooling=0" .
			";LoginTimeout=" . $dbtimeout,
            $dbuser, $dbpassword
        );

        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $db;
    }

}
