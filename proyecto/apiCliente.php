<?php
include_once("conexion.php");

$conex = conectarBd();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "PUT":
        echo "Estoy haciendo UPDATE";
        break;
    case "POST":
        insertCliente($conex);
        break;
    case "DELETE";
        echo "Estoy haciendo DELETE";
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

    $usuario = $input['usuario'];

    if (($adminStatus = getAdminStatusUser($conex, $usuario)) == false) {
        echo json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
        return;
    } else {
        if ($adminStatus["admin"] == 1) {
            $sql = "SELECT * FROM clientes;";

            $result = $conex->query($sql);

            $clientes = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $clientes[] = $row;
            }
            echo json_encode($clientes);
        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
        }
    }

}

function insertCliente($conex, $cliente = array())
{
    if (empty($cliente)) {
        $input = json_decode(file_get_contents('php://input'), true);
        $cliente = $input["cliente"];
    }

    $result = $conex->prepare("INSERT INTO clientes (nombre, apellidos, telefono, email) VALUES (?,?,?,?);");
    $result->bindParam(1, $cliente["nombre"]);
    $result->bindParam(2, $cliente["apellidos"]);
    $result->bindParam(3, $cliente["telefono"]);
    $result->bindParam(4, $cliente["email"]);

    $conex->beginTransaction();
    if ($result->execute() != 0) {
        $conex->commit();
        
        $result = $conex->prepare("SELECT * FROM clientes WHERE nombre = ? AND apellidos = ? AND telefono = ? AND email = ?;");
        $result->bindParam(1, $cliente["nombre"]);
        $result->bindParam(2, $cliente["apellidos"]);
        $result->bindParam(3, $cliente["telefono"]);
        $result->bindParam(4, $cliente["email"]);

        $result->execute();
        $cliente = $result->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array("success" => "Cliente insertado correctamente", "cliente" => $cliente));
    } else {
        $conex->rollBack();
        echo json_encode(array("error" => "Error al insertar el hotel"));
    }

    return json_encode($result);
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
            $sql = "UPDATE clientes SET nombre = ?, apellidos = ?, telefono = ?, email = ? WHERE id = ?;";
            $result = $conex->query($sql);
            $result->execute([$cliente["nombre"], $cliente["apellidos"], $cliente["telefono"], $cliente["email"], $cliente["id"]]);
            echo json_encode($result);
        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
        }
    }
}

function deleteCliente($conex)
{
    $input = json_decode(file_get_contents('php://input'), true);

    $usuario = $input['usuario'];
    $cliente = $input["cliente"];

    if (($adminStatus = getAdminStatusUser($conex, $usuario)) == false) {
        echo json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
        return;
    } else {
        if ($adminStatus["admin"] == 1) {
            $sql = "DELETE FROM clientes WHERE id = ?;";
            $result = $conex->query($sql);
            $result->execute([$cliente["id"]]);
            echo json_encode($result);
        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
        }
    }
}



?>