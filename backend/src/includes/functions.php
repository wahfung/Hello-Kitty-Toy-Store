<?php
/**
 * 公共函数库
 * Hello Kitty 玩具商城
 */

// 启动 Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/**
 * 安全输出 HTML
 */
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * 检查用户是否登录
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * 获取当前登录用户信息
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    $db = Database::getInstance();
    return $db->fetchOne("SELECT id, username, email, avatar, created_at FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * 用户登录验证
 */
function loginUser(string $username, string $password): array
{
    $db = Database::getInstance();

    // 输入验证
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => '用户名和密码不能为空'];
    }

    if (strlen($username) < 2 || strlen($username) > 50) {
        return ['success' => false, 'message' => '用户名长度应在2-50个字符之间'];
    }

    $user = $db->fetchOne("SELECT * FROM users WHERE username = ?", [$username]);

    if (!$user) {
        error_log("[" . date('Y-m-d H:i:s') . "] [WARN] Login attempt failed: User not found - {$username}");
        return ['success' => false, 'message' => '用户名或密码错误'];
    }

    if (!password_verify($password, $user['password'])) {
        error_log("[" . date('Y-m-d H:i:s') . "] [WARN] Login attempt failed: Wrong password - {$username}");
        return ['success' => false, 'message' => '用户名或密码错误'];
    }

    // 登录成功
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    error_log("[" . date('Y-m-d H:i:s') . "] [INFO] User logged in: {$username}");

    return ['success' => true, 'message' => '登录成功', 'user' => $user];
}

/**
 * 用户注册
 */
function registerUser(string $username, string $password, string $email = ''): array
{
    $db = Database::getInstance();

    // 输入验证
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => '用户名和密码不能为空'];
    }

    if (strlen($username) < 2 || strlen($username) > 50) {
        return ['success' => false, 'message' => '用户名长度应在2-50个字符之间'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'message' => '密码长度至少6个字符'];
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => '邮箱格式不正确'];
    }

    // 检查用户名是否已存在
    $existing = $db->fetchOne("SELECT id FROM users WHERE username = ?", [$username]);
    if ($existing) {
        return ['success' => false, 'message' => '用户名已被使用'];
    }

    // 加密密码并创建用户
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $db->execute(
            "INSERT INTO users (username, password, email) VALUES (?, ?, ?)",
            [$username, $hashedPassword, $email]
        );

        error_log("[" . date('Y-m-d H:i:s') . "] [INFO] New user registered: {$username}");

        return ['success' => true, 'message' => '注册成功，请登录'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => '注册失败，请稍后再试'];
    }
}

/**
 * 用户登出
 */
function logoutUser(): void
{
    $username = $_SESSION['username'] ?? 'Unknown';
    session_destroy();
    error_log("[" . date('Y-m-d H:i:s') . "] [INFO] User logged out: {$username}");
}

/**
 * 获取所有商品分类
 */
function getCategories(): array
{
    $db = Database::getInstance();
    return $db->fetchAll("SELECT * FROM categories ORDER BY id");
}

/**
 * 获取商品列表
 */
function getProducts(int $categoryId = 0, int $limit = 20, int $offset = 0): array
{
    $db = Database::getInstance();

    $sql = "SELECT p.*, c.name as category_name FROM products p
            LEFT JOIN categories c ON p.category_id = c.id";
    $params = [];

    if ($categoryId > 0) {
        $sql .= " WHERE p.category_id = ?";
        $params[] = $categoryId;
    }

    $sql .= " ORDER BY p.is_hot DESC, p.is_new DESC, p.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    return $db->fetchAll($sql, $params);
}

/**
 * 获取热门商品
 */
function getHotProducts(int $limit = 8): array
{
    $db = Database::getInstance();
    return $db->fetchAll(
        "SELECT p.*, c.name as category_name FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.is_hot = 1 ORDER BY p.created_at DESC LIMIT ?",
        [$limit]
    );
}

/**
 * 获取新品
 */
function getNewProducts(int $limit = 8): array
{
    $db = Database::getInstance();
    return $db->fetchAll(
        "SELECT p.*, c.name as category_name FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.is_new = 1 ORDER BY p.created_at DESC LIMIT ?",
        [$limit]
    );
}

/**
 * 获取单个商品详情
 */
function getProduct(int $id): ?array
{
    $db = Database::getInstance();
    return $db->fetchOne(
        "SELECT p.*, c.name as category_name FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.id = ?",
        [$id]
    );
}

/**
 * 获取购物车商品
 */
function getCartItems(int $userId): array
{
    $db = Database::getInstance();
    return $db->fetchAll(
        "SELECT c.*, p.name, p.price, p.image, p.stock
         FROM cart c
         JOIN products p ON c.product_id = p.id
         WHERE c.user_id = ?",
        [$userId]
    );
}

/**
 * 获取购物车商品数量
 */
function getCartCount(int $userId): int
{
    $db = Database::getInstance();
    $result = $db->fetchOne(
        "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?",
        [$userId]
    );
    return (int) ($result['total'] ?? 0);
}

/**
 * 添加商品到购物车
 */
function addToCart(int $userId, int $productId, int $quantity = 1): array
{
    $db = Database::getInstance();

    // 检查商品是否存在
    $product = getProduct($productId);
    if (!$product) {
        return ['success' => false, 'message' => '商品不存在'];
    }

    // 检查库存
    if ($product['stock'] < $quantity) {
        return ['success' => false, 'message' => '库存不足'];
    }

    // 检查购物车是否已有该商品
    $existing = $db->fetchOne(
        "SELECT * FROM cart WHERE user_id = ? AND product_id = ?",
        [$userId, $productId]
    );

    if ($existing) {
        // 更新数量
        $newQuantity = $existing['quantity'] + $quantity;
        if ($newQuantity > $product['stock']) {
            return ['success' => false, 'message' => '超过库存数量'];
        }
        $db->execute(
            "UPDATE cart SET quantity = ? WHERE id = ?",
            [$newQuantity, $existing['id']]
        );
    } else {
        // 新增
        $db->execute(
            "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)",
            [$userId, $productId, $quantity]
        );
    }

    return ['success' => true, 'message' => '已添加到购物车'];
}

/**
 * 更新购物车商品数量
 */
function updateCartQuantity(int $userId, int $cartId, int $quantity): array
{
    $db = Database::getInstance();

    if ($quantity <= 0) {
        // 删除商品
        $db->execute("DELETE FROM cart WHERE id = ? AND user_id = ?", [$cartId, $userId]);
        return ['success' => true, 'message' => '已从购物车移除'];
    }

    $db->execute(
        "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?",
        [$quantity, $cartId, $userId]
    );

    return ['success' => true, 'message' => '已更新数量'];
}

/**
 * 从购物车移除商品
 */
function removeFromCart(int $userId, int $cartId): array
{
    $db = Database::getInstance();
    $db->execute("DELETE FROM cart WHERE id = ? AND user_id = ?", [$cartId, $userId]);
    return ['success' => true, 'message' => '已从购物车移除'];
}

/**
 * 获取留言列表
 */
function getGuestbookMessages(int $limit = 20, int $offset = 0): array
{
    $db = Database::getInstance();
    return $db->fetchAll(
        "SELECT g.*, u.username FROM guestbook g
         LEFT JOIN users u ON g.user_id = u.id
         ORDER BY g.created_at DESC LIMIT ? OFFSET ?",
        [$limit, $offset]
    );
}

/**
 * 添加留言
 */
function addGuestbookMessage(string $nickname, string $content, ?int $userId = null): array
{
    $db = Database::getInstance();

    // 输入验证
    if (empty($nickname) || empty($content)) {
        return ['success' => false, 'message' => '昵称和留言内容不能为空'];
    }

    if (strlen($nickname) > 50) {
        return ['success' => false, 'message' => '昵称长度不能超过50个字符'];
    }

    if (strlen($content) > 1000) {
        return ['success' => false, 'message' => '留言内容不能超过1000个字符'];
    }

    // 简单的敏感词过滤
    $sensitiveWords = ['垃圾', '骗子'];
    foreach ($sensitiveWords as $word) {
        if (stripos($content, $word) !== false) {
            return ['success' => false, 'message' => '留言内容包含敏感词'];
        }
    }

    try {
        $db->execute(
            "INSERT INTO guestbook (user_id, nickname, content) VALUES (?, ?, ?)",
            [$userId, $nickname, $content]
        );

        error_log("[" . date('Y-m-d H:i:s') . "] [INFO] New guestbook message from: {$nickname}");

        return ['success' => true, 'message' => '留言成功'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => '留言失败，请稍后再试'];
    }
}

/**
 * 格式化价格
 */
function formatPrice(float $price): string
{
    return '¥' . number_format($price, 2);
}

/**
 * 格式化日期
 */
function formatDate(string $date): string
{
    return date('Y-m-d H:i', strtotime($date));
}

/**
 * 生成 CSRF Token
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 验证 CSRF Token
 */
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * JSON 响应
 */
function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 重定向
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * 设置 Flash 消息
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * 获取并清除 Flash 消息
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * 根据商品名称获取对应的 Emoji 配图
 */
function getProductEmoji(string $productName): string
{
    $mapping = [
        '经典毛绒公仔' => '🧸',
        '粉色礼服公仔' => '👗',
        '毛绒兔子' => '🐰',
        '大耳狗公仔' => '🐶',
        '酷洛米公仔' => '😈',
        '笔记本套装' => '📓',
        '中性笔套装' => '🖊️',
        '文具收纳盒' => '📦',
        '便利贴套装' => '📝',
        '马克杯' => '☕',
        '保温杯' => '🥤',
        '化妆镜' => '🪞',
        '抱枕' => '🛋️',
        '发夹套装' => '🎀',
        '手提包' => '👜',
        '袜子礼盒' => '🧦',
        '发箍' => '🎀',
        '新年限定' => '🧧',
        '樱花限定' => '🌸',
        '50周年纪念版' => '🎂',
    ];

    foreach ($mapping as $keyword => $emoji) {
        if (mb_strpos($productName, $keyword) !== false) {
            return $emoji;
        }
    }

    $emojis = ['🧸', '🎁', '🎀', '💝', '🌸', '⭐'];
    $hash = hexdec(substr(md5($productName), 0, 8));
    return $emojis[$hash % count($emojis)];
}

/**
 * 根据商品名称获取对应的真实图片路径
 */
function getProductImage(string $productName): ?string
{
    // Possible extensions
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    $imagesDir = __DIR__ . '/../assets/images/';

    foreach ($extensions as $ext) {
        $filename = $productName . '.' . $ext;
        if (file_exists($imagesDir . $filename)) {
            return 'assets/images/' . rawurlencode($filename);
        }
    }

    return null;
}

