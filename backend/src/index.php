<?php
/**
 * 首页
 * Hello Kitty 玩具商城
 */
$pageTitle = '首页';
require_once __DIR__ . '/includes/header.php';

$hotProducts = getHotProducts(8);
$newProducts = getNewProducts(8);
$categories = getCategories();
?>

<!-- Hero Banner -->
<section class="relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 py-12 md:py-20">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="text-center md:text-left md:w-1/2 mb-8 md:mb-0">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 mb-4">
                    欢迎来到
                    <span class="bg-gradient-to-r from-kitty-pink-500 to-kitty-pink-600 bg-clip-text text-transparent">
                        Hello Kitty
                    </span>
                    <br>玩具商城
                </h1>
                <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto md:mx-0">
                    发现最可爱的三丽鸥家族玩具，让萌萌的 Hello Kitty 陪伴你的每一天！
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="products.php"
                        class="kitty-btn text-white px-8 py-3 rounded-full font-bold text-lg inline-flex items-center justify-center">
                        <span class="mr-2">🛍️</span>
                        开始购物
                    </a>
                    <a href="#categories"
                        class="bg-white text-kitty-pink-600 px-8 py-3 rounded-full font-bold text-lg border-2 border-kitty-pink-300 hover:border-kitty-pink-500 transition-colors inline-flex items-center justify-center">
                        <span class="mr-2">🎀</span>
                        浏览分类
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <div class="relative">
                    <div
                        class="w-64 h-64 md:w-80 md:h-80 bg-gradient-to-br from-kitty-pink-200 to-kitty-pink-300 rounded-full flex items-center justify-center shadow-2xl">
                        <span class="text-[120px] md:text-[160px]">🎀</span>
                    </div>
                    <!-- 装饰元素 -->
                    <div class="absolute -top-4 -right-4 text-4xl animate-bounce">⭐</div>
                    <div class="absolute -bottom-4 -left-4 text-4xl animate-bounce delay-100">💖</div>
                    <div class="absolute top-1/2 -right-8 text-3xl animate-pulse">✨</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 分类导航 -->
<section id="categories" class="py-12 bg-white/50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-800 mb-8">
            <span class="inline-block relative">
                🎀 商品分类
                <span
                    class="absolute -bottom-2 left-0 right-0 h-1 bg-gradient-to-r from-kitty-pink-400 to-kitty-pink-500 rounded-full"></span>
            </span>
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?= $category['id'] ?>" class="kitty-card rounded-2xl p-6 text-center group">
                    <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">
                        <?= $category['icon'] ?>
                    </div>
                    <h3 class="font-bold text-gray-700 group-hover:text-kitty-pink-600 transition-colors">
                        <?= e($category['name']) ?>
                    </h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 热门商品 -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                <span class="text-kitty-pink-500">🔥</span> 热门商品
            </h2>
            <a href="products.php?filter=hot"
                class="text-kitty-pink-500 hover:text-kitty-pink-600 font-medium flex items-center">
                查看更多
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($hotProducts as $product): ?>
                <div class="kitty-card rounded-2xl overflow-hidden relative">
                    <?php if ($product['is_hot']): ?>
                        <div class="ribbon">热卖</div>
                    <?php endif; ?>
                    <a href="product.php?id=<?= $product['id'] ?>" class="block">
                        <div
                            class="aspect-square bg-gradient-to-br from-kitty-pink-50 to-kitty-pink-100 flex items-center justify-center overflow-hidden">
                            <?php if ($img = getProductImage($product['name'])): ?>
                                <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>"
                                    class="w-full h-full object-cover mix-blend-multiply hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <span class="text-6xl md:text-7xl"><?= getProductEmoji($product['name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-800 mb-1 truncate hover:text-kitty-pink-600 transition-colors">
                                <?= e($product['name']) ?>
                            </h3>
                            <p class="text-xs text-gray-500 mb-2"><?= e($product['category_name'] ?? '未分类') ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-kitty-pink-600 font-bold text-lg">
                                    <?= formatPrice($product['price']) ?>
                                </span>
                                <button onclick="addToCart(<?= $product['id'] ?>)"
                                    class="w-8 h-8 bg-kitty-pink-100 hover:bg-kitty-pink-500 text-kitty-pink-500 hover:text-white rounded-full flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 新品上架 -->
<section class="py-12 bg-white/50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                <span class="text-kitty-pink-500">✨</span> 新品上架
            </h2>
            <a href="products.php?filter=new"
                class="text-kitty-pink-500 hover:text-kitty-pink-600 font-medium flex items-center">
                查看更多
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($newProducts as $product): ?>
                <div class="kitty-card rounded-2xl overflow-hidden relative">
                    <?php if ($product['is_new']): ?>
                        <div class="ribbon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">新品</div>
                    <?php endif; ?>
                    <a href="product.php?id=<?= $product['id'] ?>" class="block">
                        <div
                            class="aspect-square bg-gradient-to-br from-kitty-pink-50 to-kitty-pink-100 flex items-center justify-center overflow-hidden">
                            <?php if ($img = getProductImage($product['name'])): ?>
                                <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>"
                                    class="w-full h-full object-cover mix-blend-multiply hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <span class="text-6xl md:text-7xl"><?= getProductEmoji($product['name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-800 mb-1 truncate hover:text-kitty-pink-600 transition-colors">
                                <?= e($product['name']) ?>
                            </h3>
                            <p class="text-xs text-gray-500 mb-2"><?= e($product['category_name'] ?? '未分类') ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-kitty-pink-600 font-bold text-lg">
                                    <?= formatPrice($product['price']) ?>
                                </span>
                                <button onclick="addToCart(<?= $product['id'] ?>)"
                                    class="w-8 h-8 bg-kitty-pink-100 hover:bg-kitty-pink-500 text-kitty-pink-500 hover:text-white rounded-full flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 特色服务 -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="kitty-card rounded-2xl p-6 text-center">
                <div class="text-3xl mb-3">🚚</div>
                <h3 class="font-bold text-gray-800 mb-1">免费配送</h3>
                <p class="text-sm text-gray-500">满99元包邮</p>
            </div>
            <div class="kitty-card rounded-2xl p-6 text-center">
                <div class="text-3xl mb-3">✅</div>
                <h3 class="font-bold text-gray-800 mb-1">正品保证</h3>
                <p class="text-sm text-gray-500">官方授权</p>
            </div>
            <div class="kitty-card rounded-2xl p-6 text-center">
                <div class="text-3xl mb-3">🔄</div>
                <h3 class="font-bold text-gray-800 mb-1">7天无理由</h3>
                <p class="text-sm text-gray-500">轻松退换</p>
            </div>
            <div class="kitty-card rounded-2xl p-6 text-center">
                <div class="text-3xl mb-3">💬</div>
                <h3 class="font-bold text-gray-800 mb-1">贴心客服</h3>
                <p class="text-sm text-gray-500">随时在线</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>