# Hello Kitty 玩具商城

一个粉色系 Hello Kitty 主题玩具商城，采用 PHP + MySQL 构建。

## 技术栈

- **Frontend**: PHP + Tailwind CSS (CDN)
- **Backend**: PHP 8.2 + Apache
- **Database**: MySQL 8.0

## 本地启动

1. 安装 PHP 8.2，并启用 `pdo_mysql` / `mysqli` 扩展。
2. 安装并启动 MySQL 8.0，创建数据库和用户：
   ```bash
   mysql -uroot -p -e "CREATE DATABASE IF NOT EXISTS hellokitty_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -uroot -p -e "CREATE USER IF NOT EXISTS 'kitty'@'%' IDENTIFIED BY 'kitty123';"
   mysql -uroot -p -e "GRANT ALL PRIVILEGES ON hellokitty_shop.* TO 'kitty'@'%'; FLUSH PRIVILEGES;"
   ```
3. 导入初始化数据：
   ```bash
   mysql -ukitty -pkitty123 hellokitty_shop < mysql/init/init.sql
   ```
4. 在项目目录启动 PHP 内置服务：
   ```bash
   DB_HOST=127.0.0.1 DB_NAME=hellokitty_shop DB_USER=kitty DB_PASS=kitty123 php -S 127.0.0.1:8088 -t backend/src
   ```
5. 访问 http://localhost:8088 即可使用。

## 服务地址

| 服务 | 地址 |
|------|------|
| 前端页面 | http://localhost:8088 |
| 数据库 | 127.0.0.1:3306 |

## 测试账号

| 用户名 | 密码 | 说明 |
|--------|------|------|
| admin | 123456 | 管理员账号 |
| kitty | 123456 | 普通用户 |
| melody | 123456 | 普通用户 |

## 功能特性

- **首页**: 展示热门商品、新品上架、商品分类
- **商品列表**: 支持分类筛选、热门/新品筛选
- **商品详情**: 详细商品信息、数量选择、加入购物车
- **购物车**: 添加/删除商品、修改数量、查看总价
- **用户系统**: 注册、登录、退出
- **留言板**: 发表留言、查看留言、店主回复

## 设计特点

- 粉色系配色方案，符合 Hello Kitty 主题
- 现代化 UI 设计，使用 Tailwind CSS
- 响应式布局，支持移动端访问
- 精致的交互动画和状态反馈
- 可爱的图标装饰

## 项目结构

```
hellokitty-shop/
├── .gitignore
├── README.md
├── backend/
│   └── src/                # PHP 应用源码
│       ├── index.php
│       ├── login.php
│       ├── register.php
│       ├── logout.php
│       ├── products.php
│       ├── product.php
│       ├── cart.php
│       ├── guestbook.php
│       ├── config/
│       ├── includes/
│       ├── api/
│       └── assets/
└── mysql/
    └── init/
        └── init.sql
```

## 开发说明

### 数据库配置

数据库使用 UTF-8 编码（utf8mb4），包含以下数据表：

- `users` - 用户表
- `categories` - 商品分类表
- `products` - 商品表
- `cart` - 购物车表
- `guestbook` - 留言板表
- `orders` - 订单表
- `order_items` - 订单详情表

### 环境变量

| 变量名 | 默认值 | 说明 |
|--------|--------|------|
| DB_HOST | 127.0.0.1 | 数据库主机 |
| DB_NAME | hellokitty_shop | 数据库名 |
| DB_USER | kitty | 数据库用户 |
| DB_PASS | kitty123 | 数据库密码 |

## 注意事项

1. 首次启动时，MySQL 容器需要初始化数据库，请耐心等待
2. 如遇到连接数据库失败，可能是 MySQL 尚未完全启动或环境变量配置不正确
3. 商品图片使用 Emoji 表情代替，实际项目中可替换为真实图片
