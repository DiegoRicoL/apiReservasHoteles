<?php
include_once("conexion.php");

$conex = conectarBd();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "PUT":
        updateCliente($conex);
        break;
    case "POST":
        // insertCliente($conex); Este metodo no se accede desde aquí
        break;
    case "DELETE";
        // deleteCliente($conex); Este metodo no se accede desde aquí
        break;
    case "GET":
        getClientes($conex);
        break;
    default:
        break;
}

function getClientes($conex)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input)) {
        echo json_encode(array("error" => "Error al obtener los datos de los clientes: No se ha proporcionado un usuario"));
        return;
    } else {
        $usuario = $input["usuario"];

        if (($adminStatus = getAdminStatusUser($conex, $usuario)) == false) {
            return json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
        } else {
            if ($adminStatus["admin"] == 1) {
                $sql = "SELECT * FROM clientes;";

                $result = $conex->query($sql);

                $clientes = array();
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $clientes[] = $row;
                }
                echo json_encode($clientes);
                return;
            } else {
                echo json_encode(array("error" => "El usuario no es administrador"));
                return;
            }
        }
    }

}


function insertCliente($conex, $cliente)
{
    if (empty($cliente)) {
        echo json_encode(array("error" => "Error al insertar el cliente: No se ha proporcionado un cliente"));
        return;
    } else {
        if (empty($cliente["nombre"]) || empty($cliente["apellidos"]) || empty($cliente["telefono"]) || empty($cliente["email"])) {
            echo json_encode(array("error" => "Error al insertar el cliente: No se han proporcionado todos los datos"));
            return;
        }
    }

    $result = $conex->prepare("INSERT INTO clientes (nombre, apellidos, numtlf, mail) VALUES (?,?,?,?);");
    $result->bindParam(1, $cliente["nombre"]);
    $result->bindParam(2, $cliente["apellidos"]);
    $result->bindParam(3, $cliente["telefono"]);
    $result->bindParam(4, $cliente["email"]);

    $conex->beginTransaction();
    if ($result->execute() != 0) {
        $conex->commit();

        $result = $conex->prepare("SELECT * FROM clientes WHERE nombre = ? AND apellidos = ? AND numtlf = ? AND mail = ?;");
        $result->bindParam(1, $cliente["nombre"]);
        $result->bindParam(2, $cliente["apellidos"]);
        $result->bindParam(3, $cliente["telefono"]);
        $result->bindParam(4, $cliente["email"]);

        $result->execute();
        $cliente = $result->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array("success" => "Cliente insertado correctamente", "cliente" => $cliente["id"]));
        return;

    } else {
        $conex->rollBack();

        echo json_encode(array("error" => "Error al insertar el cliente"));
        return;
    }
}

function updateCliente($conex)
{
    $input = json_decode(file_get_contents('php://input'), true);

    $usuario = $input['usuario'];
    $cliente = $input["cliente"];

    if (($adminStatus = getAdminStatusUser($conex, $usuario)) == false) {
        echo json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
        return;
    } else {
        if ($adminStatus["admin"] == 1) {
            if (empty($cliente["id"]) || empty($cliente["nombre"]) || empty($cliente["apellidos"]) || empty($cliente["telefono"]) || empty($cliente["email"])) {
                echo json_encode(array("error" => "Error al actualizar el cliente: No se han proporcionado todos los datos"));
                return;
            } else {
                $result = $conex->prepare("UPDATE clientes SET nombre = ?, apellidos = ?, numtlf = ?, mail = ? WHERE id = ?;");
                $result->bindParam(1, $cliente["nombre"]);
                $result->bindParam(2, $cliente["apellidos"]);
                $result->bindParam(3, $cliente["telefono"]);
                $result->bindParam(4, $cliente["email"]);
                $result->bindParam(5, $cliente["id"]);

                $conex->beginTransaction();
                if ($result->execute() != 0) {
                    $conex->commit();
                    echo json_encode(array("success" => "Cliente actualizado correctamente"));
                    return;
                } else {
                    $conex->rollBack();
                    echo json_encode(array("error" => "Error al actualizar el cliente"));
                    return;
                }
            }
        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
            return;
        }
    }
}

function deleteCliente($conex, $id)
{
    if (empty($id)) {
        echo json_encode(array("error" => "Error al eliminar el cliente: No se ha proporcionado el id"));
        return;
    } else {
        $result = $conex->prepare("SELECT id FROM usuarios WHERE cliente = ?;");
        $result->bindParam(1, $id);
        $result->execute();

        $idUsuario = $result->fetch(PDO::FETCH_ASSOC);
        if (empty($idUsuario)) {
            $idUsuario = null;
        } else {
            $idUsuario = $idUsuario["id"];
        }

        $result = $conex->prepare("DELETE FROM clientes WHERE id = ?;");
        $result->bindParam(1, $id);

        $conex->beginTransaction();
        if ($result->execute() != 0) {
            $conex->commit();
            if ($idUsuario != null) {
                require_once("apiUsuarios.php");
                deleteUsuarios($conex, $idUsuario);
            }
            echo json_encode(array("success" => "Cliente eliminado correctamente"));
            return;
        } else {
            $conex->rollBack();
            echo json_encode(array("error" => "Error al eliminar el cliente"));
            return;
        }

    }
}



?>