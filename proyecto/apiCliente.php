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

function getClientes($conex){
    $sql = "SELECT * FROM clientes;";

    $result = $conex->query($sql);

    $clientes = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)){
        $clientes[] = $row;
    }
    echo json_encode($clientes);

}

function insertCliente($conex){
    $input = json_decode(file_get_contents('php://input'), true);
    
    $cliente = $input["cliente"];

    $sql = "INSERT INTO clientes (nombre, apellidos, telefono, email) VALUES (?,?,?,?);";
    $result = $conex->query($sql);
    $result->execute([$cliente["nombre"], $cliente["apellidos"], $cliente["telefono"], $cliente["email"]]);
    echo json_encode($result);
}



?>