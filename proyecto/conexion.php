<?php
function conectarBd(){
    try{
        $conn = new PDO("mysql:host=localhost;dbname=hotelreservation;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e){
        exit($e->getMessage());
    }
}

function getAdminStatusUser($conex, $usuario){
    $sql = "SELECT admin FROM usuarios WHERE id = ". $usuario["id"] . ";";
    $result = $conex->query($sql);
    $adminStatusUser = $result->fetch(PDO::FETCH_ASSOC);
    return $adminStatusUser;
}

?>