<?php

require_once('db-connect.php');
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script> alert('Error: No hay datos para guardar.'); location.replace('./') </script>";
    $conn->close();
    exit;
}
extract($_POST);
$allday = isset($allday);

if (empty($id)) {
    $sql = "INSERT INTO eventos (title,descripcion,start,end) VALUES ('$title','$descripcion','$start','$end')";
} else {
    $sql = "UPDATE eventos set title = '{$title}', descripcion = '{$descripcion}', start = '{$start}', end = '{$end}' where id = '{$id}'";
}
$save = $conn->query($sql);
if ($save) {
    echo "<script> alert('Evento Guardado Correctamente.'); location.replace('./') </script>";
} else {
    echo "<pre>";
    echo "Ocurrio un ERROR.<br>";
    echo "Error: " . $conn->error . "<br>";
    echo "SQL: " . $sql . "<br>";
    echo "</pre>";
}
$conn->close();
?>

