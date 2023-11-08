<?php
// Confirmar sesión
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}
// Incluir el archivo de conexión a la base de datos
include 'conexionDB.php';
global $con;
// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $nombres = $_POST['nombres'];
    $apellidomat = $_POST['apellidomat'];
    $apellidopat = $_POST['apellidopat'];
    $grupo = $_POST['grupo'];
    $prof_autor = $_SESSION['id_user']; // El autor toma el ID del usuario en sesión
    // Verificar si el estudiante ya existe en la base de datos
    $stmt = $con->prepare("SELECT id_estudiante FROM estudiantes WHERE nombres = ? AND apellidomat = ? AND apellidopat = ?");
    $stmt->bind_param('sss', $nombres, $apellidomat, $apellidopat);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // El estudiante ya existe, puedes mostrar un mensaje de error o realizar alguna acción adicional
        header('Location: inicio.php?error=El+estudiante+ya+existe+en+la+base+de+datos.');
    } else {
        // El estudiante no existe, procede a insertar los datos
        $stmt = $con->prepare("INSERT INTO estudiantes (nombres, apellidomat, apellidopat, grupo, prof_autor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $nombres, $apellidomat, $apellidopat, $grupo, $prof_autor);
        // Ejecutar la consulta de inserción
        if ($stmt->execute()) {
            header('Location: inicio.php?mensaje=El+alumno+se+ha+registrado+exitosamente.');
        } else {
            header('Location: inicio.php?error=Error+al+guardar+los+datos:' . urlencode($stmt->error));
        }
    }
    // Cerrar la sentencia
    $stmt->close();
}
// Cerrar la conexión a la base de datos
$con->close();
