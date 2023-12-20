<?php
include("conexion.php");
include("apiCliente.php");
$arrayTipoHabitaciones = array('Individual', 'Doble', 'Triple', 'Quad', 'Queen', 'King', 'Duplex', 'Doble-doble', 'Estudio', 'Suite');
$conex = conectarBd();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'PUT':
        updateUsuarios($conex);
        break;
    case 'POST':
        registraUsuarios($conex);
        break;
}

function updateUsuarios($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);
    $idUsuario = $input['idUsuario'];
    $id = $input['id'];
    $nombre = $input['nombre'];
    $contrasena = $input['contrasena'];

    if (($adminStatus = getAdminStatusUser($conex, $idUsuario)) == false) {
        echo json_encode(array('error' => 'Error al visualizar el estado de administrador del usuario'));
    } else {
        if ($adminStatus['admin'] == '1') {
            $updateUsuario = $conex->prepare("UPDATE usuarios SET nombre = ?, contrasena = ? WHERE id = ?");
            $updateUsuario->bindParam(1, $nombre);
            $updateUsuario->bindParam(2, $contrasena);
            $updateUsuario->bindParam(3, $id);
            $conex->beginTransaction();
            if($updateUsuario->execute()){
                $conex->commit();
                echo json_encode(array('ok' => 'Usuario actualizado correctamente'));
            }else{
                $conex->rollback();
                echo json_encode(array('error' => 'Error al actualizar el usuario'));
            }
        } else {
            echo json_encode(array('error' => 'No tienes permisos de administrador'));
        }
    }
}

function registraUsuarios($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);

    $usuario = $input['usuario'];
    $cliente = $input['cliente'];
    $Insertcliente = json_decode(insertCliente($conex, $cliente),true);
    if(isset($Insertcliente['error'])){
        echo json_encode(array('error' => 'Error al registrar el usuario'));
        return;
    }else{
        $id = $Insertcliente['cliente'];
    }


    $nombre = $usuario['nombre'];
    $contrasena = $usuario['contrasena'];
    $admin = $usuario['admin'];
    $getIdCliente = $conex->prepare("SELECT id FROM clientes WHERE id = ?");
    $getIdCliente->bindParam(1, $id);

    $conex->beginTransaction();
    if($getIdCliente->execute()){
        $conex->commit();
        echo json_encode(array('ok' => 'Usuario registrado correctamente'));
    }else{
        $conex->rollback();
        echo json_encode(array('error' => 'Error al registrar el usuario'));
    }

    $insertUsuario = $conex->prepare("INSERT INTO usuarios (nombre, contrasena, admin, cliente) VALUES (?, ?, ?, ?)");
    $insertUsuario->bindParam(1, $nombre);
    $insertUsuario->bindParam(2, $contrasena);
    $insertUsuario->bindParam(3, $admin);
    $insertUsuario->bindParam(4, $id);

    $conex->beginTransaction();
    if($insertUsuario->execute()){
        $conex->commit();
        echo json_encode(array('ok' => 'Usuario registrado correctamente'));
    }else{
        $conex->rollback();
        echo json_encode(array('error' => 'Error al registrar el usuario'));
    }

   



}

//Metodo deleteUsuarios no es necesario
//function deleteUsuarios($conex){}

