    </main>

    <!-- 页脚 -->
    <footer class="bg-white/80 backdrop-blur-md border-t-2 border-kitty-pink-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- 关于我们 -->
                <div>
                    <h3 class="text-lg font-bold text-kitty-pink-600 mb-4 flex items-center">
                        <span class="mr-2">🎀</span>关于我们
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Hello Kitty 玩具商城是一家专注于三丽鸥系列玩具的可爱小店，
                        我们精选全球各地的正版授权商品，为您带来最萌最可爱的购物体验！
                    </p>
                </div>

                <!-- 快速链接 -->
                <div>
                    <h3 class="text-lg font-bold text-kitty-pink-600 mb-4 flex items-center">
                        <span class="mr-2">💕</span>快速链接
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="index.php" class="text-gray-600 hover:text-kitty-pink-500 transition-colors">
                                🏠 首页
                            </a>
                        </li>
                        <li>
                            <a href="products.php" class="text-gray-600 hover:text-kitty-pink-500 transition-colors">
                                🧸 全部商品
                            </a>
                        </li>
                        <li>
                            <a href="guestbook.php" class="text-gray-600 hover:text-kitty-pink-500 transition-colors">
                                💌 留言板
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- 联系方式 -->
                <div>
                    <h3 class="text-lg font-bold text-kitty-pink-600 mb-4 flex items-center">
                        <span class="mr-2">📞</span>联系我们
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <span class="mr-2">📧</span>
                            <span>hello@hellokitty-shop.com</span>
                        </li>
                        <li class="flex items-center">
                            <span class="mr-2">📱</span>
                            <span>400-KITTY-520</span>
                        </li>
                        <li class="flex items-center">
                            <span class="mr-2">🕐</span>
                            <span>营业时间: 9:00 - 21:00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 版权信息 -->
            <div class="mt-8 pt-6 border-t border-kitty-pink-100 text-center">
                <p class="text-gray-500 text-sm">
                    © 2024 Hello Kitty 玩具商城 - Made with 💖
                </p>
                <p class="text-gray-400 text-xs mt-1">
                    本站仅供学习交流使用，商品图片及品牌归 Sanrio 所有
                </p>
            </div>
        </div>
    </footer>

    <!-- 通用 JavaScript -->
    <script>
        // Toast 通知函数
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast fixed top-20 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-full shadow-lg text-white font-medium ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' : 'bg-kitty-pink-500'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // 添加到购物车
        async function addToCart(productId, quantity = 1) {
            try {
                const response = await fetch('api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product_id: productId,
                        quantity: quantity
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    // 更新购物车数量
                    updateCartBadge(data.cart_count);
                } else {
                    showToast(data.message, 'error');
                    if (data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 1500);
                    }
                }
            } catch (error) {
                showToast('操作失败，请稍后再试', 'error');
            }
        }

        // 更新购物车角标
        function updateCartBadge(count) {
            const badges = document.querySelectorAll('[data-cart-badge]');
            badges.forEach(badge => {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        }

        // 确认对话框（现代化弹窗）
        function showConfirmModal(message, onConfirm, onCancel) {
            // 创建遮罩层
            const overlay = document.createElement('div');
            overlay.id = 'confirm-modal-overlay';
            overlay.className = 'fixed inset-0 bg-black/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4';
            overlay.style.animation = 'fadeIn 0.2s ease';

            // 创建弹窗内容
            overlay.innerHTML = `
                <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full transform transition-all" style="animation: scaleIn 0.2s ease">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-kitty-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-kitty-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">确认操作</h3>
                        <p class="text-gray-600 mb-6">${message}</p>
                        <div class="flex gap-3">
                            <button id="confirm-modal-cancel" class="flex-1 px-4 py-3 border-2 border-kitty-pink-200 text-gray-600 rounded-full font-bold hover:bg-kitty-pink-50 hover:border-kitty-pink-300 transition-all">
                                取消
                            </button>
                            <button id="confirm-modal-confirm" class="flex-1 px-4 py-3 bg-gradient-to-r from-kitty-pink-400 to-kitty-pink-500 text-white rounded-full font-bold hover:from-kitty-pink-500 hover:to-kitty-pink-600 transition-all">
                                确定
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);

            // 阻止背景滚动
            document.body.style.overflow = 'hidden';

            // 关闭弹窗函数
            const closeModal = () => {
                overlay.style.animation = 'fadeOut 0.2s ease';
                setTimeout(() => {
                    overlay.remove();
                    document.body.style.overflow = '';
                }, 150);
            };

            // 绑定事件
            document.getElementById('confirm-modal-cancel').onclick = () => {
                closeModal();
                if (onCancel) onCancel();
            };

            document.getElementById('confirm-modal-confirm').onclick = () => {
                closeModal();
                if (onConfirm) onConfirm();
            };

            // 点击遮罩关闭
            overlay.onclick = (e) => {
                if (e.target === overlay) {
                    closeModal();
                    if (onCancel) onCancel();
                }
            };

            // ESC 键关闭
            const handleEsc = (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    if (onCancel) onCancel();
                    document.removeEventListener('keydown', handleEsc);
                }
            };
            document.addEventListener('keydown', handleEsc);
        }
    </script>

    <!-- 弹窗动画样式 -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</body>
</html>
