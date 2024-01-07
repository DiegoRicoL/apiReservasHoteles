<?php
include_once("conexion.php");
$conex = conectarBd();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getOpinionesByHabitacion($conex);
        break;
    case 'PUT':
        updateOpiniones($conex);
        break;
    case 'POST':
        insertOpiniones($conex);
        break;
    case 'DELETE':
        deleteOpiniones($conex);
        break;
}

function getOpinionesByHabitacion($conex)
{
    global $conex;
    $sql = 'SELECT * from opinion WHERE Habitacion = ?';
    $result = $conex->prepare($sql);
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['opinion']['habitacion'])) {
        echo json_encode(array('message' => 'No se ha proporcionado una habitacion'));
        return;
    }
    $habitacion = $input['opinion']['habitacion'];
    $result->bindParam(1, $habitacion);
    $result->execute();
    $array = array();
    $arrayPrin = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $array = $row;
        array_push($arrayPrin, $array);
    }
    echo json_encode($arrayPrin);
    return;
}

function updateOpiniones($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['opinion']['id']) || !isset($input['opinion']['nota']) || !isset($input['opinion']['texto'])) {
        echo json_encode(array('message' => 'No se ha proporcionado un id, nota o texto'));
        return;
    }
    $id = $input['opinion']['id'];
    $nota = $input['opinion']['nota'];
    $texto = $input['opinion']['texto'];

    if ($nota >= 0 && $nota <= 10) {
        $insertOpinion = $conex->prepare("UPDATE opinion SET Nota = ?, Texto = ? WHERE id = ?");
        $insertOpinion->bindParam(1, $nota);
        $insertOpinion->bindParam(2, $texto);
        $insertOpinion->bindParam(3, $id);
        $conex->beginTransaction();
        if ($insertOpinion->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Opinion actualizada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al actualizar opinion'));
        }
    }
}

function insertOpiniones($conex)
{
    global $conex;
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['opinion']['cliente']) || !isset($input['opinion']['habitacion']) || !isset($input['opinion']['nota']) || !isset($input['opinion']['texto'])) {
        echo json_encode(array('message' => 'No se ha proporcionado un cliente, habitacion, nota o texto'));
        return;
    }
    $cliente = $input['opinion']['cliente'];
    $habitacion = $input['opinion']['habitacion'];
    $nota = $input['opinion']['nota'];
    $texto = $input['opinion']['texto'];

    //comprueba que el cliente ha estado en esa habitacion
    $sql = 'SELECT * from reservas WHERE Cliente = ? AND Habitacion = ?';
    $result = $conex->prepare($sql);
    $result->bindParam(1, $cliente);
    $result->bindParam(2, $habitacion);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if(empty($row)){
        echo json_encode(array('message' => 'El cliente no ha estado en esa habitacion'));
        return;
    }

    if ($nota >= 0 && $nota <= 10) {
        $insertOpinion = $conex->prepare("INSERT INTO opinion (Cliente, Habitacion, Nota, Texto) VALUES (?, ?, ?, ?)");
        $insertOpinion->bindParam(1, $cliente);
        $insertOpinion->bindParam(2, $habitacion);
        $insertOpinion->bindParam(3, $nota);
        $insertOpinion->bindParam(4, $texto);
        $conex->beginTransaction();
        if ($insertOpinion->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Opinion insertada'));
            return;
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al insertar opinion'));
            return;
        }
    }
}

function deleteOpiniones($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['opinion']['id'])) {
        echo json_encode(array('message' => 'No se ha proporcionado un id'));
        return;
    }
    $id = $input['opinion']['id'];
    $insertOpinion = $conex->prepare("DELETE FROM opinion WHERE id = ?");
    $insertOpinion->bindParam(1, $id);
    $conex->beginTransaction();
    if ($insertOpinion->execute() != 0) {
        $conex->commit();
        echo json_encode(array('message' => 'Opinion eliminada'));
        return;
    } else {
        $conex->rollback();
        echo json_encode(array('message' => 'Error al eliminar opinion'));
        return;
    }

}
