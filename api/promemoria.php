<?php
session_start();
header("Content-Type: application/json");
$conn = new mysqli("localhost","root","","Agenda");

if(!isset($_SESSION["user"])){
    http_response_code(401);
    exit;
}

if($_SERVER["REQUEST_METHOD"]=="GET"){
    $r = $conn->query(
        "SELECT * FROM promemoria WHERE id_utente=".$_SESSION["user"]
    );
    echo json_encode($r->fetch_all(MYSQLI_ASSOC));
    exit;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $d = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare(
        "INSERT INTO promemoria
        (id_utente, descrizione, data, ora, durata, ricorrenza)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isssis",
        $_SESSION["user"],
        $d["descrizione"],
        $d["data"],
        $d["ora"],
        $d["durata"],
        $d["ricorrenza"]
    );

    $stmt->execute();
    echo json_encode(["status"=>"ok"]);
}
?>