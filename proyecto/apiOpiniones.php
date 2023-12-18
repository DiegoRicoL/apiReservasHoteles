<?php
include("conexion.php");
$conex = conectarBd();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getOpiniones($conex);
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

function getOpiniones($conex)
{
    $sql = 'SELECT * from opinion';
    global $conex;
    $result = $conex->query($sql);
    $arrayPrin = array();
    $array = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $array["cliente"] = $row["Cliente"];
        $array["habitacion"] = $row["Habitacion"];
        $array["nota"] = $row["Nota"];
        $array["texto"] = $row["Texto"];
        $arrayPrin[$row["id"]] = $array;
    }
    echo json_encode($arrayPrin);
}

function updateOpiniones($conex)
{
    global $conex;
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $nota = $input['nota'];
    $texto = $input['texto'];

    if ($nota >= 0 && $nota <= 10) {
        $ok = true;
    }
    if ($ok) {
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
    } else {
        echo json_encode(array('message' => 'Error al actualizar opinion'));
    }
}

function insertOpiniones($conex)
{
    global $conex;
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);
    $cliente = $input['cliente'];
    $habitacion = $input['habitacion'];
    $nota = $input['nota'];
    $texto = $input['texto'];

    if ($nota >= 0 && $nota <= 10) {
        $ok = true;
    }
        if ($ok) {
            $insertOpinion = $conex->prepare("INSERT INTO opinion (Cliente, Habitacion, Nota, Texto) VALUES (?, ?, ?, ?)");
            $insertOpinion->bindParam(1, $cliente);
            $insertOpinion->bindParam(2, $habitacion);
            $insertOpinion->bindParam(3, $nota);
            $insertOpinion->bindParam(4, $texto);
            $conex->beginTransaction();
            if ($insertOpinion->execute() != 0) {
                $conex->commit();
                echo json_encode(array('message' => 'Opinion insertada'));
            } else {
                $conex->rollback();
                echo json_encode(array('message' => 'Error al insertar opinion'));
            }
        } else {
            echo json_encode(array('message' => 'Error al insertar opinion'));
        }
}

function deleteOpiniones($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $ok = true;
    if ($ok) {
        $insertOpinion = $conex->prepare("DELETE FROM opinion WHERE id = ?");
        $insertOpinion->bindParam(1, $id);
        $conex->beginTransaction();
        if ($insertOpinion->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Opinion eliminada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al eliminar opinion'));
        }
    } else {
        echo json_encode(array('message' => 'Error al eliminar opinion'));
    }
}
