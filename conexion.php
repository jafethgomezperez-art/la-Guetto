<?php
// conexion.php - Conexión a MySQL/MariaDB

$host = "localhost";        // Servidor de base de datos
$usuario = "root";          // Usuario de MySQL (cambiar si es necesario)
$password = "";             // Contraseña de MySQL (por defecto suele estar vacía en XAMPP/WAMP)
$basedatos = "papeleria_la_ghetto"; // Nombre de la base de datos

// Crear conexión
$mysqli = new mysqli($host, $usuario, $password, $basedatos);

// Verificar conexión
if ($mysqli->connect_error) {
    // Intentar crear la base de datos si no existe
    if ($mysqli->connect_errno === 1049) { // Error: Base de datos no existe
        // Conectar sin base de datos para crearla
        $mysqli_temp = new mysqli($host, $usuario, $password);
        
        if ($mysqli_temp->connect_error) {
            die("Error de conexión: " . $mysqli_temp->connect_error);
        }
        
        // Crear base de datos
        $sql = "CREATE DATABASE IF NOT EXISTS $basedatos 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if ($mysqli_temp->query($sql) === TRUE) {
            echo "Base de datos creada exitosamente.<br>";
            // Reconectar con la base de datos creada
            $mysqli = new mysqli($host, $usuario, $password, $basedatos);
        } else {
            die("Error al crear la base de datos: " . $mysqli_temp->error);
        }
        
        $mysqli_temp->close();
    } else {
        die("Error de conexión: " . $mysqli->connect_error);
    }
}

// Configurar el conjunto de caracteres
$mysqli->set_charset("utf8mb4");

// Función para crear tablas si no existen
function crearTablasSiNoExisten($mysqli) {
    // Tabla de órdenes (ventas)
    $sql_orders = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        total DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Tabla de items de cada orden
    $sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )";
    
    // Tabla de resumen de ventas (para el botón de registrar ventas)
    $sql_sales_summary = "CREATE TABLE IF NOT EXISTS sales_summary (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE NOT NULL,
        total_sales DECIMAL(10,2) NOT NULL,
        total_items INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Ejecutar las consultas
    $queries = [$sql_orders, $sql_order_items, $sql_sales_summary];
    
    foreach ($queries as $query) {
        if (!$mysqli->query($query)) {
            echo "Error al crear tabla: " . $mysqli->error . "<br>";
        }
    }
}

// Llamar a la función para crear tablas
crearTablasSiNoExisten($mysqli);

// Opcional: Mensaje de éxito (puedes comentarlo después)
// echo "Conexión exitosa a la base de datos.";
?>