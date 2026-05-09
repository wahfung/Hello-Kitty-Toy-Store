<?php
/**
 * 注册页面
 * Hello Kitty 玩具商城
 */
require_once __DIR__ . '/includes/functions.php';

// 已登录则跳转首页
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

// 处理注册请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');

    // 验证密码确认
    if ($password !== $confirmPassword) {
        $error = '两次输入的密码不一致';
    } else {
        $result = registerUser($username, $password, $email);

        if ($result['success']) {
            setFlash('success', '注册成功，请登录！');
            redirect('login.php');
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = '注册';
require_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- 注册卡片 -->
        <div class="kitty-card rounded-3xl p-8 relative">
            <!-- 装饰蝴蝶结 -->
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                <span class="text-5xl">🎀</span>
            </div>

            <div class="text-center mb-8 pt-4">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">创建账户</h1>
                <p class="text-gray-500">加入 Hello Kitty 大家庭</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-600 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="mr-1">👤</span>用户名 <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        minlength="2"
                        maxlength="50"
                        class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white"
                        placeholder="2-50个字符"
                        value="<?= e($_POST['username'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="mr-1">📧</span>邮箱 <span class="text-gray-400">(可选)</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white"
                        placeholder="your@email.com"
                        value="<?= e($_POST['email'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="mr-1">🔒</span>密码 <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        minlength="6"
                        class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white"
                        placeholder="至少6个字符"
                    >
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="mr-1">🔐</span>确认密码 <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        required
                        minlength="6"
                        class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white"
                        placeholder="再次输入密码"
                    >
                </div>

                <button type="submit" class="kitty-btn w-full text-white py-3 rounded-xl font-bold text-lg">
                    注 册
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-500 text-sm">
                    已有账户？
                    <a href="login.php" class="text-kitty-pink-500 hover:text-kitty-pink-600 font-medium">
                        立即登录
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
