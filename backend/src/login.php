<?php
/**
 * 登录页面
 * Hello Kitty 玩具商城
 */
require_once __DIR__ . '/includes/functions.php';

// 已登录则跳转首页
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

// 处理登录请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = loginUser($username, $password);

    if ($result['success']) {
        setFlash('success', '登录成功，欢迎回来！');
        redirect($_GET['redirect'] ?? 'index.php');
    } else {
        $error = $result['message'];
    }
}

$pageTitle = '登录';
require_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- 登录卡片 -->
        <div class="kitty-card rounded-3xl p-8 relative">
            <!-- 装饰蝴蝶结 -->
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                <span class="text-5xl">🎀</span>
            </div>

            <div class="text-center mb-8 pt-4">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">欢迎回来</h1>
                <p class="text-gray-500">登录你的 Hello Kitty 账户</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-600 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="mr-1">👤</span>用户名
                    </label>
                    <input type="text" id="username" name="username" required
                        class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white" placeholder="请输入用户名"
                        value="<?= e($_POST['username'] ?? '') ?>">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="mr-1">🔒</span>密码
                    </label>
                    <input type="password" id="password" name="password" required
                        class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white" placeholder="请输入密码">
                </div>

                <button type="submit" class="kitty-btn w-full text-white py-3 rounded-xl font-bold text-lg">
                    登 录
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-500 text-sm">
                    还没有账户？
                    <a href="register.php" class="text-kitty-pink-500 hover:text-kitty-pink-600 font-medium">
                        立即注册
                    </a>
                </p>
            </div>

        </div>

        <!-- 装饰元素 -->
        <div class="flex justify-center mt-6 space-x-4 text-2xl">
            <span class="animate-bounce">💖</span>
            <span class="animate-bounce delay-100">✨</span>
            <span class="animate-bounce delay-200">🌸</span>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>