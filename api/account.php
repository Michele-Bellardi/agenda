<?php
$conn = new mysqli("localhost", "root", "", "Agenda");

$data = json_decode(file_get_contents("php://input"), true);

$nome = $data["nome"];
$cognome = $data["cognome"];
$email = $data["email"];
$telefono = $data["telefono"];
$username = $data["username"];
$password = password_hash($data["password"], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO utenti(nome,cognome,email,telefono,username,password) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss",$nome,$cognome,$email,$telefono,$username,$password);

if($stmt->execute()){
    echo json_encode(["status"=>"ok"]);
}else{
    echo json_encode(["status"=>"error"]);
}
?>
