<?php
/**
 * 商品详情页
 * Hello Kitty 玩具商城
 */
require_once __DIR__ . '/includes/functions.php';

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = getProduct($productId);

if (!$product) {
    setFlash('error', '商品不存在');
    redirect('products.php');
}

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';

// 获取同分类推荐商品
$db = Database::getInstance();
$relatedProducts = $db->fetchAll(
    "SELECT p.*, c.name as category_name FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.category_id = ? AND p.id != ?
     ORDER BY RAND() LIMIT 4",
    [$product['category_id'], $product['id']]
);
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- 面包屑导航 -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="index.php" class="text-gray-500 hover:text-kitty-pink-500 transition-colors">首页</a>
            </li>
            <li class="text-gray-400">/</li>
            <li>
                <a href="products.php" class="text-gray-500 hover:text-kitty-pink-500 transition-colors">全部商品</a>
            </li>
            <?php if ($product['category_name']): ?>
                <li class="text-gray-400">/</li>
                <li>
                    <a href="products.php?category=<?= $product['category_id'] ?>"
                        class="text-gray-500 hover:text-kitty-pink-500 transition-colors">
                        <?= e($product['category_name']) ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="text-gray-400">/</li>
            <li class="text-kitty-pink-600 font-medium truncate max-w-[200px]"><?= e($product['name']) ?></li>
        </ol>
    </nav>

    <!-- 商品详情 -->
    <div class="kitty-card rounded-3xl overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            <!-- 商品图片 -->
            <div
                class="lg:w-1/2 p-8 bg-gradient-to-br from-kitty-pink-50 to-kitty-pink-100 flex items-center justify-center">
                <div class="relative">
                    <div
                        class="w-64 h-64 md:w-80 md:h-80 bg-white/50 rounded-3xl flex items-center justify-center shadow-xl overflow-hidden">
                        <?php if ($img = getProductImage($product['name'])): ?>
                            <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>"
                                class="w-full h-full object-cover mix-blend-multiply">
                        <?php else: ?>
                            <span class="text-[100px] md:text-[140px]">
                                <?= getProductEmoji($product['name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['is_hot']): ?>
                        <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            🔥 热卖
                        </div>
                    <?php endif; ?>
                    <?php if ($product['is_new']): ?>
                        <div
                            class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            ✨ 新品
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 商品信息 -->
            <div class="lg:w-1/2 p-8">
                <div class="mb-4">
                    <span
                        class="inline-block bg-kitty-pink-100 text-kitty-pink-600 px-3 py-1 rounded-full text-sm font-medium">
                        <?= e($product['category_name'] ?? '未分类') ?>
                    </span>
                </div>

                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                    <?= e($product['name']) ?>
                </h1>

                <div class="flex items-baseline gap-4 mb-6">
                    <span class="text-3xl md:text-4xl font-bold text-kitty-pink-600">
                        <?= formatPrice($product['price']) ?>
                    </span>
                </div>

                <p class="text-gray-600 mb-6 leading-relaxed">
                    <?= e($product['description']) ?>
                </p>

                <div class="flex items-center gap-4 mb-6 text-sm text-gray-500">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        库存: <?= $product['stock'] ?> 件
                    </span>
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-1 text-kitty-pink-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        48小时内发货
                    </span>
                </div>

                <!-- 数量选择 -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">购买数量</label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border-2 border-kitty-pink-200 rounded-xl overflow-hidden">
                            <button type="button" onclick="changeQuantity(-1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-kitty-pink-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>"
                                class="w-16 h-10 text-center border-x-2 border-kitty-pink-200 focus:outline-none">
                            <button type="button" onclick="changeQuantity(1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-kitty-pink-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 操作按钮 -->
                <div class="flex gap-4">
                    <button onclick="addToCartWithQuantity()"
                        class="flex-1 kitty-btn text-white py-3 rounded-xl font-bold text-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        加入购物车
                    </button>
                </div>

                <!-- 服务保障 -->
                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="mr-2">✅</span>正品保证
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="mr-2">🚚</span>满99包邮
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="mr-2">🔄</span>7天无理由退换
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="mr-2">💬</span>在线客服
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 推荐商品 -->
    <?php if (!empty($relatedProducts)): ?>
        <section class="mt-12">
            <h2 class="text-xl font-bold text-gray-800 mb-6">
                <span class="text-kitty-pink-500">💕</span> 相关推荐
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <?php foreach ($relatedProducts as $related): ?>
                    <div class="kitty-card rounded-2xl overflow-hidden">
                        <a href="product.php?id=<?= $related['id'] ?>" class="block">
                            <div
                                class="aspect-square bg-gradient-to-br from-kitty-pink-50 to-kitty-pink-100 flex items-center justify-center overflow-hidden">
                                <?php if ($img = getProductImage($related['name'])): ?>
                                    <img src="<?= e($img) ?>" alt="<?= e($related['name']) ?>"
                                        class="w-full h-full object-cover mix-blend-multiply hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <span class="text-5xl">
                                        <?= getProductEmoji($related['name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3
                                    class="font-bold text-gray-800 mb-1 truncate hover:text-kitty-pink-600 transition-colors text-sm">
                                    <?= e($related['name']) ?>
                                </h3>
                                <span class="text-kitty-pink-600 font-bold">
                                    <?= formatPrice($related['price']) ?>
                                </span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
    const maxStock = <?= $product['stock'] ?>;

    function changeQuantity(delta) {
        const input = document.getElementById('quantity');
        let value = parseInt(input.value) + delta;
        value = Math.max(1, Math.min(maxStock, value));
        input.value = value;
    }

    function addToCartWithQuantity() {
        const quantity = parseInt(document.getElementById('quantity').value);
        addToCart(<?= $product['id'] ?>, quantity);
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>