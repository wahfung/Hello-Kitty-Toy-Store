<?php
/**
 * 购物车页面
 * Hello Kitty 玩具商城
 */
require_once __DIR__ . '/includes/functions.php';

// 未登录则跳转登录
if (!isLoggedIn()) {
    setFlash('info', '请先登录后再查看购物车');
    redirect('login.php?redirect=cart.php');
}

$pageTitle = '购物车';
$currentUser = getCurrentUser();
$cartItems = getCartItems($currentUser['id']);

// 计算总价
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 flex items-center">
        <span class="mr-2">🛒</span>我的购物车
    </h1>

    <?php if (empty($cartItems)): ?>
        <!-- 空购物车 -->
        <div class="kitty-card rounded-3xl p-12 text-center">
            <div class="text-8xl mb-6">🛒</div>
            <h2 class="text-xl font-bold text-gray-700 mb-2">购物车是空的</h2>
            <p class="text-gray-500 mb-6">快去挑选心仪的 Hello Kitty 商品吧！</p>
            <a href="products.php" class="kitty-btn inline-block text-white px-8 py-3 rounded-full font-bold">
                去逛逛
            </a>
        </div>
    <?php else: ?>
        <!-- 购物车列表 -->
        <div class="kitty-card rounded-3xl overflow-hidden mb-6">
            <div class="divide-y divide-kitty-pink-100">
                <?php foreach ($cartItems as $item): ?>
                    <div class="p-4 md:p-6 flex items-center gap-4" id="cart-item-<?= $item['id'] ?>"
                        data-cart-id="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>">
                        <!-- 商品图片 -->
                        <div
                            class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-kitty-pink-50 to-kitty-pink-100 rounded-2xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <?php if ($img = getProductImage($item['name'])): ?>
                                <img src="<?= e($img) ?>" alt="<?= e($item['name']) ?>"
                                    class="w-full h-full object-cover mix-blend-multiply">
                            <?php else: ?>
                                <span class="text-4xl md:text-5xl">
                                    <?= getProductEmoji($item['name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- 商品信息 -->
                        <div class="flex-1 min-w-0">
                            <a href="product.php?id=<?= $item['product_id'] ?>"
                                class="font-bold text-gray-800 hover:text-kitty-pink-600 transition-colors block truncate">
                                <?= e($item['name']) ?>
                            </a>
                            <div class="text-kitty-pink-600 font-bold mt-1">
                                <?= formatPrice($item['price']) ?>
                            </div>
                        </div>

                        <!-- 数量控制 -->
                        <div class="flex items-center border-2 border-kitty-pink-200 rounded-xl overflow-hidden">
                            <button onclick="changeQty(<?= $item['id'] ?>, -1)"
                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-kitty-pink-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="w-10 text-center font-medium"
                                id="qty-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                            <button onclick="changeQty(<?= $item['id'] ?>, 1)"
                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-kitty-pink-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>

                        <!-- 小计 -->
                        <div class="text-right hidden md:block">
                            <div class="text-sm text-gray-500">小计</div>
                            <div class="text-kitty-pink-600 font-bold" id="subtotal-<?= $item['id'] ?>">
                                <?= formatPrice($item['price'] * $item['quantity']) ?>
                            </div>
                        </div>

                        <!-- 删除按钮 -->
                        <button onclick="removeItem(<?= $item['id'] ?>)"
                            class="w-10 h-10 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 结算栏 -->
        <div class="kitty-card rounded-3xl p-6 sticky bottom-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">共 <span id="total-items"
                            class="font-bold text-kitty-pink-600"><?= count($cartItems) ?></span> 件商品</span>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-600">
                        合计: <span id="total-price"
                            class="text-2xl font-bold text-kitty-pink-600"><?= formatPrice($totalPrice) ?></span>
                    </span>
                </div>
                <button onclick="checkout()" class="kitty-btn text-white px-12 py-3 rounded-full font-bold text-lg">
                    去结算
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // 修改数量（增加或减少）
    function changeQty(cartId, delta) {
        const qtyElement = document.getElementById(`qty-${cartId}`);
        const currentQty = parseInt(qtyElement.textContent);
        const newQty = currentQty + delta;

        updateQuantity(cartId, newQty);
    }

    async function updateQuantity(cartId, quantity) {
        if (quantity <= 0) {
            showConfirmModal('确定要移除这件商品吗？', () => {
                doUpdateQuantity(cartId, quantity);
            });
            return;
        }
        doUpdateQuantity(cartId, quantity);
    }

    async function doUpdateQuantity(cartId, quantity) {
        try {
            const response = await fetch('api/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update', cart_id: cartId, quantity })
            });

            const data = await response.json();
            if (data.success) {
                if (quantity <= 0) {
                    document.getElementById(`cart-item-${cartId}`).remove();
                    showToast('已移除商品', 'success');
                } else {
                    // 更新显示的数量
                    document.getElementById(`qty-${cartId}`).textContent = quantity;

                    // 更新小计
                    const cartItem = document.getElementById(`cart-item-${cartId}`);
                    const price = parseFloat(cartItem.dataset.price);
                    const subtotal = (price * quantity).toFixed(2);
                    document.getElementById(`subtotal-${cartId}`).textContent = '¥' + subtotal;
                }
                updateTotals(data);
                updateCartBadge(data.cart_count);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('操作失败', 'error');
        }
    }

    function removeItem(cartId) {
        showConfirmModal('确定要移除这件商品吗？', async () => {
            try {
                const response = await fetch('api/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'remove', cart_id: cartId })
                });

                const data = await response.json();
                if (data.success) {
                    document.getElementById(`cart-item-${cartId}`).remove();
                    updateTotals(data);
                    updateCartBadge(data.cart_count);
                    showToast('已移除商品', 'success');

                    // 如果购物车为空，刷新页面
                    if (data.total_items === 0) {
                        location.reload();
                    }
                }
            } catch (error) {
                showToast('操作失败', 'error');
            }
        });
    }

    function updateTotals(data) {
        document.getElementById('total-items').textContent = data.total_items;
        document.getElementById('total-price').textContent = data.total_price_formatted;
    }

    function checkout() {
        showToast('结算功能开发中，敬请期待！', 'info');
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>