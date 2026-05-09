<?php
/**
 * 购物车 API
 * Hello Kitty 玩具商城
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

// 获取请求数据
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// 检查登录状态
if (!isLoggedIn()) {
    jsonResponse([
        'success' => false,
        'message' => '请先登录',
        'redirect' => 'login.php'
    ], 401);
}

$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'add':
            $productId = (int)($input['product_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 1);

            if ($productId <= 0) {
                jsonResponse(['success' => false, 'message' => '无效的商品ID']);
            }

            $result = addToCart($userId, $productId, $quantity);
            $result['cart_count'] = getCartCount($userId);
            jsonResponse($result);
            break;

        case 'update':
            $cartId = (int)($input['cart_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 0);

            if ($cartId <= 0) {
                jsonResponse(['success' => false, 'message' => '无效的购物车ID']);
            }

            $result = updateCartQuantity($userId, $cartId, $quantity);

            // 计算新的总价和数量
            $cartItems = getCartItems($userId);
            $totalPrice = 0;
            $subtotal = 0;

            foreach ($cartItems as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
                if ($item['id'] == $cartId) {
                    $subtotal = $item['price'] * $quantity;
                }
            }

            $result['subtotal'] = formatPrice($subtotal);
            $result['total_items'] = count($cartItems);
            $result['total_price'] = $totalPrice;
            $result['total_price_formatted'] = formatPrice($totalPrice);
            $result['cart_count'] = getCartCount($userId);

            jsonResponse($result);
            break;

        case 'remove':
            $cartId = (int)($input['cart_id'] ?? 0);

            if ($cartId <= 0) {
                jsonResponse(['success' => false, 'message' => '无效的购物车ID']);
            }

            $result = removeFromCart($userId, $cartId);

            // 重新计算
            $cartItems = getCartItems($userId);
            $totalPrice = 0;

            foreach ($cartItems as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            $result['total_items'] = count($cartItems);
            $result['total_price'] = $totalPrice;
            $result['total_price_formatted'] = formatPrice($totalPrice);
            $result['cart_count'] = getCartCount($userId);

            jsonResponse($result);
            break;

        case 'get':
            $cartItems = getCartItems($userId);
            $totalPrice = 0;

            foreach ($cartItems as &$item) {
                $item['subtotal'] = $item['price'] * $item['quantity'];
                $item['subtotal_formatted'] = formatPrice($item['subtotal']);
                $item['price_formatted'] = formatPrice($item['price']);
                $totalPrice += $item['subtotal'];
            }

            jsonResponse([
                'success' => true,
                'items' => $cartItems,
                'total_items' => count($cartItems),
                'total_price' => $totalPrice,
                'total_price_formatted' => formatPrice($totalPrice)
            ]);
            break;

        default:
            jsonResponse(['success' => false, 'message' => '无效的操作'], 400);
    }
} catch (Exception $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] [ERROR] Cart API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => '操作失败，请稍后再试'], 500);
}
