<?php
/**
 * 留言板页面
 * Hello Kitty 玩具商城
 */
require_once __DIR__ . '/includes/functions.php';

$currentUser = getCurrentUser();
$error = '';

// 处理留言提交 - 必须在输出任何内容之前
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $userId = $currentUser ? $currentUser['id'] : null;

    // 如果已登录，使用用户名作为昵称
    if ($currentUser && empty($nickname)) {
        $nickname = $currentUser['username'];
    }

    $result = addGuestbookMessage($nickname, $content, $userId);

    if ($result['success']) {
        setFlash('success', '留言成功！');
        redirect('guestbook.php');
    } else {
        $error = $result['message'];
    }
}

// 获取留言列表
$messages = getGuestbookMessages(50);

// 现在才输出 HTML
$pageTitle = '留言板';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
            <span class="mr-2">💌</span>留言板
        </h1>
        <p class="text-gray-500">分享你与 Hello Kitty 的故事，留下美好的回忆</p>
    </div>

    <!-- 留言表单 -->
    <div class="kitty-card rounded-3xl p-6 md:p-8 mb-8 relative">
        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
            <span class="text-3xl">✨</span>
        </div>

        <h2 class="font-bold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">📝</span>发表留言
        </h2>

        <?php if ($error): ?>
            <div class="mb-4 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-600 text-sm">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="mr-1">👤</span>昵称 <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nickname"
                        name="nickname"
                        required
                        maxlength="50"
                        class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white"
                        placeholder="你的昵称"
                        value="<?= e($currentUser['username'] ?? ($_POST['nickname'] ?? '')) ?>"
                        <?= $currentUser ? 'readonly' : '' ?>
                    >
                </div>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="mr-1">💬</span>留言内容 <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="content"
                    name="content"
                    required
                    maxlength="1000"
                    rows="4"
                    class="kitty-input w-full px-4 py-3 rounded-xl focus:outline-none bg-white resize-none"
                    placeholder="分享你的想法..."
                ><?= e($_POST['content'] ?? '') ?></textarea>
                <div class="text-right text-xs text-gray-400 mt-1">
                    <span id="char-count">0</span>/1000
                </div>
            </div>

            <button type="submit" class="kitty-btn text-white px-8 py-3 rounded-xl font-bold flex items-center justify-center">
                <span class="mr-2">💖</span>发表留言
            </button>
        </form>
    </div>

    <!-- 留言列表 -->
    <div class="space-y-4">
        <h2 class="font-bold text-gray-800 flex items-center">
            <span class="mr-2">💕</span>全部留言 <span class="text-kitty-pink-500 ml-2">(<?= count($messages) ?>)</span>
        </h2>

        <?php if (empty($messages)): ?>
            <div class="kitty-card rounded-2xl p-8 text-center">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-gray-500">还没有留言，快来抢沙发吧！</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="kitty-card rounded-2xl p-5 md:p-6">
                    <div class="flex items-start gap-4">
                        <!-- 头像 -->
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-kitty-pink-200 to-kitty-pink-300 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                            <span class="text-xl md:text-2xl">
                                <?php
                                $avatars = ['🐱', '🐰', '🐻', '🦊', '🐼', '🐨'];
                                echo $avatars[ord($message['nickname'][0]) % count($avatars)];
                                ?>
                            </span>
                        </div>

                        <!-- 内容 -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-bold text-gray-800"><?= e($message['nickname']) ?></span>
                                <?php if ($message['user_id']): ?>
                                    <span class="text-xs bg-kitty-pink-100 text-kitty-pink-600 px-2 py-0.5 rounded-full">会员</span>
                                <?php endif; ?>
                                <span class="text-xs text-gray-400"><?= formatDate($message['created_at']) ?></span>
                            </div>

                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?= e($message['content']) ?></p>

                            <!-- 回复 -->
                            <?php if ($message['reply']): ?>
                                <div class="mt-4 pl-4 border-l-4 border-kitty-pink-200 bg-kitty-pink-50 rounded-r-xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-kitty-pink-600 font-bold text-sm">🎀 店主回复</span>
                                    </div>
                                    <p class="text-gray-600 text-sm"><?= e($message['reply']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // 字数统计
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('char-count');

    contentTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // 初始化字数
    charCount.textContent = contentTextarea.value.length;
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
