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

function getReservas($conex){
    $sql = 'SELECT * from reservas';
    global $conex;
    $result = $conex->query($sql);
    $arrayPrin = array();
    $array = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $array["cliente"] = $row["Cliente"];
        $array["habitacion"] = $row["Habitacion"];
        $array["fDesde"] = $row["FDesde"];
        $array["fHasta"] = $row["FHasta"];
        $arrayPrin[$row["id"]] = $array;
    }
    echo json_encode($arrayPrin);
}

function updateReservas($conex){
    
}

function insertReservas($conex){

}

function deleteReservas($conex){

}
?>