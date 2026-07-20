<?php
// Load koneksi database
require_once __DIR__ . '/../config/database.php';

// Header type
header('Content-Type: application/json');

// URL path yang diakses
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Method request (GET, POST, dll)
$method = $_SERVER['REQUEST_METHOD'];

// Routing standar
if ($uri === '/' && $method === 'GET') {
    
    echo json_encode([
        "status" => "success",
        "message" => "API Ready! Server is ok."
    ]);

} elseif ($uri === '/api/flashsale' && $method === 'POST') {

    // payload
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $input['user_id'] ?? null;
    $productId = $input['product_id'] ?? null;

    // validation
    if (!$userId || !$productId) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "user_id dan product_id wajib diisi"]);
        exit;
    }

    try {
        // Transaction
        $pdo->beginTransaction();

        // Pessimistic Locking
        // Request lain yang mau beli produk ini akan dipaksa ngantri sampai transaksi ini selesai.
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch();

        // Cek produk di database
        if (!$product) {
            $pdo->rollBack();
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Produk tidak ditemukan"]);
            exit;
        }

        // Cek stok yg tersedia
        if ($product['stock'] <= 0) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Maaf, barang sudah habis terjual!"]);
            exit;
        }

        // Kurangi stok barang (-1)
        $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = :id");
        $updateStmt->execute(['id' => $productId]);

        // Catat riwayat pesanan ke tabel orders
        $orderStmt = $pdo->prepare("INSERT INTO orders (product_id, user_id) VALUES (:product_id, :user_id)");
        $orderStmt->execute([
            'product_id' => $productId,
            'user_id' => $userId
        ]);

        // commit ke database
        $pdo->commit();

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Flash sale berhasil! Pesanan diproses."
        ]);

    } catch (Exception $e) {
        // throw exception
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Terjadi kesalahan sistem: " . $e->getMessage()
        ]);
    }

} else {
    
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Page not found."
    ], 404);

}