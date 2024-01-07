<?php
include_once("conexion.php");
$conex = conectarBd();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getReservas($conex);
        break;
    case 'PUT':
        updateReservas($conex);
        break;
    case 'POST':
        insertReservas($conex);
        break;
    case 'DELETE':
        deleteReservas($conex);
        break;
}

function getReservas($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['usuario']['id'])) {
        $usuario = $input['usuario'];
    } else {
        echo json_encode(array('message' => 'No se ha proporcionado un usuario'));
        return;
    }


    if (($adminStatus = getAdminStatusUser($conex, $usuario)) == false) {
        return json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
    } else {
        if ($adminStatus["admin"] == 1) {
            $sql = 'SELECT * from reservas';
            global $conex;
            $result = $conex->query($sql);
            $array = array();
            $arrayPrin = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $array = $row;
                array_push($arrayPrin, $array);
            }
            echo json_encode($arrayPrin);
            return;
        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
            return;
        }
    }

}

function updateReservas($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['reserva']['id']) || !isset($input['reserva']['fDesde']) || !isset($input['reserva']['fHasta'])) {
        echo json_encode(array('message' => 'No se ha proporcionado todos los datos necesarios'));
        return;
    }

    $id = $input['reserva']['id'];
    $fDesde = $input['reserva']['fDesde'];
    $fHasta = $input['reserva']['fHasta'];

    $sqlCheckeaFecha = "SELECT * from reservas where ? <= fHasta AND fDesde <= ?";
    $result = $conex->prepare($sqlCheckeaFecha);
    $result->bindParam(1, $fDesde);
    $result->bindParam(2, $fHasta);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if (empty($row)) {
        $insertReserva = $conex->prepare("UPDATE reservas SET FDesde = ?, FHasta = ? WHERE id = ?");
        $insertReserva->bindParam(1, $fDesde);
        $insertReserva->bindParam(2, $fHasta);
        $insertReserva->bindParam(3, $id);
        $conex->beginTransaction();
        if ($insertReserva->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Reserva actualizada'));
            return;
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al actualizar reserva'));
            return;
        }
    } else {
        echo json_encode(array('message' => 'La fecha de reserva no esta disponible'));
        return;
    }
}

function insertReservas($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);
    if(!isset($input['reserva']['cliente']) || !isset($input['reserva']['habitacion']) || !isset($input['reserva']['fDesde']) || !isset($input['reserva']['fHasta'])){
        echo json_encode(array('message' => 'No se ha proporcionado todos los datos necesarios'));
        return;
    }

    $cliente = $input['reserva']['cliente'];
    $habitacion = $input['reserva']['habitacion'];
    $fDesde = $input['reserva']['fDesde'];
    $fHasta = $input['reserva']['fHasta'];


    $sqlCheckeaFecha = "SELECT * from reservas where ? <= fHasta AND fDesde <= ?";
    $result = $conex->prepare($sqlCheckeaFecha);
    $result->bindParam(1, $fDesde);
    $result->bindParam(2, $fHasta);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if (!empty($row)) {
        echo json_encode(array('message' => 'La fecha de reserva no esta disponible'));
        return;
    }


    //Comprobacion de que la habitacion existe y es la correcta dentro de nuestra base de datos
    $sqlCompruebaHabitacion = "SELECT * FROM habitaciones WHERE id = $habitacion";
    $result = $conex->query($sqlCompruebaHabitacion);
    $resultado = $result->fetch(PDO::FETCH_ASSOC);
    if (empty($resultado)) {
        echo json_encode(array('message' => 'La habitacion no existe'));
        return;
    }
    //Comprobacion de que el usuario existe y cliente es el correcto dentro de nuestra base de datos
    $sqlCompruebaCliente = "SELECT * FROM clientes WHERE id = $cliente";
    $result = $conex->query($sqlCompruebaCliente);
    $resultado = $result->fetch(PDO::FETCH_ASSOC);
    if (empty($resultado)) {
        echo json_encode(array('message' => 'El cliente no existe'));
        return;
    }


    //Insercion de la reserva
    $insertReserva = $conex->prepare("INSERT INTO reservas (Cliente, Habitacion, FDesde, FHasta) VALUES (?, ?, ?, ?)");
    $insertReserva->bindParam(1, $cliente);
    $insertReserva->bindParam(2, $habitacion);
    $insertReserva->bindParam(3, $fDesde);
    $insertReserva->bindParam(4, $fHasta);
    $conex->beginTransaction();
    if ($insertReserva->execute() != 0) {
        $conex->commit();
        echo json_encode(array('message' => 'Reserva realizada'));
        return;
    } else {
        $conex->rollback();
        echo json_encode(array('message' => 'Error al realizar reserva'));
        return;
    }
}

function deleteReservas($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['reserva']['id'])) {
        echo json_encode(array('message' => 'No se ha proporcionado todos los datos necesarios'));
        return;
    }

    $id = $input['reserva']['id'];

    //Elimina la reserva
    $insertOpinion = $conex->prepare("DELETE FROM reservas WHERE id = ?");
    $insertOpinion->bindParam(1, $id);
    $conex->beginTransaction();
    if ($insertOpinion->execute() != 0) {
        $conex->commit();
        echo json_encode(array('message' => 'Reserva eliminada'));
        return;
    } else {
        $conex->rollback();
        echo json_encode(array('message' => 'Error al eliminar Reserva'));
        return;
    }

}
