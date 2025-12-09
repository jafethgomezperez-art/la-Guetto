<?php
session_start();

// Incluir la conexión a la base de datos
require_once "conexion.php";

/* =========================
   Datos de productos (20)
   ========================= */
$productos = [
    // ARTE (5)
    ['id'=>101,'code'=>'A1001','name'=>'Set Pinceles 10pz','price'=>120.00,'category'=>'Arte','img'=>'https://rodin.mx/cdn/shop/files/d8d7c07c-7b56-49f6-96e6-b29dbdddb220_512x1223.jpg?v=1764087416'],
    ['id'=>102,'code'=>'A1002','name'=>'Acuarelas 12 colores','price'=>85.50,'category'=>'Arte','img'=>'https://m.media-amazon.com/images/I/81H8XFRQjIL.jpg'],
    ['id'=>103,'code'=>'A1003','name'=>'Bloc Sketch A4','price'=>95.00,'category'=>'Arte','img'=>'https://m.media-amazon.com/images/I/81hJ4EWI9SL.jpg'],
    ['id'=>104,'code'=>'A1004','name'=>'Lienzo 30x40 cm','price'=>150.00,'category'=>'Arte','img'=>'https://production-tailoy-repo-magento-statics.s3.amazonaws.com/imagenes/872x872/productos/i/l/i/lienzo-ove-15-x-20-cm-con-diseno-animales-de-la-selva-75065004-default-1.jpg'],
    ['id'=>105,'code'=>'A1005','name'=>'Acrílicos 6pz','price'=>65.25,'category'=>'Arte','img'=>'https://m.media-amazon.com/images/I/81xrGstqRoL.jpg'],

    // ESCOLAR (5)
    ['id'=>201,'code'=>'E2001','name'=>'Cuaderno profesional 100h','price'=>30.00,'category'=>'Escolar','img'=>'https://officemax.vtexassets.com/arquivos/ids/1346785/12948_1.jpg?v=638158805884270000'],
    ['id'=>202,'code'=>'E2002','name'=>'Set Colores 24pz','price'=>55.00,'category'=>'Escolar','img'=>'https://ofixmx.vtexassets.com/arquivos/ids/162075/LAPICES-DE-COLOR-24-L-TRIANGULAR-SMARTY.jpg?v=638417592605270000'],
    ['id'=>203,'code'=>'E2003','name'=>'Plumas Punta Fina 3pz','price'=>40.00,'category'=>'Escolar','img'=>'https://rumaonline.com/cdn/shop/products/Stabilo-Point-88-30-322315.jpg?v=1693857601&width=300'],
    ['id'=>204,'code'=>'E2004','name'=>'Mochila Escolar','price'=>180.00,'category'=>'Escolar','img'=>'https://http2.mlstatic.com/D_NQ_NP_899325-CBT81568343644_012025-O-mochila-escolar-viaje-bolsa-juvenil-de-moda-estio-coreana-para-laptop-kawaii-impermeable-de-moda.webp'],
    ['id'=>205,'code'=>'E2005','name'=>'Calculadora Científica','price'=>320.00,'category'=>'Escolar','img'=>'https://www.correosclic.gob.mx/images/thumbs/0011327_calculadora-cientifica-cteifree-991es-plus-417-funciones.jpeg'],

    // URBANO (5)
    ['id'=>301,'code'=>'U3001','name'=>'Rotulador Urbano 1pz','price'=>110.00,'category'=>'Urbano','img'=>'https://m.media-amazon.com/images/I/81LX3wlMJRL._AC_UF894,1000_QL80_.jpg'],
    ['id'=>302,'code'=>'U3002','name'=>'Spray Arte 400ml','price'=>290.00,'category'=>'Urbano','img'=>'https://ventdepot.mx/cdn/shop/files/Lata-Pinturas-Aerosol-Met_C3_A1lico-MXAER-001-g_grande.jpg?v=1762189505'],
    ['id'=>303,'code'=>'U3003','name'=>'Stickers Pack','price'=>85.00,'category'=>'Urbano','img'=>'https://m.media-amazon.com/images/I/81w5sgS9VLL._AC_UF894,1000_QL80_.jpg'],
    ['id'=>304,'code'=>'U3004','name'=>'Libreta Urbana','price'=>60.00,'category'=>'Urbano','img'=>'https://tiendanecaxa.mx/cdn/shop/files/rn-image_picker_lib_temp_5f571aba-d431-4a65-8e07-ff759be9e22c_600x.jpg?v=1750878286'],
    ['id'=>305,'code'=>'U3005','name'=>'Marcadores Metalizados','price'=>140.00,'category'=>'Urbano','img'=>'https://tienda.faber-castell.com.mx/cdn/shop/products/452006_MarkermetallicpermanentHS6x_PX_Office_70647.jpg?v=1683562462'],

    // PAPELES (5)
    ['id'=>401,'code'=>'P4001','name'=>'Foamy 45x60 cm Negro','price'=>16.10,'category'=>'Papeles','img'=>'https://papeleriadelahorro.mx/cdn/shop/products/1039124.jpg?v=1751744843'],
    ['id'=>402,'code'=>'P4002','name'=>'Cartulina A4 Blanca','price'=>8.00,'category'=>'Papeles','img'=>'https://elpartenon.com.mx/wp-content/uploads/2022/02/Cartulina-bristol-blanca.jpeg'],
    ['id'=>403,'code'=>'P4003','name'=>'Papel Bond 500h','price'=>120.00,'category'=>'Papeles','img'=>'https://m.media-amazon.com/images/I/61PaC5O+wRL._AC_UF1000,1000_QL80_.jpg'],
    ['id'=>404,'code'=>'P4004','name'=>'Hojas Colores 50pz','price'=>23.30,'category'=>'Papeles','img'=>'https://m.media-amazon.com/images/I/51FAcbwHCTL._AC_SL1000_.jpg'],
    ['id'=>405,'code'=>'P4005','name'=>'Papel Fotográfico','price'=>45.00,'category'=>'Papeles','img'=>'https://m.media-amazon.com/images/I/71mcNggtQwL.jpg'],
];

/* =========================
   Variables para mensajes
   ========================= */
$message = '';
$messageType = '';
$showInvoice = false;
$invoiceContent = '';
$showInvoiceForm = false;
$isImmediatePurchase = false;
$search_result = null;
$search_performed = false;

/* =========================
   Manejo de carrito en sesión
   ========================= */
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['invoice_data'])) $_SESSION['invoice_data'] = [];

/* =========================
   PROCESAMIENTO DE TODAS LAS ACCIONES
   ========================= */

// 1. BOTÓN: Registrar ventas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_sales') {
    $total_ventas = 0;
    $num_productos = 0;
    
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $prod = findProduct($pid, $productos);
            if ($prod) {
                $total_ventas += ($prod['price'] * $qty);
                $num_productos += $qty;
            }
        }
    }
    
    // Verificar si ya existe un registro para hoy
    $check_stmt = $mysqli->prepare("SELECT id FROM sales_summary WHERE date = CURDATE()");
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        // Actualizar registro existente
        $stmt = $mysqli->prepare("UPDATE sales_summary SET total_sales = total_sales + ?, total_items = total_items + ? WHERE date = CURDATE()");
    } else {
        // Crear nuevo registro
        $stmt = $mysqli->prepare("INSERT INTO sales_summary (date, total_sales, total_items) VALUES (CURDATE(), ?, ?)");
    }
    
    $check_stmt->close();
    
    $stmt->bind_param("di", $total_ventas, $num_productos);
    if ($stmt->execute()) {
        $message = "✅ Ventas registradas: $" . number_format($total_ventas, 2) . " (" . $num_productos . " productos)";
        $messageType = 'success';
    } else {
        $message = "❌ Error al registrar ventas: " . $mysqli->error;
        $messageType = 'error';
    }
    $stmt->close();
}

// 2. BOTÓN: Buscar artículo en inventario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search_inventory') {
    $search_term = trim($_POST['search_term'] ?? '');
    $search_performed = true;
    
    if (!empty($search_term)) {
        $search_result = [];
        foreach ($productos as $p) {
            if (stripos($p['name'], $search_term) !== false || 
                stripos($p['code'], $search_term) !== false ||
                stripos($p['category'], $search_term) !== false) {
                $search_result[] = $p;
            }
        }
        if (empty($search_result)) {
            $message = "🔍 No se encontraron productos para: " . htmlspecialchars($search_term);
            $messageType = 'info';
        }
    } else {
        $message = "🔍 Por favor ingresa un término de búsqueda";
        $messageType = 'info';
    }
}

// 3. Añadir al carrito (botón 🛒 Añadir)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $pid = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['quantity'] ?? 1));
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }
    $message = "✅ Producto añadido al carrito";
    $messageType = 'success';
}

// 4. COMPRA INMEDIATA (botón ⚡ Comprar ahora)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'buy_now') {
    $pid = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['quantity'] ?? 1));
    
    // Crear una compra inmediata sin pasar por el carrito
    $prod = findProduct($pid, $productos);
    if ($prod) {
        $subtotal = $prod['price'] * $qty;
        $iva = $subtotal * 0.16;
        $total = $subtotal + $iva;
        
        // Guardar en sesión para mostrar el formulario de factura
        $_SESSION['immediate_purchase'] = [
            'product_id' => $pid,
            'quantity' => $qty,
            'product' => $prod,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total
        ];
        
        // Mostrar formulario de factura
        $showInvoiceForm = true;
        $isImmediatePurchase = true;
    }
}

// 5. Eliminar item del carrito
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $rid = intval($_GET['id']);
    unset($_SESSION['cart'][$rid]);
    $message = "✅ Producto eliminado del carrito";
    $messageType = 'success';
}

// 6. Mostrar formulario de factura (para carrito)
if (isset($_GET['show_invoice_form']) && $_GET['show_invoice_form'] == '1') {
    $showInvoiceForm = true;
    $isImmediatePurchase = false;
}

// 7. Procesar formulario de factura y finalizar compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_invoice') {
    
    // Validar datos requeridos
    $required_fields = ['nombre', 'email', 'rfc', 'direccion'];
    $valid = true;
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $valid = false;
            break;
        }
    }
    
    if ($valid) {
        // Guardar datos de facturación
        $_SESSION['invoice_data'] = [
            'nombre' => $_POST['nombre'],
            'email' => $_POST['email'],
            'rfc' => $_POST['rfc'],
            'direccion' => $_POST['direccion'],
            'telefono' => $_POST['telefono'] ?? '',
            'razon_social' => $_POST['razon_social'] ?? $_POST['nombre']
        ];
        
        // Determinar si es compra inmediata o del carrito
        if (isset($_SESSION['immediate_purchase'])) {
            // COMPRA INMEDIATA
            $immediate = $_SESSION['immediate_purchase'];
            $prod = $immediate['product'];
            
            // Insertar orden en BD
            $stmt = $mysqli->prepare("INSERT INTO orders (total, customer_name, customer_email, customer_rfc, customer_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("dssss", $immediate['total'], $_POST['nombre'], $_POST['email'], $_POST['rfc'], $_POST['direccion']);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();
            
            // Insertar producto
            $stmt2 = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("iiid", $order_id, $prod['id'], $immediate['quantity'], $prod['price']);
            $stmt2->execute();
            $stmt2->close();
            
            // Generar factura
            $invoice_html = generateInvoice($order_id, [['product' => $prod, 'quantity' => $immediate['quantity']]], $immediate['quantity'], $_SESSION['invoice_data'], $immediate['subtotal'], $immediate['iva'], $immediate['total']);
            
            // Limpiar
            unset($_SESSION['immediate_purchase']);
            
            $message = "✅ Compra realizada. Factura #$order_id generada.";
            $messageType = 'success';
            
            // Mostrar factura
            $showInvoice = true;
            $invoiceContent = $invoice_html;
            
        } else if (!empty($_SESSION['cart'])) {
            // COMPRA DESDE CARRITO
            $subtotal = 0;
            $cart_products = [];
            
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $prod = findProduct($pid, $productos);
                if ($prod) {
                    $cart_products[] = ['product' => $prod, 'quantity' => $qty];
                    $subtotal += ($prod['price'] * $qty);
                }
            }
            
            $iva = $subtotal * 0.16;
            $total = $subtotal + $iva;
            
            // Insertar orden en BD
            $stmt = $mysqli->prepare("INSERT INTO orders (total, customer_name, customer_email, customer_rfc, customer_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("dssss", $total, $_POST['nombre'], $_POST['email'], $_POST['rfc'], $_POST['direccion']);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();
            
            // Insertar productos
            $stmt2 = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $prod = findProduct($pid, $productos);
                $stmt2->bind_param("iiid", $order_id, $pid, $qty, $prod['price']);
                $stmt2->execute();
            }
            $stmt2->close();
            
            // Generar factura
            $invoice_html = generateInvoice($order_id, $cart_products, array_sum($_SESSION['cart']), $_SESSION['invoice_data'], $subtotal, $iva, $total);
            
            // Limpiar carrito
            $_SESSION['cart'] = [];
            
            $message = "✅ Compra finalizada. Factura #$order_id generada.";
            $messageType = 'success';
            
            // Mostrar factura
            $showInvoice = true;
            $invoiceContent = $invoice_html;
            
        }
    } else {
        $message = "❌ Por favor completa todos los campos requeridos (*)";
        $messageType = 'error';
        $showInvoiceForm = true;
        $isImmediatePurchase = isset($_SESSION['immediate_purchase']);
    }
}

// 8. Botón para actualizar carrito
if (isset($_GET['update_cart']) && $_GET['update_cart'] == '1') {
    // Simplemente recarga la página
    $message = "🔄 Carrito actualizado";
    $messageType = 'info';
}

/* =========================
   Filtrado por categoría
   ========================= */
$categoriaSeleccionada = $_GET['categoria'] ?? null;
$productosFiltrados = [];
if ($categoriaSeleccionada) {
    foreach ($productos as $p) if ($p['category'] === $categoriaSeleccionada) $productosFiltrados[] = $p;
}

/* =========================
   Funciones auxiliares
   ========================= */
function findProduct($id, $productos) {
    foreach ($productos as $p) if ($p['id'] == $id) return $p;
    return null;
}

function generateInvoice($order_id, $products, $total_items, $customer_data, $subtotal, $iva, $total) {
    $invoice_date = date('d/m/Y H:i:s');
    $invoice_number = str_pad($order_id, 8, '0', STR_PAD_LEFT);
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Factura Digital #' . $invoice_number . '</title>
        <style>
            .invoice {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                border: 2px solid #333;
                background: white;
            }
            .invoice-header {
                text-align: center;
                border-bottom: 3px solid #4361ee;
                padding-bottom: 20px;
                margin-bottom: 30px;
            }
            .company-info {
                text-align: center;
                margin-bottom: 30px;
            }
            .customer-info, .invoice-details {
                margin-bottom: 30px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            .items-table th, .items-table td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
            .items-table th {
                background: #4361ee;
                color: white;
            }
            .totals {
                text-align: right;
                margin-top: 20px;
            }
            .footer {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
                color: #666;
                font-size: 12px;
            }
            .qr-code {
                text-align: center;
                margin: 20px 0;
            }
            .stamp {
                position: relative;
                padding: 20px;
                margin: 20px 0;
                text-align: center;
            }
            .paid-stamp {
                display: inline-block;
                padding: 10px 30px;
                background: #06d6a0;
                color: white;
                font-weight: bold;
                transform: rotate(-15deg);
                border: 3px solid #047857;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="invoice">
            <div class="invoice-header">
                <h1>FACTURA DIGITAL</h1>
                <h2>LA GHETTO PAPELERÍA</h2>
                <p>RFC: GHG-123456-ABC</p>
                <p>Calle Roque Ortega, López Mateos, Lote I Esquina</p>
                <p>Ejidal Emiliano Zapata, 55024 Ecatepec de Morelos, Méx.</p>
                <p>Tel: 55-1234-5678 | www.laghettopapeleria.com</p>
            </div>
            
            <div class="company-info">
                <h3>COMPROBANTE FISCAL DIGITAL POR INTERNET</h3>
                <p>Folio Fiscal: ' . strtoupper(uniqid()) . '</p>
            </div>
            
            <div class="invoice-details">
                <p><strong>No. Factura:</strong> ' . $invoice_number . '</p>
                <p><strong>Fecha y Hora:</strong> ' . $invoice_date . '</p>
                <p><strong>Forma de Pago:</strong> Efectivo</p>
                <p><strong>Método de Pago:</strong> Pago en una sola exhibición</p>
            </div>
            
            <div class="customer-info">
                <h3>DATOS DEL CLIENTE</h3>
                <p><strong>Nombre/Razón Social:</strong> ' . htmlspecialchars($customer_data['razon_social']) . '</p>
                <p><strong>RFC:</strong> ' . htmlspecialchars($customer_data['rfc']) . '</p>
                <p><strong>Dirección:</strong> ' . htmlspecialchars($customer_data['direccion']) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($customer_data['email']) . '</p>
                <p><strong>Teléfono:</strong> ' . htmlspecialchars($customer_data['telefono']) . '</p>
            </div>
            
            <h3>DETALLE DE PRODUCTOS</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Precio Unitario</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($products as $item) {
        $product = $item['product'];
        $quantity = $item['quantity'];
        $total_item = $product['price'] * $quantity;
        
        $html .= '
                    <tr>
                        <td>' . $quantity . '</td>
                        <td>' . htmlspecialchars($product['code']) . '</td>
                        <td>' . htmlspecialchars($product['name']) . '</td>
                        <td>$ ' . number_format($product['price'], 2) . '</td>
                        <td>$ ' . number_format($total_item, 2) . '</td>
                    </tr>';
    }
    
    $html .= '
                </tbody>
            </table>
            
            <div class="totals">
                <p><strong>Subtotal:</strong> $ ' . number_format($subtotal, 2) . '</p>
                <p><strong>IVA (16%):</strong> $ ' . number_format($iva, 2) . '</p>
                <p><strong>Total:</strong> $ ' . number_format($total, 2) . '</p>
                <p style="font-size: 1.2em; margin-top: 10px;">
                    <strong>TOTAL A PAGAR:</strong> $ ' . number_format($total, 2) . ' MXN
                </p>
            </div>
            
            <div class="stamp">
                <div class="paid-stamp">PAGADO</div>
            </div>
            
            <div class="qr-code">
                <!-- Espacio para código QR -->
                <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <p><strong>Código de Verificación:</strong> ' . strtoupper(uniqid()) . '</p>
                    <p>Esta factura puede ser verificada en el sistema</p>
                </div>
            </div>
            
            <div class="footer">
                <p>Este documento es una representación impresa de un Comprobante Fiscal Digital.</p>
                <p>La información contenida en esta factura puede ser verificada en el portal del SAT.</p>
                <p>Gracias por su preferencia. Vuelva pronto.</p>
                <p>Factura generada el ' . $invoice_date . ' por La Ghetto Papelería</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>La Ghetto - Papelería</title>
<style>
:root {
    --primary: #4361ee;
    --primary-dark: #3a56d4;
    --secondary: #7209b7;
    --success: #06d6a0;
    --danger: #ef476f;
    --warning: #ffd166;
    --dark: #2b2d42;
    --light: #f8f9fa;
    --gray: #6c757d;
    --border: #e9ecef;
    --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 8px 15px rgba(0, 0, 0, 0.15);
    --radius: 12px;
    --transition: all 0.3s ease;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    color: var(--dark);
    min-height: 100vh;
    line-height: 1.6;
}

/* HEADER MODERNIZADO */
.site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 80px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    z-index: 1000;
    color: white;
}

.brand {
    font-weight: 800;
    font-size: 24px;
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.brand::before {
    content: "🎨";
    font-size: 28px;
}

.header-left {
    display: flex;
    gap: 25px;
    align-items: center;
}

.location {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    transition: var(--transition);
    text-decoration: none;
    color: white;
    font-weight: 600;
}

.location:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.location img {
    width: 24px;
    height: 24px;
    filter: brightness(0) invert(1);
}

/* Botones de acción en header */
.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.action-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-register {
    background: var(--success);
    color: white;
}

.btn-update {
    background: var(--warning);
    color: var(--dark);
}

.btn-search {
    background: var(--light);
    color: var(--primary);
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

/* Layout mejorado */
.main {
    display: flex;
    gap: 25px;
    max-width: 1400px;
    margin: 100px auto 40px;
    padding: 0 20px;
}

.sidebar {
    width: 280px;
    background: white;
    border-radius: var(--radius);
    padding: 20px;
    height: fit-content;
    position: sticky;
    top: 100px;
    box-shadow: var(--shadow);
}

.content {
    flex: 1;
    min-width: 0;
}

/* Accordion mejorado */
.accordion-item {
    margin-bottom: 10px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--border);
}

.accordion-item input {
    display: none;
}

.accordion-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    cursor: pointer;
    font-weight: 700;
    color: var(--dark);
    transition: var(--transition);
}

.accordion-title:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.accordion-title::after {
    content: '▶';
    font-size: 12px;
    transition: var(--transition);
}

.accordion-item input:checked + label.accordion-title::after {
    transform: rotate(90deg);
}

.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
}

.accordion-item input:checked ~ .accordion-content {
    max-height: 400px;
    padding: 15px;
}

.accordion-content a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: var(--primary);
    margin: 5px 0;
    border-radius: 8px;
    transition: var(--transition);
    font-weight: 600;
}

.accordion-content a:hover {
    background: rgba(67, 97, 238, 0.1);
    transform: translateX(5px);
}

/* Grid products mejorado */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

/* Product card modernizada */
.product-card {
    background: white;
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: var(--shadow);
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    border: 1px solid transparent;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary);
}

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: contain;
    border-radius: 8px;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 10px;
}

.product-code {
    color: var(--gray);
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-name {
    font-size: 16px;
    color: var(--dark);
    margin: 8px 0;
    font-weight: 700;
    line-height: 1.4;
}

.product-price {
    font-size: 24px;
    color: var(--primary);
    font-weight: 800;
    margin: 10px 0;
}

/* Quantity controls mejorados */
.controls {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 15px;
}

.qty {
    width: 80px;
    padding: 10px;
    border-radius: 8px;
    border: 2px solid var(--border);
    font-size: 16px;
    text-align: center;
    transition: var(--transition);
}

.qty:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
}

/* Botones mejorados */
.actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 20px;
    gap: 10px;
}

.btn-buy {
    background: linear-gradient(135deg, var(--success) 0%, #05c090 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    flex: 1;
    transition: var(--transition);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-buy:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(6, 214, 160, 0.3);
}

.btn-cart {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.btn-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(67, 97, 238, 0.3);
}

/* CART panel mejorado */
.cart-panel {
    width: 350px;
    background: white;
    border-radius: var(--radius);
    padding: 20px;
    height: fit-content;
    position: sticky;
    top: 100px;
    box-shadow: var(--shadow);
}

.cart-panel h3 {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--primary);
    color: var(--dark);
}

.cart-item {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    padding: 15px;
    border-bottom: 1px solid var(--border);
    transition: var(--transition);
    border-radius: 8px;
}

.cart-item:hover {
    background: rgba(67, 97, 238, 0.05);
}

.cart-item .left {
    flex: 1;
}

.cart-summary {
    margin-top: 15px;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    font-weight: 700;
    display: flex;
    justify-content: space-between;
}

.cart-summary.total {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    font-size: 18px;
}

.finalize {
    margin-top: 20px;
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    font-size: 16px;
    transition: var(--transition);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    display: block;
    text-align: center;
}

.finalize:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
}

/* FORMULARIO DE FACTURACIÓN */
.invoice-form-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    padding: 20px;
}

.invoice-form-container {
    background: white;
    border-radius: var(--radius);
    padding: 30px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-hover);
}

.invoice-form h3 {
    color: var(--primary);
    margin-bottom: 20px;
    text-align: center;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark);
}

.form-group label.required::after {
    content: " *";
    color: var(--danger);
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 16px;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
}

.form-row {
    display: flex;
    gap: 15px;
}

.form-row .form-group {
    flex: 1;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-submit {
    flex: 1;
    background: linear-gradient(135deg, var(--success) 0%, #05c090 100%);
    color: white;
    border: none;
    padding: 15px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: var(--transition);
}

.btn-cancel {
    flex: 1;
    background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
    color: white;
    border: none;
    padding: 15px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: var(--transition);
}

.btn-submit:hover, .btn-cancel:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

/* FACTURA GENERADA */
.invoice-display-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 3000;
    padding: 20px;
}

.invoice-display-container {
    background: white;
    border-radius: var(--radius);
    width: 100%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
}

.invoice-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    padding: 20px;
    background: var(--light);
    border-top: 1px solid var(--border);
}

.btn-print, .btn-download, .btn-close {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.btn-print {
    background: var(--primary);
    color: white;
}

.btn-download {
    background: var(--success);
    color: white;
}

.btn-close {
    background: var(--danger);
    color: white;
}

.btn-print:hover, .btn-download:hover, .btn-close:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

/* Mensajes */
.message {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    animation: slideIn 0.3s ease;
}

.message.success {
    background: rgba(6, 214, 160, 0.1);
    border-left: 4px solid var(--success);
    color: #047857;
}

.message.error {
    background: rgba(239, 71, 111, 0.1);
    border-left: 4px solid var(--danger);
    color: #b91c1c;
}

.message.info {
    background: rgba(67, 97, 238, 0.1);
    border-left: 4px solid var(--primary);
    color: var(--primary);
}

/* Modal de búsqueda */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    padding: 20px;
}

.modal-container {
    background: white;
    border-radius: var(--radius);
    padding: 30px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-hover);
}

.search-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.search-result-card {
    background: white;
    border-radius: var(--radius);
    padding: 15px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
}

.search-result-img {
    width: 100%;
    height: 120px;
    object-fit: contain;
    border-radius: 6px;
    margin-bottom: 10px;
    background: var(--light);
    padding: 5px;
}

/* Responsive */
@media (max-width: 1200px) {
    .main {
        flex-direction: column;
        margin: 100px 15px 20px;
    }
    
    .sidebar,
    .cart-panel {
        width: 100%;
        position: static;
    }
    
    .sidebar {
        order: 1;
    }
    
    .content {
        order: 2;
    }
    
    .cart-panel {
        order: 3;
    }
}

@media (max-width: 768px) {
    .site-header {
        padding: 0 15px;
        height: auto;
        flex-direction: column;
        gap: 15px;
        padding: 15px;
    }
    
    .header-left {
        flex-direction: column;
        gap: 15px;
        width: 100%;
    }
    
    .header-actions {
        flex-wrap: wrap;
        justify-content: center;
        width: 100%;
    }
    
    .main {
        margin: 150px 10px 20px;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}

@media (max-width: 480px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .btn-buy,
    .btn-cart {
        width: 100%;
    }
}

/* Animaciones */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.product-card,
.cart-item,
.accordion-content a {
    animation: fadeIn 0.5s ease;
}
</style>
</head>
<body>

<header class="site-header">
  <div class="brand">La Ghetto Papelería</div>
  
  <div class="header-left">
    <a 
      href="https://www.google.com/maps/search/?api=1&query=Calle+Roque+Ortega%2C+L%C3%B3pez+Mateos%2C+Lote+I+Esquina%2C+Ejidal+Emiliano+Zapata%2C+55024+Ecatepec+de+Morelos%2C+M%C3%A9x."
      target="_blank"
      class="location">
      <img src="https://cdn-icons-png.flaticon.com/512/2838/2838912.png" alt="Ubicación" style="width:20px; height:20px;">
      Ver ubicación
    </a>
    
    <div style="display: flex; align-items: center; gap: 10px;">
      <?php
        $countItems = array_sum($_SESSION['cart']);
        echo "<div style='font-weight:700;color:white'>🛒 " . ($countItems ?: 0) . " items</div>";
      ?>
    </div>
  </div>
  
  <div class="header-actions">
    <!-- Botón para registrar ventas -->
    <form method="post" style="display: inline;">
      <input type="hidden" name="action" value="register_sales">
      <button type="submit" class="action-btn btn-register" title="Registrar ventas del día">
        📊 Registrar Ventas
      </button>
    </form>
    
    <!-- Botón para actualizar carrito -->
    <a href="?update_cart=1" class="action-btn btn-update" title="Actualizar carrito">
      🔄 Actualizar
    </a>
    
    <!-- Botón para buscar en inventario -->
    <button type="button" onclick="showSearchModal()" class="action-btn btn-search">
      🔍 Buscar Artículo
    </button>
  </div>
</header>

<!-- Modal de búsqueda -->
<div id="searchModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0; color:var(--primary);">🔍 Buscar en inventario</h3>
            <button type="button" onclick="hideSearchModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:var(--gray);">&times;</button>
        </div>
        
        <form method="post">
            <input type="hidden" name="action" value="search_inventory">
            <div class="form-group">
                <label>Buscar producto por nombre, código o categoría:</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="search_term" class="form-control" 
                           placeholder="Ej: Pinceles, A1001, Arte..." 
                           value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>">
                    <button type="submit" class="btn-submit" style="width:auto; padding: 0 30px;">Buscar</button>
                </div>
            </div>
        </form>
        
        <?php if ($search_performed): ?>
            <div class="search-results">
                <h4 style="margin:20px 0 10px 0; color:var(--dark);">
                    Resultados de búsqueda 
                    <?php if (!empty($search_result)): ?>
                        (<?php echo count($search_result); ?> encontrados)
                    <?php endif; ?>
                </h4>
                
                <?php if (empty($search_result)): ?>
                    <div class="message info">
                        No se encontraron productos para "<?php echo htmlspecialchars($_POST['search_term'] ?? ''); ?>"
                    </div>
                <?php else: ?>
                    <div class="search-results-grid">
                        <?php foreach ($search_result as $product): ?>
                            <div class="search-result-card">
                                <img src="<?= htmlspecialchars($product['img']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="search-result-img">
                                <div style="font-weight:700; font-size:14px; margin-bottom:5px;">
                                    <?= htmlspecialchars($product['name']) ?>
                                </div>
                                <div style="color:var(--gray); font-size:12px; margin-bottom:5px;">
                                    Código: <?= htmlspecialchars($product['code']) ?>
                                </div>
                                <div style="color:var(--gray); font-size:12px; margin-bottom:5px;">
                                    Categoría: <?= htmlspecialchars($product['category']) ?>
                                </div>
                                <div style="color:var(--primary); font-weight:700; font-size:16px;">
                                    $ <?= number_format($product['price'], 2) ?>
                                </div>
                                <form method="post" style="margin-top:10px;">
                                    <input type="hidden" name="action" value="add_to_cart">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="current_cat" value="<?= htmlspecialchars($categoriaSeleccionada) ?>">
                                    <button type="submit" class="btn-cart" style="width:100%; padding:8px; font-size:12px;">
                                        🛒 Añadir al carrito
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div style="margin-top:20px; text-align:center;">
            <button type="button" onclick="hideSearchModal()" class="btn-cancel" style="width:auto; padding:10px 30px;">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de formulario de facturación -->
<?php if ($showInvoiceForm): ?>
<div class="invoice-form-overlay">
    <div class="invoice-form-container">
        <form method="post" class="invoice-form">
            <h3>📄 DATOS PARA FACTURACIÓN</h3>
            
            <?php if ($isImmediatePurchase && isset($_SESSION['immediate_purchase'])): ?>
                <div class="message info">
                    <p><strong>Compra inmediata:</strong> <?php echo $_SESSION['immediate_purchase']['product']['name']; ?></p>
                    <p><strong>Cantidad:</strong> <?php echo $_SESSION['immediate_purchase']['quantity']; ?></p>
                    <p><strong>Total:</strong> $<?php echo number_format($_SESSION['immediate_purchase']['total'], 2); ?></p>
                </div>
            <?php elseif (!empty($_SESSION['cart'])): ?>
                <div class="message info">
                    <p><strong>Carrito:</strong> <?php echo array_sum($_SESSION['cart']); ?> productos</p>
                </div>
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control" required 
                           value="<?php echo $_SESSION['invoice_data']['nombre'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label class="required">Email</label>
                    <input type="email" name="email" class="form-control" required
                           value="<?php echo $_SESSION['invoice_data']['email'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="required">RFC</label>
                    <input type="text" name="rfc" class="form-control" required 
                           placeholder="ABCD123456XYZ"
                           value="<?php echo $_SESSION['invoice_data']['rfc'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" name="telefono" class="form-control"
                           value="<?php echo $_SESSION['invoice_data']['telefono'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Razón Social (opcional)</label>
                <input type="text" name="razon_social" class="form-control"
                       value="<?php echo $_SESSION['invoice_data']['razon_social'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label class="required">Dirección fiscal</label>
                <textarea name="direccion" class="form-control" rows="3" required><?php echo $_SESSION['invoice_data']['direccion'] ?? ''; ?></textarea>
            </div>
            
            <div class="form-actions">
                <input type="hidden" name="action" value="process_invoice">
                <button type="submit" class="btn-submit">✅ Generar Factura y Completar Compra</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='?'">❌ Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modal para mostrar factura generada -->
<?php if ($showInvoice): ?>
<div class="invoice-display-overlay">
    <div class="invoice-display-container">
        <?php echo $invoiceContent; ?>
        
        <div class="invoice-actions">
            <button class="btn-print" onclick="window.print()">🖨️ Imprimir Factura</button>
            <button class="btn-download" onclick="downloadInvoice()">📥 Descargar PDF</button>
            <button class="btn-close" onclick="window.location.href='?'">❌ Cerrar</button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="main">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <h3 style="margin:0 0 20px 0; color:var(--primary);">Secciones</h3>

    <!-- ARTE -->
    <div class="accordion-item">
      <input type="checkbox" id="s-arte">
      <label class="accordion-title" for="s-arte">Arte</label>
      <div class="accordion-content">
        <a href="?categoria=Arte">Ver productos</a>
        <a href="?categoria=Arte">Pinceles</a>
        <a href="?categoria=Arte">Acuarelas</a>
        <a href="?categoria=Arte">Lienzos</a>
        <a href="?categoria=Arte">Acrílicos</a>
      </div>
    </div>

    <!-- ESCOLAR -->
    <div class="accordion-item">
      <input type="checkbox" id="s-escolar">
      <label class="accordion-title" for="s-escolar">Escolar</label>
      <div class="accordion-content">
        <a href="?categoria=Escolar">Ver productos</a>
        <a href="?categoria=Escolar">Cuadernos</a>
        <a href="?categoria=Escolar">Colores</a>
        <a href="?categoria=Escolar">Plumas</a>
        <a href="?categoria=Escolar">Mochilas</a>
      </div>
    </div>

    <!-- URBANO -->
    <div class="accordion-item">
      <input type="checkbox" id="s-urbano">
      <label class="accordion-title" for="s-urbano">Urbano</label>
      <div class="accordion-content">
        <a href="?categoria=Urbano">Ver productos</a>
        <a href="?categoria=Urbano">Rotuladores</a>
        <a href="?categoria=Urbano">Sprays</a>
        <a href="?categoria=Urbano">Stickers</a>
        <a href="?categoria=Urbano">Libretas</a>
      </div>
    </div>

    <!-- PAPELES -->
    <div class="accordion-item">
      <input type="checkbox" id="s-papeles">
      <label class="accordion-title" for="s-papeles">Papeles</label>
      <div class="accordion-content">
        <a href="?categoria=Papeles">Ver productos</a>
        <a href="?categoria=Papeles">Foamy</a>
        <a href="?categoria=Papeles">Cartulinas</a>
        <a href="?categoria=Papeles">Bond</a>
        <a href="?categoria=Papeles">Papel fotográfico</a>
      </div>
    </div>
  </aside>

  <!-- CONTENT -->
  <section class="content">
    <?php if (!empty($message)): ?>
      <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="toolbar">
      <div>
        <?php
          if ($categoriaSeleccionada) {
              echo "<h2 style='margin:0 0 12px 0; color:var(--dark);'>Categoría: ".htmlspecialchars($categoriaSeleccionada)."</h2>";
          } else {
              echo "<h2 style='margin:0 0 12px 0; color:var(--dark);'>Selecciona una categoría</h2>";
          }
        ?>
      </div>
      <div style="color:var(--primary); font-weight:700; font-size:18px;"><?= count($productosFiltrados) ?> productos</div>
    </div>

    <div class="products-grid">
      <?php
        if (!$categoriaSeleccionada) {
            echo '<div style="grid-column:1/-1; text-align:center; padding:60px 20px; background:white; border-radius:var(--radius); box-shadow:var(--shadow);">';
            echo '<div style="font-size:72px; margin-bottom:20px;">🎨</div>';
            echo '<h3 style="color:var(--dark); margin-bottom:10px;">Bienvenido a La Ghetto Papelería</h3>';
            echo '<p style="color:var(--gray);">Selecciona una categoría para ver nuestros productos</p>';
            echo '</div>';
        } else {
            if (empty($productosFiltrados)) {
                echo '<div style="grid-column:1/-1; text-align:center; padding:60px 20px; background:white; border-radius:var(--radius); box-shadow:var(--shadow);">';
                echo '<div style="font-size:72px; margin-bottom:20px;">📦</div>';
                echo '<h3 style="color:var(--dark); margin-bottom:10px;">Sin productos</h3>';
                echo '<p style="color:var(--gray);">No hay productos en esta categoría actualmente</p>';
                echo '</div>';
            } else {
                foreach ($productosFiltrados as $p) {
                    ?>
                    <article class="product-card">
                      <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                      <div class="product-code"><?= htmlspecialchars($p['code']) ?></div>
                      <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                      <div class="product-price">$ <?= number_format($p['price'],2) ?></div>

                      <!-- Form para añadir al carrito O compra inmediata -->
                      <form method="post" style="margin-top:10px; display:flex; flex-direction:column;">
                        <div class="controls">
                          <label style="font-size:14px;color:var(--gray); font-weight:600;">Cantidad:</label>
                          <input class="qty" type="number" name="quantity" value="1" min="1">
                        </div>

                        <div class="actions">
                          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                          <input type="hidden" name="current_cat" value="<?= htmlspecialchars($categoriaSeleccionada) ?>">
                          
                          <!-- Botón para COMPRA INMEDIATA -->
                          <button class="btn-buy" type="submit" name="action" value="buy_now">
                            ⚡ Comprar ahora
                          </button>
                          
                          <!-- Botón para AÑADIR AL CARRITO -->
                          <button class="btn-cart" type="submit" name="action" value="add_to_cart">
                            🛒 Añadir al carrito
                          </button>
                        </div>
                      </form>
                    </article>
                    <?php
                }
            }
        }
      ?>
    </div>
  </section>

  <!-- CART -->
  <aside class="cart-panel">
    <h3>🛒 Carrito de compras</h3>

    <?php
      if (empty($_SESSION['cart'])) {
          echo '<div style="text-align:center; padding:40px 20px; color:var(--gray);">';
          echo '<div style="font-size:48px; margin-bottom:15px;">🛒</div>';
          echo '<p style="font-weight:600; margin-bottom:10px;">Tu carrito está vacío</p>';
          echo '<p style="font-size:14px;">Agrega productos desde el catálogo</p>';
          echo '</div>';
      } else {
          $subtotal = 0.0;
          foreach ($_SESSION['cart'] as $pid => $qty) {
              $prod = findProduct($pid, $productos);
              if (!$prod) continue;
              $line = $prod['price'] * $qty;
              $subtotal += $line;
              ?>
              <div class="cart-item">
                <div class="left">
                  <div style="font-weight:700; color:var(--dark); margin-bottom:5px;"><?= htmlspecialchars($prod['name']) ?></div>
                  <div style="font-size:14px;color:var(--gray); margin-bottom:3px;">Código: <?= htmlspecialchars($prod['code']) ?></div>
                  <div style="font-size:14px;color:var(--gray);">$<?= number_format($prod['price'],2) ?> x <?= intval($qty) ?> unidades</div>
                </div>
                <div style="text-align:right; min-width:100px;">
                  <div style="font-weight:700; color:var(--primary); font-size:18px;">$ <?= number_format($line,2) ?></div>
                  <div style="margin-top:8px;">
                    <a href="?action=remove&id=<?= $prod['id'] ?>&<?= $categoriaSeleccionada ? 'categoria='.urlencode($categoriaSeleccionada) : '' ?>" 
                       style="color:var(--danger); text-decoration:none; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:5px;"
                       onmouseover="this.style.color='#dc2626'"
                       onmouseout="this.style.color='var(--danger)'">
                      🗑️ Eliminar
                    </a>
                  </div>
                </div>
              </div>
              <?php
          }
          $iva = $subtotal * 0.16;
          $total = $subtotal + $iva;
          ?>
          <div class="cart-summary">Subtotal: <span>$ <?= number_format($subtotal,2) ?></span></div>
          <div class="cart-summary">IVA (16%): <span>$ <?= number_format($iva,2) ?></span></div>
          <div class="cart-summary total">Total: <span>$ <?= number_format($total,2) ?></span></div>

          <!-- Botón para finalizar compra (abre formulario de factura) -->
          <a href="?show_invoice_form=1" class="finalize">
            💳 Finalizar compra y facturar
          </a>
          <?php
      }
    ?>
  </aside>
</div>

<script>
// Funciones para el modal de búsqueda
function showSearchModal() {
    document.getElementById('searchModal').style.display = 'flex';
}

function hideSearchModal() {
    document.getElementById('searchModal').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('searchModal');
    if (event.target == modal) {
        hideSearchModal();
    }
}

// Función para "descargar" factura (simulada)
function downloadInvoice() {
    alert('En una implementación real, aquí se generaría un PDF.\nPara producción, considera usar librerías como:\n- TCPDF\n- Dompdf\n- mPDF');
}

// Función para actualizar carrito
if (window.location.search.includes('update_cart=1')) {
    window.location.href = window.location.pathname + '?<?= $categoriaSeleccionada ? 'categoria='.urlencode($categoriaSeleccionada) : '' ?>';
}
</script>
</body>
</html>