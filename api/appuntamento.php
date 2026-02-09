<?php
session_start();
header("Content-Type: application/json");
$conn = new mysqli("localhost","root","","Agenda");

if(!isset($_SESSION["user"])){
    http_response_code(401);
    exit;
}

// ---------------- GET: appuntamenti dell'utente ----------------
if($_SERVER["REQUEST_METHOD"]=="GET"){
    $q = $conn->query("
        SELECT DISTINCT a.*
        FROM appuntamenti a
        JOIN appuntamento_utenti au ON a.id = au.id_app
        WHERE au.id_utente = ".$_SESSION["user"]
    );
    echo json_encode($q->fetch_all(MYSQLI_ASSOC));
    exit;
}

// ---------------- POST: nuovo appuntamento ----------------
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $d = json_decode(file_get_contents("php://input"), true);

    $emails = $d["emails"];
    $utenti = [];

    //aggiungo l'utente creatore
    $utenti[] = $_SESSION["user"];

    // recupero ID dagli indirizzi email
    foreach($emails as $email){
        $email = trim($email);
        if($email == "") continue;

        $stmt = $conn->prepare("SELECT id FROM utenti WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if(!$res){
            echo json_encode(["status"=>"notfound"]);
            exit;
        }

        // evito duplicati
        if(!in_array($res["id"], $utenti)){
            $utenti[] = $res["id"];
        }
    }

    // controllo disponibilità
    foreach($utenti as $u){
        $q = $conn->query("
            SELECT *
            FROM appuntamenti a
            JOIN appuntamento_utenti au ON a.id = au.id_app
            WHERE au.id_utente = $u
              AND a.data = '{$d["data"]}'
              AND a.ora = '{$d["ora"]}'
        ");
        if($q->num_rows > 0){
            echo json_encode(["status"=>"busy"]);
            exit;
        }
    }

    // inserimento appuntamento
    $stmt = $conn->prepare("
        INSERT INTO appuntamenti (descrizione, data, ora, durata)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssi",
        $d["descrizione"],
        $d["data"],
        $d["ora"],
        $d["durata"]
    );
    $stmt->execute();

    $id_app = $conn->insert_id;

    // collegamento utenti - appuntamento
    foreach($utenti as $u){
        $conn->query(
            "INSERT INTO appuntamento_utenti (id_app, id_utente)
             VALUES ($id_app, $u)"
        );
    }

    echo json_encode(["status"=>"ok"]);
}
?>