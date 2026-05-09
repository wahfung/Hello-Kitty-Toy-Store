<?php
/**
 * 商品列表页
 * Hello Kitty 玩具商城
 */
$pageTitle = '全部商品';
require_once __DIR__ . '/includes/header.php';

$categories = getCategories();
$currentCategory = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$filter = $_GET['filter'] ?? '';

// 获取商品
$db = Database::getInstance();
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($currentCategory > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $currentCategory;
}

if ($filter === 'hot') {
    $sql .= " AND p.is_hot = 1";
} elseif ($filter === 'new') {
    $sql .= " AND p.is_new = 1";
}

$sql .= " ORDER BY p.is_hot DESC, p.is_new DESC, p.created_at DESC";
$products = $db->fetchAll($sql, $params);

// 获取当前分类名称
$currentCategoryName = '全部商品';
if ($currentCategory > 0) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $currentCategory) {
            $currentCategoryName = $cat['name'];
            break;
        }
    }
}
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- 面包屑导航 -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="index.php" class="text-gray-500 hover:text-kitty-pink-500 transition-colors">首页</a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-kitty-pink-600 font-medium"><?= e($currentCategoryName) ?></li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- 侧边栏 - 分类筛选 -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="kitty-card rounded-2xl p-6 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <span class="mr-2">🏷️</span>商品分类
                </h3>
                <ul class="space-y-2">
                    <li>
                        <a href="products.php"
                            class="block px-4 py-2 rounded-xl transition-colors <?= $currentCategory === 0 && !$filter ? 'bg-kitty-pink-100 text-kitty-pink-600 font-medium' : 'text-gray-600 hover:bg-kitty-pink-50' ?>">
                            全部商品
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="products.php?category=<?= $category['id'] ?>"
                                class="block px-4 py-2 rounded-xl transition-colors <?= $currentCategory === (int) $category['id'] ? 'bg-kitty-pink-100 text-kitty-pink-600 font-medium' : 'text-gray-600 hover:bg-kitty-pink-50' ?>">
                                <?= $category['icon'] ?>     <?= e($category['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <hr class="my-4 border-kitty-pink-100">

                <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                    <span class="mr-2">✨</span>快速筛选
                </h3>
                <ul class="space-y-2">
                    <li>
                        <a href="products.php?filter=hot"
                            class="block px-4 py-2 rounded-xl transition-colors <?= $filter === 'hot' ? 'bg-kitty-pink-100 text-kitty-pink-600 font-medium' : 'text-gray-600 hover:bg-kitty-pink-50' ?>">
                            🔥 热门商品
                        </a>
                    </li>
                    <li>
                        <a href="products.php?filter=new"
                            class="block px-4 py-2 rounded-xl transition-colors <?= $filter === 'new' ? 'bg-kitty-pink-100 text-kitty-pink-600 font-medium' : 'text-gray-600 hover:bg-kitty-pink-50' ?>">
                            🆕 新品上架
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- 商品列表 -->
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    <?php if ($filter === 'hot'): ?>
                        🔥 热门商品
                    <?php elseif ($filter === 'new'): ?>
                        ✨ 新品上架
                    <?php else: ?>
                        <?= e($currentCategoryName) ?>
                    <?php endif; ?>
                </h1>
                <span class="text-gray-500 text-sm">共 <?= count($products) ?> 件商品</span>
            </div>

            <?php if (empty($products)): ?>
                <div class="kitty-card rounded-2xl p-12 text-center">
                    <div class="text-6xl mb-4">😿</div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">暂无商品</h3>
                    <p class="text-gray-500">该分类下还没有商品，请查看其他分类</p>
                    <a href="products.php"
                        class="kitty-btn inline-block mt-4 text-white px-6 py-2 rounded-full font-medium">
                        查看全部商品
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="kitty-card rounded-2xl overflow-hidden relative">
                            <?php if ($product['is_hot']): ?>
                                <div class="ribbon">热卖</div>
                            <?php elseif ($product['is_new']): ?>
                                <div class="ribbon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">新品</div>
                            <?php endif; ?>
                            <a href="product.php?id=<?= $product['id'] ?>" class="block">
                                <div
                                    class="aspect-square bg-gradient-to-br from-kitty-pink-50 to-kitty-pink-100 flex items-center justify-center overflow-hidden">
                                    <?php if ($img = getProductImage($product['name'])): ?>
                                        <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>"
                                            class="w-full h-full object-cover mix-blend-multiply hover:scale-105 transition-transform duration-300">
                                    <?php else: ?>
                                        <span class="text-6xl md:text-7xl">
                                            <?= getProductEmoji($product['name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-4">
                                    <h3
                                        class="font-bold text-gray-800 mb-1 truncate hover:text-kitty-pink-600 transition-colors">
                                        <?= e($product['name']) ?>
                                    </h3>
                                    <p class="text-xs text-gray-500 mb-2"><?= e($product['category_name'] ?? '未分类') ?></p>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2 h-10">
                                        <?= e(mb_substr($product['description'], 0, 40)) ?>...
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-kitty-pink-600 font-bold text-lg">
                                            <?= formatPrice($product['price']) ?>
                                        </span>
                                        <button onclick="event.preventDefault(); addToCart(<?= $product['id'] ?>)"
                                            class="w-8 h-8 bg-kitty-pink-100 hover:bg-kitty-pink-500 text-kitty-pink-500 hover:text-white rounded-full flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-400">
                                        库存: <?= $product['stock'] ?> 件
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>