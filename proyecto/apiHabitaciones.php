<?php
include("conexion.php");
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
        $array["hotel"] = $row["Hotel"];
        $array["ocupado"] = $row["Ocupado"];
        $array["tipo"] = $row["Tipo"];
        $array["camas"] = $row["Camas"];
        $arrayPrin[$row["id"]] = $array;
    }
    echo json_encode($arrayPrin);
}

function updateHabitaciones($conex)
{
    global $conex;
    global $arrayTipoHabitaciones;
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $ocupado = $input['ocupado'];
    $tipo = $input['tipo'];
    $camas = $input['camas'];

    for ($i = 0; $i < count($arrayTipoHabitaciones); $i++) {
        if ($tipo == $arrayTipoHabitaciones[$i]) {
            $ok = true;
        }
    }
    if ($ok) {
        $insertHabita = $conex->prepare("UPDATE habitaciones SET Ocupado = ?, Tipo = ?, Camas = ? WHERE id = ?");
        $insertHabita->bindParam(1, $ocupado);
        $insertHabita->bindParam(2, $tipo);
        $insertHabita->bindParam(3, $camas);
        $insertHabita->bindParam(4, $id);
        $conex->beginTransaction();
        if($insertHabita->execute() != 0){
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion actualizada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al actualizar habitacion'));
        }
    } else {
        echo json_encode(array('message' => 'Error al actualizar habitacion'));
    }
}

function insertHabitaciones($conex)
{
    global $conex;
    global $arrayTipoHabitaciones;
    $input = json_decode(file_get_contents('php://input'), true);
    $hotel = $input['hotel'];
    $ocupado = $input['ocupado'];
    $tipo = $input['tipo'];
    $camas = $input['camas'];
    $sql = "";


    for ($i = 0; $i < count($arrayTipoHabitaciones); $i++) {
        if ($tipo == $arrayTipoHabitaciones[$i]) {
            $ok = true;
        }
    }

    if ($ok) {
       
        $insertHabita = $conex->prepare("INSERT INTO habitaciones (Hotel, Ocupado, Tipo, Camas) VALUES (?, ?, ?, ?)");
        $insertHabita->bindParam(1, $hotel);
        $insertHabita->bindParam(2, $ocupado);
        $insertHabita->bindParam(3, $tipo);
        $insertHabita->bindParam(4, $camas);
        $conex->beginTransaction();
        if($insertHabita->execute() != 0){
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion insertada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al insertar habitacion'));
        }
    }else{
        echo json_encode(array('message' => 'Error al insertar habitacion'));
    }
}

function deleteHabitaciones($conex)
{
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $ok = true;
    global $conex;
    if ($ok) {
       
        $insertHabita = $conex->prepare("DELETE FROM habitaciones WHERE id = ?");
        $insertHabita->bindParam(1, $id);
        $conex->beginTransaction();
        if($insertHabita->execute() != 0){
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion eliminada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al eliminar habitacion'));
        }
    }else{
        echo json_encode(array('message' => 'Error al eliminar habitacion'));
    }
}
