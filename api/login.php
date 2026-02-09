<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "Agenda");

$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username"];
$password = $data["password"];

$res = $conn->prepare("SELECT * FROM utenti WHERE username=?");
$res->bind_param("s",$username);
$res->execute();
$user = $res->get_result()->fetch_assoc();

if($user && password_verify($password, $user["password"])){
    $_SESSION["user"] = $user["id"];
    echo json_encode(["status"=>"ok"]);
} else {
    echo json_encode(["status"=>"error"]);
}
?>