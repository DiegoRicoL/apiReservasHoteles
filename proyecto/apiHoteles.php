<?php
include_once("conexion.php");

$conex = conectarBd();

switch ($_SERVER["REQUEST_METHOD"]) {
    case "PUT":
        updateHotel($conex);
        break;
    case "POST":
        insertHotel($conex);
        break;
    case "DELETE";
        deleteById($conex);
        break;
    case "GET":
        getHoteles($conex);
        break;
    default:
        echo json_encode(array("error" => "Método HTTP no soportado"));
        break;
}


function getHoteles($conex){
    $sql = "SELECT * FROM hoteles;";

    $result = $conex->query($sql);

    $hoteles = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
        $hoteles[] = $row;
    }
    echo json_encode($hoteles);

}

function insertHotel($conex){
    $input = json_decode(file_get_contents('php://input'), true);
    
    $usuario = $input["usuario"];
    $hotel = $input["hotel"];

    if(($adminStatus = getAdminStatusUser($conex, $usuario)) == false){
        echo json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
        return;
    } else {
        if($adminStatus["admin"] == 1){
            
            $insertHotel = $conex->prepare("INSERT INTO hoteles (nombre, ubicacion) VALUES (:nombre, :ubicacion);");

            $insertHotel->bindParam(":nombre", $hotel["nombre"]);
            $insertHotel->bindParam(":ubicacion", $hotel["ubicacion"]);

            $conex->beginTransaction();
            if($insertHotel->execute() != 0){
                $conex->commit();
                echo json_encode(array("success" => "Hotel insertado correctamente"));
            } else {
                $conex->rollBack();
                echo json_encode(array("error" => "Error al insertar el hotel"));
            }

        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
            return;
        }
    }

    
}

function updateHotel($conex){
    $input = json_decode(file_get_contents('php://input'), true);
    
    $usuario = $input["usuario"];
    $hotel = $input["hotel"];

    if(($adminStatus = getAdminStatusUser($conex, $usuario)) == false){
        echo json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
        return;
    } else {
        if($adminStatus["admin"] == 1){
            
            $updateHotel = $conex->prepare("UPDATE hoteles SET nombre = :nombre, valoracion = :valoracion, ubicacion = :ubicacion WHERE id = :id;");

            $updateHotel->bindParam(":nombre", $hotel["nombre"]);
            $updateHotel->bindParam(":valoracion", $hotel["valoracion"]);
            $updateHotel->bindParam(":ubicacion", $hotel["ubicacion"]);
            $updateHotel->bindParam(":id", $hotel["id"]);

            $conex->beginTransaction();
            if($updateHotel->execute() != 0){
                $conex->commit();
                echo json_encode(array("success" => "Hotel actualizado correctamente"));
            } else {
                $conex->rollBack();
                echo json_encode(array("error" => "Error al actualizar el hotel"));
            }

        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
            return;
        }
    }
}

function deleteById($conex){
    $input = json_decode(file_get_contents('php://input'), true);
    
    $usuario = $input["usuario"];
    $hotel = $input["hotel"];

    if(($adminStatus = getAdminStatusUser($conex, $usuario)) == false){
        echo json_encode(array("error" => "Error al obtener el estado de administrador del usuario"));
        return;
    } else {
        if($adminStatus["admin"] == 1){
            
            $deleteHotel = $conex->prepare("DELETE FROM hoteles WHERE id = :id;");

            $deleteHotel->bindParam(":id", $hotel["id"]);

            $conex->beginTransaction();
            if($deleteHotel->execute() != 0){
                $conex->commit();
                echo json_encode(array("success" => "Hotel eliminado correctamente"));
            } else {
                $conex->rollBack();
                echo json_encode(array("error" => "Error al eliminar el hotel"));
            }

        } else {
            echo json_encode(array("error" => "El usuario no es administrador"));
            return;
        }
    }
}
?>