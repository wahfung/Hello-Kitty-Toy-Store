-- Hello Kitty 玩具商城数据库初始化脚本
-- 使用 UTF-8 编码

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- 用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    avatar VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 商品分类表
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 商品表
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 100,
    is_hot TINYINT(1) DEFAULT 0,
    is_new TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 购物车表
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 留言板表
CREATE TABLE IF NOT EXISTS guestbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nickname VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    reply TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 订单表
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 订单详情表
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================== 初始化数据 ====================

-- 插入测试用户 (密码: 123456)
INSERT INTO users (username, password, email) VALUES
('admin', '$2y$10$7fXigfTZAlQRLF7Aar95YuxPa1J5IncUa0WORhq4EI2R1db7yutq.', 'admin@hellokitty.com'),
('kitty', '$2y$10$7fXigfTZAlQRLF7Aar95YuxPa1J5IncUa0WORhq4EI2R1db7yutq.', 'kitty@hellokitty.com'),
('melody', '$2y$10$7fXigfTZAlQRLF7Aar95YuxPa1J5IncUa0WORhq4EI2R1db7yutq.', 'melody@hellokitty.com');

-- 插入商品分类
INSERT INTO categories (name, description, icon) VALUES
('毛绒玩具', 'Hello Kitty 系列毛绒公仔', '🧸'),
('文具用品', '可爱的 Hello Kitty 文具', '✏️'),
('生活用品', 'Hello Kitty 主题生活小物', '🎀'),
('服饰配件', 'Hello Kitty 服装和配饰', '👗'),
('限定款式', '限量版珍藏玩具', '⭐');

-- 插入商品数据
INSERT INTO products (category_id, name, description, price, image, stock, is_hot, is_new) VALUES
-- 毛绒玩具
(1, 'Hello Kitty 经典毛绒公仔', '经典款 Hello Kitty 毛绒玩具，柔软舒适，高度约30cm，采用优质毛绒面料', 128.00, 'plush1.jpg', 100, 1, 0),
(1, 'Hello Kitty 粉色礼服公仔', '穿着华丽粉色礼服的 Hello Kitty，适合收藏和送礼', 168.00, 'plush2.jpg', 80, 1, 1),
(1, 'My Melody 毛绒兔子', '超萌 My Melody 毛绒玩具，粉嫩可爱', 138.00, 'plush3.jpg', 90, 0, 1),
(1, 'Cinnamoroll 大耳狗公仔', '软萌大耳狗毛绒玩具，超级治愈', 148.00, 'plush4.jpg', 70, 1, 0),
(1, 'Kuromi 酷洛米公仔', '个性酷洛米毛绒玩具，黑紫配色超酷', 158.00, 'plush5.jpg', 60, 0, 1),

-- 文具用品
(2, 'Hello Kitty 笔记本套装', '精美笔记本三件套，包含线圈本、便签本、手账本', 58.00, 'stationery1.jpg', 200, 0, 0),
(2, 'Hello Kitty 中性笔套装', '6支装可爱中性笔，书写流畅，颜色丰富', 28.00, 'stationery2.jpg', 300, 1, 0),
(2, 'Hello Kitty 文具收纳盒', '桌面收纳神器，让桌面整洁有序', 45.00, 'stationery3.jpg', 150, 0, 1),
(2, 'My Melody 便利贴套装', '超萌便利贴组合，学习办公必备', 18.00, 'stationery4.jpg', 500, 0, 0),

-- 生活用品
(3, 'Hello Kitty 马克杯', '陶瓷马克杯，容量350ml，可微波炉加热', 68.00, 'life1.jpg', 120, 1, 0),
(3, 'Hello Kitty 保温杯', '不锈钢保温杯，12小时保温，500ml', 128.00, 'life2.jpg', 80, 0, 1),
(3, 'Hello Kitty 化妆镜', '便携折叠化妆镜，双面镜设计', 38.00, 'life3.jpg', 200, 0, 0),
(3, 'Hello Kitty 抱枕', '超柔软抱枕，午睡好伴侣', 88.00, 'life4.jpg', 100, 1, 1),

-- 服饰配件
(4, 'Hello Kitty 发夹套装', '可爱发夹10件套，多种款式', 35.00, 'accessory1.jpg', 300, 0, 1),
(4, 'Hello Kitty 手提包', '粉色PU皮手提包，时尚又实用', 198.00, 'accessory2.jpg', 50, 1, 0),
(4, 'Hello Kitty 袜子礼盒', '精美袜子5双装，纯棉舒适', 58.00, 'accessory3.jpg', 200, 0, 0),
(4, 'My Melody 发箍', '甜美兔耳朵发箍，少女心爆棚', 28.00, 'accessory4.jpg', 150, 0, 1),

-- 限定款式
(5, '2024新年限定 Hello Kitty', '龙年限定款，穿着中国风服饰', 298.00, 'limited1.jpg', 30, 1, 1),
(5, '樱花限定 Hello Kitty', '春季樱花主题限定款，浪漫粉色', 268.00, 'limited2.jpg', 40, 1, 1),
(5, '50周年纪念版公仔', 'Hello Kitty 50周年纪念珍藏版', 388.00, 'limited3.jpg', 20, 1, 1);

-- 插入留言板数据
INSERT INTO guestbook (user_id, nickname, content, reply) VALUES
(2, 'Kitty粉丝', '这家店的玩具质量超棒！Hello Kitty 公仔做工精细，女儿超喜欢！', '感谢您的支持！我们会继续提供优质的产品～'),
(3, '小美', '买了限定款，包装很精美，送人很有面子！', '谢谢认可！限定款都是精心设计的哦～'),
(NULL, '路过的猫咪', '网站好可爱啊，粉粉的看着心情都变好了！', NULL),
(2, 'Kitty粉丝', '希望能多上一些 Cinnamoroll 的商品，大耳狗也超可爱的！', '好的呢，我们会持续上新更多三丽鸥家族的商品！'),
(NULL, '甜甜', '第一次在这里购物，发货很快，玩具和图片一样好看！', '欢迎再次光临！祝您购物愉快～');
