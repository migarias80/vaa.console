<?php

namespace daoimpl;

use dao\IUsuarioDAO;
use model\Usuario;
use model\UsuarioUltimoAcceso;
use model\UsuarioAU;

class UsuarioSQLDAO implements IUsuarioDAO
{

    public function getUsuarioById($idUsuario)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SELECT 
                USR_ID ID,
                USR_NAME NAME,
                USR_PASSWORD PASSWORD,
                USR_ACTIVE ACTIVE,
                USR_COMPANY_ID COMPANY_ID,
                USR_FULL_NAME FULL_NAME,
                USR_PROFILE_ID ID_PROFILE,
                USR_EDITING_USER_ID LAST_EDIT_USER_ID
            FROM vaa2_users WHERE USR_ID = :id 
        ");
        $stmt->bindParam(":id", $idUsuario, \PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $usuario) {
                return new Usuario($usuario);
            }
        }

        return null;
    }

    public function getUsuarioByNombre($nombreUsuario)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SELECT
                USR_ID ID,
                USR_NAME NAME,
                USR_PASSWORD PASSWORD,
                USR_ACTIVE ACTIVE,
                USR_COMPANY_ID COMPANY_ID,
                USR_FULL_NAME FULL_NAME,
                USR_PROFILE_ID ID_PROFILE,
                USR_EDITING_USER_ID LAST_EDIT_USER_ID
            FROM vaa2_users 
            WHERE USR_NAME = :nombre_usuario 
        ");
        $stmt->bindParam(":nombre_usuario", $nombreUsuario, \PDO::PARAM_STR);
        $stmt->execute();

        $usuarios = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $usuario) {
                $usuarios[] = new Usuario($usuario);
            }
        }

        return $usuarios;
    }

    public function getUsuariosByIdEmpresa($idEmpresa)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SELECT 
                USR_ID ID,
                USR_NAME NAME,
                USR_PASSWORD PASSWORD,
                USR_ACTIVE ACTIVE,
                USR_COMPANY_ID COMPANY_ID,
                USR_FULL_NAME FULL_NAME,
                USR_PROFILE_ID ID_PROFILE,
                USR_EDITING_USER_ID LAST_EDIT_USER_ID
            FROM vaa2_users 
            WHERE USR_COMPANY_ID = :id_empresa 
        ");
        $stmt->bindParam(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->execute();

        $usuarios = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $usuarioResult) {
                array_push($usuarios, new Usuario($usuarioResult));
            }
        }

        return $usuarios;
    }

    public function nuevo($nombreUsuario, $idEmpresa, $fullName, $password, $idProfile, $lastEditUserId)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            INSERT INTO vaa2_users 
            (USR_NAME, USR_ACTIVE, USR_COMPANY_ID, USR_FULL_NAME, USR_PASSWORD, USR_PROFILE_ID, USR_EDITING_USER_ID) 
            VALUES 
            (:nombre_usuario, 1, :id_empresa, :full_name, :password, :id_profile, :last_edit_user_id)
        ");
        $stmt->bindParam(":nombre_usuario", $nombreUsuario, \PDO::PARAM_STR);
        $stmt->bindParam(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":full_name", $fullName, \PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, \PDO::PARAM_STR);
        $stmt->bindParam(":id_profile", $idProfile, \PDO::PARAM_STR);
        $stmt->bindParam(":last_edit_user_id", $lastEditUserId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function modificar($id, $idEmpresa, $nombreUsuario, $fullName, $idProfile, $lastEditUserId)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            UPDATE vaa2_users SET 
            USR_FULL_NAME = :full_name, 
            USR_PROFILE_ID = :id_profile, 
            USR_EDITING_USER_ID = :last_edit_user_id 
            WHERE USR_ID = :id 
            AND USR_COMPANY_ID = :id_empresa
        ");
        $stmt->bindParam(":full_name", $fullName, \PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);
        $stmt->bindParam(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":id_profile", $idProfile, \PDO::PARAM_INT);
        $stmt->bindParam(":last_edit_user_id", $lastEditUserId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function modificarMisDatos($id, $idEmpresa, $nombreUsuario, $fullName, $lastEditUserId)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            UPDATE vaa2_users SET 
            USR_FULL_NAME = :full_name, 
            USR_EDITING_USER_ID = :last_edit_user_id 
            WHERE USR_ID = :id 
            AND USR_COMPANY_ID = :id_empresa
        ");
        $stmt->bindParam(":full_name", $fullName, \PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);
        $stmt->bindParam(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":last_edit_user_id", $lastEditUserId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function deshabilitar($idEmpresa, $id, $lastEditUserId)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            UPDATE vaa2_users 
            SET USR_ACTIVE = 0,
            USR_EDITING_USER_ID = :last_edit_user_id
            WHERE USR_ID = :id 
            AND USR_COMPANY_ID = :id_empresa
        ");
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $stmt->bindValue(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":last_edit_user_id", $lastEditUserId, \PDO::PARAM_INT);

        $stmt->execute();
    }

    public function habilitar($idEmpresa, $id, $lastEditUserId)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            UPDATE vaa2_users 
            SET USR_ACTIVE = 1,
            USR_EDITING_USER_ID = :last_edit_user_id
            WHERE USR_ID = :id 
            AND USR_COMPANY_ID = :id_empresa
        ");
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $stmt->bindValue(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":last_edit_user_id", $lastEditUserId, \PDO::PARAM_INT);

        $stmt->execute();
    }

    public function eliminar($idEmpresa, $id, $lastEditUserId)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            DELETE VAA2_USER_ACCESSES_LOG 
            WHERE UAL_USR_ID = :id
        ");
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            DELETE VAA2_USER_CHANGES_LOG 
            WHERE UCL_USR_ID = :id
        ");
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            DELETE vaa2_users 
            WHERE USR_ID = :id 
            AND USR_COMPANY_ID = :id_empresa
        ");
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $stmt->bindValue(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function updateLastAccess($idUsuario, $idEmpresa, $ipAddress)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            INSERT INTO VAA2_USER_ACCESSES_LOG 
            (UAL_USR_ID, UAL_COMPANY_ID_ACCESSED, UAL_LAST_ACCESS, UAL_IP_ADDRESS) 
            Values 
            (:user_id, :COMPANY_ID, getdate(), :ip_address)
        ");
        $stmt->bindParam(":user_id", $idUsuario, \PDO::PARAM_INT);
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":ip_address", $ipAddress, \PDO::PARAM_STR);

        $stmt->execute();
    }

    public function getLastAccess($idUsuario, $idEmpresa)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SELECT TOP 2 
              UAL_USR_ID USER_ID, 
              UAL_COMPANY_ID_ACCESSED COMPANY_ID, 
                CONVERT(VARCHAR(10), UAL_LAST_ACCESS, 103) + 
                ' '+
                RIGHT(CONVERT(VARCHAR(32), UAL_LAST_ACCESS, 108), 8) 
              AS LAST_ACCESS, 
              UAL_IP_ADDRESS IP_ADDRESS,
              UAL_LAST_ACCESS LAST_ACCESS_VALUE
            FROM VAA2_USER_ACCESSES_LOG 
            WHERE UAL_COMPANY_ID_ACCESSED = :id_empresa 
            AND UAL_USR_ID = :user_id 
            ORDER BY UAL_LAST_ACCESS DESC
        ");
        $stmt->bindParam(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $idUsuario, \PDO::PARAM_INT);
        $stmt->execute();

        $usuarioUltimoAcceso = null;
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $ultimoAccesoResult) {
                $usuarioUltimoAcceso = new UsuarioUltimoAcceso($ultimoAccesoResult);
            }
        }

        return $usuarioUltimoAcceso;
    }

    public function setPassword($idUsuario, $password, $id_empresa, $old_password, $lastEditUserId)
    {
        $db = \db\SQLConnectPDO::GetConnection();

        if ($old_password == null) {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                UPDATE vaa2_users SET 
                USR_PASSWORD = :password,
                USR_EDITING_USER_ID = :last_edit_user_id
                WHERE USR_ID = :id 
                AND USR_COMPANY_ID = :id_empresa 
            ");
        } else {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                UPDATE vaa2_users SET 
                USR_PASSWORD = :password,
                USR_EDITING_USER_ID = :last_edit_user_id
                WHERE USR_ID = :id 
                AND USR_COMPANY_ID = :id_empresa
                AND USR_PASSWORD = :old_password 
            ");
            $stmt->bindValue(":old_password", $old_password, \PDO::PARAM_STR);
        }

        $stmt->bindValue(":id", $idUsuario, \PDO::PARAM_INT);
        $stmt->bindValue(":password", $password, \PDO::PARAM_STR);
        $stmt->bindValue(":id_empresa", $id_empresa, \PDO::PARAM_INT);
        $stmt->bindParam(":last_edit_user_id", $lastEditUserId, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getHistorial($idUsuario, $idEmpresa)
    {
        $db = \db\SQLConnectPDO::GetConnection();

        // Filtros
        $filtroEmpresa = "";
        $filtroEmpresa = " AND vaa2_users.USR_COMPANY_ID = :id_empresa ";

        // Query
        $sql = "
            SELECT 
                VAA2_USER_CHANGES_LOG.UCL_USR_ID ID, 
                VAA2_USER_CHANGES_LOG.UCL_NAME NAME, 
                VAA2_USER_CHANGES_LOG.UCL_FULL_NAME FULL_NAME, 
                VAA2_USER_CHANGES_LOG.UCL_PROFILE_ID ID_PROFILE, 
                VAA2_USER_CHANGES_LOG.UCL_EDITING_USER_ID LAST_EDIT_USER_ID, 
                VAA2_USER_CHANGES_LOG.UCL_LAST_EDIT LAST_EDIT,
                vaa2_users.USR_NAME AS LAST_EDIT_USER_NAME,
                VAA2_USER_CHANGES_LOG.UCL_CHANGE_PASSWORD AS CHANGE_PASSWORD,
                VAA2_USER_CHANGES_LOG.UCL_ACTIVE AS ACTIVE
            FROM VAA2_USER_CHANGES_LOG 
            INNER JOIN vaa2_users ON vaa2_users.USR_ID = VAA2_USER_CHANGES_LOG.UCL_EDITING_USER_ID
            WHERE VAA2_USER_CHANGES_LOG.UCL_USR_ID = :id
            $filtroEmpresa
            ORDER BY VAA2_USER_CHANGES_LOG.UCL_LAST_EDIT DESC
        ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":id_empresa", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":id", $idUsuario, \PDO::PARAM_INT);
        $stmt->execute();

        $usuariosAU = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $usuarioResult) {
                array_push($usuariosAU, new UsuarioAU($usuarioResult));
            }
        }

        return $usuariosAU;
    }

    public function getAllAccess($idUsuario, $idEmpresa)
    {
        $db = \db\SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SELECT 
              UAL_USR_ID USER_ID, 
              CONVERT(VARCHAR(10), UAL_LAST_ACCESS, 103) + 
              ' '+
              RIGHT(CONVERT(VARCHAR(32), UAL_LAST_ACCESS, 108), 8) 
              AS LAST_ACCESS, 
              CASE WHEN UAL_COMPANY_ID_ACCESSED = 1 
              THEN 'Administración General' 
              ELSE VAA2_COMPANIES.COM_NAME
              END BUSINESS_NAME,
              UAL_IP_ADDRESS IP_ADDRESS 
            FROM VAA2_USER_ACCESSES_LOG  
            INNER JOIN VAA2_COMPANIES ON VAA2_COMPANIES.COM_ID = VAA2_USER_ACCESSES_LOG.UAL_COMPANY_ID_ACCESSED
            WHERE 1 = 1
            AND UAL_USR_ID = :user_id 
            ORDER BY UAL_LAST_ACCESS DESC
        ");
        $stmt->bindParam(":user_id", $idUsuario, \PDO::PARAM_INT);
        $stmt->execute();

        $usuarioUltimosAccesos = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $ultimoAccesoResult) {
                array_push($usuarioUltimosAccesos, new UsuarioUltimoAcceso($ultimoAccesoResult));
            }
        }

        return $usuarioUltimosAccesos;
    }
}