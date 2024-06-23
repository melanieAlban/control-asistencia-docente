<?php
session_start();
require_once '../servicios/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $docenteId = $data['id'];
        $conn = new Conexion();
        $con = $conn->conectar();

        if ($con->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Database connection error']);
            exit();
        }

        $stmt = $con->prepare("UPDATE empleados SET estado = 'INA' WHERE id = ?");
        $stmt->bind_param("i", $docenteId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating record']);
        }

        $stmt->close();
        $con->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
