<?php

namespace daoimpl;

use dao\IFuncionDAO;
use \dao\IUsuarioDAO;
use \db\MySQLConnectPDO;
use db\SQLConnectPDO;
use \model\Funcion;

class FuncionSQLDAO implements IFuncionDAO
{

    public function getFuncionesByIdUsuario($idUsuario)
    {
        $db = SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SELECT 
                vaa2_user_profiles_functions.UPF_USER_PROFILE_ID ID, 
                vaa2_user_functions.UFU_USER_FUNCTION_NAME NAME 
            FROM vaa2_user_profiles_functions 
            INNER JOIN vaa2_user_functions ON vaa2_user_functions.UFU_ID = vaa2_user_profiles_functions.UPF_USER_FUNCTION_ID 
            INNER JOIN vaa2_users ON vaa2_users.USR_PROFILE_ID = vaa2_user_profiles_functions.UPF_USER_PROFILE_ID 
            WHERE vaa2_users.USR_ID = :id_usuario 
        ");
        $stmt->bindParam(":id_usuario", $idUsuario, \PDO::PARAM_INT);
        $stmt->execute();

        $funciones = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $funcionResult) {
                $funciones[] = new Funcion($funcionResult);
            }
        }

        return $funciones;
    }
}