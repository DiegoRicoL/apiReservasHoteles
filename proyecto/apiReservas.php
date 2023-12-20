<?php
include("conexion.php");
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
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);
    $idUsuario = $input['idUsuario'];

    $sql = "SELECT * from usuarios where id = ?";
    $result = $conex->prepare($sql);
    $result->bindParam(1, $idUsuario);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row["Admin"] == "1") {
        $ok = true;
    }else{
        $ok = false;
        echo json_encode(array('message' => 'No tienes permisos para actualizar habitaciones'));
    }

    $sql = 'SELECT * from reservas';
    global $conex;
    $result = $conex->query($sql);
    $arrayPrin = array();
    $array = array();

    if ($ok) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $array["cliente"] = $row["Cliente"];
            $array["habitacion"] = $row["Habitacion"];
            $array["fDesde"] = $row["FDesde"];
            $array["fHasta"] = $row["FHasta"];
            $arrayPrin[$row["id"]] = $array;
        }
        echo json_encode($arrayPrin);
    } else {
        echo json_encode(array('message' => 'No tienes permisos para actualizar habitaciones'));
    }
}

function updateReservas($conex)
{
    global $conex;
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $fDesde = $input['fDesde'];
    $fHasta = $input['fHasta'];
    
    $sqlCheckeaFecha = "SELECT * from reservas where fDesde = ? <= fHasta AND fDesde <= fHasta = ?";
    $result = $conex->prepare($sqlCheckeaFecha);
    $result->bindParam(1, $fDesde);
    $result->bindParam(2, $fHasta);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if($row->rowCount() == 0){
        $ok = true;
    }else{
        $ok = false;
        echo json_encode(array('message' => 'La fecha de reserva no esta disponible'));
    }
    if ($ok) {
        $insertReserva = $conex->prepare("UPDATE reservas SET FDesde = ?, FHasta = ? WHERE id = ?");
        $insertReserva->bindParam(1, $fDesde);
        $insertReserva->bindParam(2, $fHasta);
        $insertReserva->bindParam(3, $id);
        $conex->beginTransaction();
        if ($insertReserva->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Reserva actualizada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al actualizar reserva'));
        }
    } else {
        echo json_encode(array('message' => 'Error al actualizar reserva'));
    }
}

function insertReservas($conex)
{
    global $conex;
    $ok = false;
    $input = json_decode(file_get_contents('php://input'), true);
    $cliente = $input['cliente'];
    $habitacion = $input['habitacion'];
    $fDesde = $input['fDesde'];
    $fHasta = $input['fHasta'];


    $sqlCheckeaFecha = "SELECT * from reservas where fDesde = ? <= fHasta AND fDesde <= fHasta = ?";
    $result = $conex->prepare($sqlCheckeaFecha);
    $result->bindParam(1, $fDesde);
    $result->bindParam(2, $fHasta);
    $result->execute();
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if($row->rowCount() == 0){
        $ok = true;
    }else{
        $ok = false;
        echo json_encode(array('message' => 'La fecha de reserva no esta disponible'));
    }


    //Comprobacion de que la habitacion existe y es la correcta dentro de nuestra base de datos
    $sqlCompruebaHabitacion = "SELECT * FROM habitaciones WHERE id = $habitacion";
    $result = $conex->query($sqlCompruebaHabitacion);
    $resultado = $result->fetch(PDO::FETCH_ASSOC);
    if ($resultado['id'] == $habitacion) {
        $ok = true;
    } else {
        $ok = false;
        echo json_encode(array('message' => 'La habitacion no existe'));
    }


    //Comprueba si la habitacion esta ocupada
    $sqlCompruebaHabitacion = "SELECT * FROM habitaciones WHERE Tipo = $habitacion";
    $result = $conex->query($sqlCompruebaHabitacion);
    $resultado = $result->fetch(PDO::FETCH_ASSOC);
    //Si esta ocupada no se puede reservar
    if ($resultado['Ocupado'] == '1') {
        $ok = false;
        echo json_encode(array('message' => 'No se puede reservar la habitacion seleccionada porque ya esta ocupada'));
    } else {
        $ok = true;
    }

    //Comprobacion de que el usuario existe y cliente es el correcto dentro de nuestra base de datos
    $sqlCompruebaCliente = "SELECT * FROM clientes WHERE id = $cliente";
    $result = $conex->query($sqlCompruebaCliente);
    $resultado = $result->fetch(PDO::FETCH_ASSOC);
    if ($resultado['id'] == $cliente) {
        $ok = true;
    } else {
        $ok = false;
        echo json_encode(array('message' => 'El cliente no existe'));
    }


    //Insercion de la reserva
    if ($ok) {
        $insertReserva = $conex->prepare("INSERT INTO reservas (Cliente, Habitacion, FDesde, FHasta) VALUES (?, ?, ?, ?)");
        $insertReserva->bindParam(1, $cliente);
        $insertReserva->bindParam(2, $habitacion);
        $insertReserva->bindParam(3, $fDesde);
        $insertReserva->bindParam(4, $fHasta);
        $conex->beginTransaction();
        if ($insertReserva->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Reserva realizada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al realizar reserva'));
        }

        //Actualiza el campo ocupado de la habitacion a 1
        $updateHabitacion = $conex->prepare("UPDATE habitaciones SET Ocupado = 1 WHERE id = ?");
        $updateHabitacion->bindParam(1, $habitacion);
        $conex->beginTransaction();
        if ($updateHabitacion->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion actualizada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al actualizar habitacion'));
        }
    } else {
        echo json_encode(array('message' => 'Error al realizar reserva'));
    }
}

function deleteReservas($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $ok = true;
    //Elimina la reserva y actualiza el campo ocupado de la habitacion a 0 para que se pueda volver a reservar
    if ($ok) {

        $insertOpinion = $conex->prepare("DELETE FROM reservas WHERE id = ?");
        $insertOpinion->bindParam(1, $id);
        $conex->beginTransaction();
        if ($insertOpinion->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Reserva eliminada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al eliminar Reserva'));
        }

        //Actualiza el campo ocupado de la habitacion a 0
        $sqlCompruebaHabitacion = "SELECT * FROM reservas WHERE id = $id";
        $result = $conex->query($sqlCompruebaHabitacion);
        $resultado = $result->fetch(PDO::FETCH_ASSOC);
        $habitacion = $resultado['Habitacion'];
        $updateHabitacion = $conex->prepare("UPDATE habitaciones SET Ocupado = 0 WHERE id = ?");
        $updateHabitacion->bindParam(1, $habitacion);
        $conex->beginTransaction();
        if ($updateHabitacion->execute() != 0) {
            $conex->commit();
            echo json_encode(array('message' => 'Habitacion actualizada'));
        } else {
            $conex->rollback();
            echo json_encode(array('message' => 'Error al actualizar habitacion'));
        }

       
    } else {
        echo json_encode(array('message' => 'Error al eliminar Reserva'));
    }

}
