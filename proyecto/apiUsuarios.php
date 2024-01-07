<?php
include_once("conexion.php");
$arrayTipoHabitaciones = array('Individual', 'Doble', 'Triple', 'Quad', 'Queen', 'King', 'Duplex', 'Doble-doble', 'Estudio', 'Suite');
$conex = conectarBd();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'PUT':
        updateUsuarios($conex);
        break;
    case 'POST':
        registraUsuarios($conex);
        break;
    default:
        echo json_encode(array('error' => 'Metodo no permitido'));
        break;
}

function updateUsuarios($conex)
{
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input) || empty($input['usuario']) || empty($input['update'])) {
        echo json_encode(array('error' => 'Error al actualizar el usuario: No se ha proporcionado un usuario'));
        return;
    }
    $usuario = $input['usuario'];
    $update = $input['update'];

    $nombre = $update['nombre'];
    $contrasena = $update['contrasena'];
    $id = $update['id'];
    

    if (($adminStatus = getAdminStatusUser($conex, $usuario)) == false) {
        echo json_encode(array('error' => 'Error al visualizar el estado de administrador del usuario'));
        return;
    } else {
        if ($adminStatus['admin'] == '1') {
            $updateUsuario = $conex->prepare("UPDATE usuarios SET nombre = ?, contrasena = ? WHERE id = ?");
            $updateUsuario->bindParam(1, $nombre);
            $updateUsuario->bindParam(2, $contrasena);
            $updateUsuario->bindParam(3, $id);
            $conex->beginTransaction();
            if($updateUsuario->execute()){
                $conex->commit();
                echo json_encode(array('success' => 'Usuario actualizado correctamente'));
                return;
            }else{
                $conex->rollback();
                echo json_encode(array('error' => 'Error al actualizar el usuario'));
                return;
            }
        } else {
            echo json_encode(array('error' => 'No tienes permisos de administrador'));
            return;
        }
    }
}

function registraUsuarios($conex)
{
    require_once("apiCliente.php");
    global $conex;
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input) || empty($input['usuario']) || empty($input['cliente'])) {
        echo json_encode(array('error' => 'Error al registrar el usuario: No se ha proporcionado un usuario'));
        return;
    }
    $usuario = $input['usuario'];
    $cliente = $input['cliente'];

    $id = json_decode(insertCliente($conex, $cliente),true)['cliente'];

    $nombre = $usuario['nombre'];
    $contrasena = $usuario['contrasena'];
    $admin = $usuario['admin'];
    $getIdCliente = $conex->prepare("SELECT id FROM clientes WHERE id = ?");
    $getIdCliente->bindParam(1, $id);
    $getIdCliente->execute();

    $id = $getIdCliente->fetch(PDO::FETCH_ASSOC);
    if(empty($id)){
        echo json_encode(array('error' => 'Error al registrar el usuario'));
        return;
    } else {
        $id = $id['id'];
    }

    $insertUsuario = $conex->prepare("INSERT INTO usuarios (nombre, contrasena, admin, cliente) VALUES (?, ?, ?, ?)");
    $insertUsuario->bindParam(1, $nombre);
    $insertUsuario->bindParam(2, $contrasena);
    $insertUsuario->bindParam(3, $admin);
    $insertUsuario->bindParam(4, $id);

    $conex->beginTransaction();
    if($insertUsuario->execute()){
        $conex->commit();
        echo json_encode(array('success' => 'Usuario registrado correctamente'));
        return;
    }else{
        $conex->rollback();

        deleteCliente($conex, $id);
        echo json_encode(array('error' => 'Error al registrar el usuario'));
        return;
    }

}

//se usa desde apiCliente.php
function deleteUsuarios($conex, $id){
    $deleteUsuario = $conex->prepare("DELETE FROM usuarios WHERE id = ?");
    $deleteUsuario->bindParam(1, $id);
    $conex->beginTransaction();
    if($deleteUsuario->execute()){
        $conex->commit();
        echo json_encode(array('success' => 'Usuario eliminado correctamente'));
    }else{
        $conex->rollback();
        echo json_encode(array('error' => 'Error al eliminar el usuario'));
    }
}

