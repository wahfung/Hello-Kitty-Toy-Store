<?php
require_once __DIR__ . '/functions.php';
$currentUser = getCurrentUser();
$cartCount = $currentUser ? getCartCount($currentUser['id']) : 0;
$flash = getFlash();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>Hello Kitty 玩具商城</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kitty-pink': {
                            50: '#fef1f7',
                            100: '#fee5f0',
                            200: '#ffcce3',
                            300: '#ffa1ca',
                            400: '#ff6ba5',
                            500: '#ff3d84',
                            600: '#f0185e',
                            700: '#d10a47',
                            800: '#ad0c3b',
                            900: '#8f0f35',
                        },
                        'kitty-cream': '#fff5f8',
                        'kitty-light': '#ffe4ec',
                    },
                    fontFamily: {
                        'cute': ['"Comic Sans MS"', 'cursive', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #fff5f8 0%, #ffe4ec 50%, #ffd6e7 100%);
            min-height: 100vh;
        }

        .kitty-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid #ffcce3;
            transition: all 0.3s ease;
        }

        .kitty-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 107, 165, 0.2);
            border-color: #ff6ba5;
        }

        .kitty-btn {
            background: linear-gradient(135deg, #ff6ba5 0%, #ff3d84 100%);
            transition: all 0.3s ease;
        }

        .kitty-btn:hover {
            background: linear-gradient(135deg, #ff3d84 0%, #f0185e 100%);
            transform: scale(1.05);
        }

        .kitty-input {
            border: 2px solid #ffcce3;
            transition: all 0.3s ease;
        }

        .kitty-input:focus {
            border-color: #ff6ba5;
            box-shadow: 0 0 0 3px rgba(255, 107, 165, 0.2);
        }

        .ribbon {
            position: absolute;
            top: 10px;
            right: -5px;
            background: linear-gradient(135deg, #ff6ba5 0%, #ff3d84 100%);
            color: white;
            padding: 4px 15px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 3px 0 0 3px;
        }

        .ribbon::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: -5px;
            border: 5px solid transparent;
            border-top-color: #d10a47;
            border-right-color: #d10a47;
        }

        .loading-spinner {
            border: 3px solid #ffcce3;
            border-top: 3px solid #ff6ba5;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toast {
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* 可爱的装饰 */
        .bow-decoration::before {
            content: '🎀';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 24px;
        }

        /* 骨架屏 */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
</head>
<body class="antialiased">
    <!-- 导航栏 -->
    <nav class="bg-white/90 backdrop-blur-md shadow-md sticky top-0 z-50 border-b-2 border-kitty-pink-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="index.php" class="flex items-center space-x-2 group">
                    <span class="text-3xl">🎀</span>
                    <span class="text-xl font-bold bg-gradient-to-r from-kitty-pink-500 to-kitty-pink-600 bg-clip-text text-transparent group-hover:from-kitty-pink-600 group-hover:to-kitty-pink-700 transition-all">
                        Hello Kitty 玩具商城
                    </span>
                </a>

                <!-- 导航链接 -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="<?= $currentPage === 'index' ? 'text-kitty-pink-600 font-bold' : 'text-gray-600 hover:text-kitty-pink-500' ?> transition-colors flex items-center space-x-1">
                        <span>🏠</span>
                        <span>首页</span>
                    </a>
                    <a href="products.php" class="<?= $currentPage === 'products' ? 'text-kitty-pink-600 font-bold' : 'text-gray-600 hover:text-kitty-pink-500' ?> transition-colors flex items-center space-x-1">
                        <span>🧸</span>
                        <span>全部商品</span>
                    </a>
                    <a href="guestbook.php" class="<?= $currentPage === 'guestbook' ? 'text-kitty-pink-600 font-bold' : 'text-gray-600 hover:text-kitty-pink-500' ?> transition-colors flex items-center space-x-1">
                        <span>💌</span>
                        <span>留言板</span>
                    </a>
                </div>

                <!-- 用户区域 -->
                <div class="flex items-center space-x-4">
                    <?php if ($currentUser): ?>
                        <!-- 购物车 -->
                        <a href="cart.php" class="relative p-2 text-gray-600 hover:text-kitty-pink-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span data-cart-badge class="absolute -top-1 -right-1 bg-kitty-pink-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold <?= $cartCount > 0 ? '' : 'hidden' ?>">
                                <?= $cartCount > 99 ? '99+' : $cartCount ?>
                            </span>
                        </a>

                        <!-- 用户菜单 -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-kitty-pink-500 transition-colors">
                                <span class="w-8 h-8 bg-kitty-pink-100 rounded-full flex items-center justify-center text-kitty-pink-600 font-bold">
                                    <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                                </span>
                                <span class="hidden sm:inline"><?= e($currentUser['username']) ?></span>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border-2 border-kitty-pink-100">
                                <a href="logout.php" class="block px-4 py-3 text-gray-700 hover:bg-kitty-pink-50 hover:text-kitty-pink-600 rounded-xl transition-colors">
                                    👋 退出登录
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-kitty-pink-500 transition-colors font-medium">
                            登录
                        </a>
                        <a href="register.php" class="kitty-btn text-white px-4 py-2 rounded-full text-sm font-bold">
                            注册
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 移动端导航 -->
        <div class="md:hidden border-t border-kitty-pink-100">
            <div class="flex justify-around py-2">
                <a href="index.php" class="<?= $currentPage === 'index' ? 'text-kitty-pink-600' : 'text-gray-500' ?> flex flex-col items-center text-xs">
                    <span class="text-lg">🏠</span>
                    <span>首页</span>
                </a>
                <a href="products.php" class="<?= $currentPage === 'products' ? 'text-kitty-pink-600' : 'text-gray-500' ?> flex flex-col items-center text-xs">
                    <span class="text-lg">🧸</span>
                    <span>商品</span>
                </a>
                <a href="guestbook.php" class="<?= $currentPage === 'guestbook' ? 'text-kitty-pink-600' : 'text-gray-500' ?> flex flex-col items-center text-xs">
                    <span class="text-lg">💌</span>
                    <span>留言</span>
                </a>
                <a href="cart.php" class="<?= $currentPage === 'cart' ? 'text-kitty-pink-600' : 'text-gray-500' ?> flex flex-col items-center text-xs relative">
                    <span class="text-lg">🛒</span>
                    <span>购物车</span>
                    <span data-cart-badge class="absolute -top-1 right-2 bg-kitty-pink-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center font-bold text-[10px] <?= $cartCount > 0 ? '' : 'hidden' ?>">
                        <?= $cartCount > 9 ? '9+' : $cartCount ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Flash 消息 -->
    <?php if ($flash): ?>
        <div id="flash-toast" class="toast fixed top-20 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-full shadow-lg <?= $flash['type'] === 'success' ? 'bg-green-500' : ($flash['type'] === 'error' ? 'bg-red-500' : 'bg-kitty-pink-500') ?> text-white font-medium">
            <?= e($flash['message']) ?>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('flash-toast').style.opacity = '0';
                setTimeout(() => document.getElementById('flash-toast').remove(), 300);
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- 主内容区 -->
    <main class="min-h-screen">
