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

?>