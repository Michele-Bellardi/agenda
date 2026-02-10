<?php
session_start();
header("Content-Type: application/json");
$conn = new mysqli("localhost","root","","Agenda");

if(!isset($_SESSION["user"])){
    http_response_code(401);
    exit;
}

$id = $_SESSION["user"];

if($_SERVER["REQUEST_METHOD"]=="GET"){
    $r = $conn->query("SELECT nome,cognome,email,telefono,username FROM utenti WHERE id=$id");
    echo json_encode($r->fetch_assoc());
    exit;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $d = json_decode(file_get_contents("php://input"), true);

    $nome = $d["nome"];
    $cognome = $d["cognome"];
    $email = $d["email"];
    $telefono = $d["telefono"];

    // se la password è vuota, NON la cambiamo
    if(!empty($d["password"])){
        $pass = password_hash($d["password"], PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
          "UPDATE utenti SET nome=?, cognome=?, email=?, telefono=?, password=? WHERE id=?"
        );
        $stmt->bind_param("sssssi",$nome,$cognome,$email,$telefono,$pass,$id);
    } else {
        $stmt = $conn->prepare(
          "UPDATE utenti SET nome=?, cognome=?, email=?, telefono=? WHERE id=?"
        );
        $stmt->bind_param("ssssi",$nome,$cognome,$email,$telefono,$id);
    }

    $stmt->execute();
    echo json_encode(["status"=>"ok"]);
}
