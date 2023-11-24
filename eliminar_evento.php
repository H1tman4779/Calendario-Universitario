<?php

require_once('db-connect.php');
if (!isset($_GET['id'])) {
    echo "<script> alert('Id. de Evento no definido.'); location.replace('./') </script>";
    $conn->close();
    exit;
}

$delete = $conn->query("DELETE FROM eventos where id = '{$_GET['id']}'");
if ($delete) {
    echo "<script> alert('El evento se ha eliminado con éxito.'); location.replace('./') </script>";
} else {
    echo "<pre>";
    echo "Ocurrio un ERROR.<br>";
    echo "Error: " . $conn->error . "<br>";
    echo "SQL: " . $sql . "<br>";
    echo "</pre>";
}
$conn->close();
?>

