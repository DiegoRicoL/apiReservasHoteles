<?php
include_once("conexion.php");
$arrayTipoHabitaciones = array('Individual', 'Doble', 'Triple', 'Quad', 'Queen', 'King', 'Duplex', 'Doble-doble', 'Estudio', 'Suite');
$conex = conectarBd();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getHabitaciones($conex);
        break;
    case 'PUT':
        updateHabitaciones($conex);
        break;
    case 'POST':
        insertHabitaciones($conex);
        break;
    case 'DELETE':
        deleteHabitaciones($conex);
        break;
}


function getHabitaciones($conex)
{
    $sql = 'SELECT * from habitaciones';
    global $conex;
    $result = $conex->query($sql);
    $arrayPrin = array();
    $array = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $array = $row;
        array_push($arrayPrin, $array);
    }
    echo json_encode($arrayPrin);
    return;
}

function updateHabitaciones($conex)
{
    global $conex;
    global $arrayTipoHabitaciones;
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['usuario']['id']) || !isset($input['habitacion']['id']) || !isset($input['habitacion']['tipo']) || !isset($input['habitacion']['camas'])) {
        echo json_encode(array('message' => 'Error al insertar habitacion'));
        return;
    }

    $idUsuario = $input['usuario']['id'];
    $id = $input['habitacion']['id'];
    $tipo = $input['habitacion']['tipo'];
    $camas = $input['habitacion']['camas'];
    $sql = "";

    for ($i = 0; $i < count($arrayTipoHabitaciones); $i++) {
        if ($tipo == $arrayTipoHabitaciones[$i]) {
            $ok = true;
        }
    }
    $sql = "SELECT * from usuarios where id = ?";
    $result = $conex->prepare($sql);
    $result->bindParam(1, $idUsuario);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row["Admin"] == "1") {
        $ok = true;
    } else {
        $ok = false;
        echo json_encode(array('message' => 'No tienes permisos para actualizar habitaciones'));
        return;
    }
    if ($ok) {
        $insertHabita = $conex->prepare("UPDATE habitaciones SET Tipo = ?, Camas = ? WHERE id = ?");
        $insertHabita->bindParam(1, $tipo);
        $insertHabita->bindParam(2, $camas);
        $insertHabita->bindParam(3, $id);
        $conex->beginTransaction();
        if ($insertHabita->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion actualizada'));
            return;
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al actualizar habitacion'));
            return;
        }
    } else {
        echo json_encode(array('message' => 'Error al actualizar habitacion'));
        return;
    }
}

function insertHabitaciones($conex)
{
    global $conex;
    global $arrayTipoHabitaciones;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['usuario']['id']) || !isset($input['habitacion']['hotel']) || !isset($input['habitacion']['tipo']) || !isset($input['habitacion']['camas'])) {
        echo json_encode(array('message' => 'Error al insertar habitacion'));
        return;
    }

    $idUsuario = $input['usuario']['id'];
    $hotel = $input['habitacion']['hotel'];
    $tipo = $input['habitacion']['tipo'];
    $camas = $input['habitacion']['camas'];
    $sql = "";


    for ($i = 0; $i < count($arrayTipoHabitaciones); $i++) {
        if ($tipo == $arrayTipoHabitaciones[$i]) {
            $ok = true;
        }
    }

    $sql = "SELECT * from usuarios where id = ?";
    $result = $conex->prepare($sql);
    $result->bindParam(1, $idUsuario);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row["Admin"] == "1") {
        $ok = true;
    } else {
        $ok = false;
        echo json_encode(array('message' => 'No tienes permisos para actualizar habitaciones'));
        return;
    }

    if ($ok) {
        $insertHabita = $conex->prepare("INSERT INTO habitaciones (Hotel, Tipo, Camas) VALUES (?, ?, ?)");
        $insertHabita->bindParam(1, $hotel);
        $insertHabita->bindParam(2, $tipo);
        $insertHabita->bindParam(3, $camas);
        $conex->beginTransaction();
        if ($insertHabita->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion insertada'));
            return;
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al insertar habitacion'));
            return;
        }
    } else {
        echo json_encode(array('message' => 'Error al insertar habitacion'));
        return;
    }
}

function deleteHabitaciones($conex)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['usuario']['id']) || !isset($input['habitacion']['id'])) {
        echo json_encode(array('message' => 'Error al eliminar habitacion'));
        return;
    }

    $idUsuario = $input['usuario']['id'];
    $id = $input['habitacion']['id'];
    global $conex;

    $sql = "SELECT * from usuarios where id = ?";
    $result = $conex->prepare($sql);
    $result->bindParam(1, $idUsuario);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row["Admin"] == "1") {
        $insertHabita = $conex->prepare("DELETE FROM habitaciones WHERE id = ?");
        $insertHabita->bindParam(1, $id);
        $conex->beginTransaction();
        if ($insertHabita->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion eliminada'));
            return;
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al eliminar habitacion'));
            return;
        }
    } else {
        echo json_encode(array('message' => 'No tienes permisos para actualizar habitaciones'));
        return;
    }

}
