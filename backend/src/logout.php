<?php
/**
 * 登出处理
 * Hello Kitty 玩具商城
 */
require_once __DIR__ . '/includes/functions.php';

logoutUser();
setFlash('success', '已安全退出，下次再见！');
redirect('index.php');
