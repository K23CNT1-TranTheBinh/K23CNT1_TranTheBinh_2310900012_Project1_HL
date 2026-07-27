-- CƠ SỞ DỮ LIỆU CỬA HÀNG ĐIỆN THOẠI
-- =============================================
-- TẠO CƠ SỞ DỮ LIỆU
-- =============================================
CREATE DATABASE IF NOT EXISTS db_phone_shop 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_phone_shop;

-- Ban cu tung co trigger tru ton kho trong khi Laravel cung tru ton kho,
-- lam moi don hang bi tru hai lan. Xoa trigger cu neu dang cap nhat database.
DROP TRIGGER IF EXISTS trg_after_insert_order_detail;

-- =============================================
-- 1. BẢNG QUẢN TRỊ VIÊN (tên kỹ thuật: admins)
-- =============================================
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'staff') DEFAULT 'staff',
    status TINYINT DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- 2. BẢNG DANH MỤC
-- =============================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    status TINYINT DEFAULT 1 COMMENT '1: Hiển thị, 0: Ẩn',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- 3. BẢNG THƯƠNG HIỆU
-- =============================================
CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    logo VARCHAR(255),
    status TINYINT DEFAULT 1 COMMENT '1: Hiển thị, 0: Ẩn',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- 4. BẢNG SẢN PHẨM (Bảng quan trọng nhất)
-- =============================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category_id INT,
    brand_id INT,
    price DECIMAL(12,0) NOT NULL COMMENT 'Giá gốc',
    sale_price DECIMAL(12,0) COMMENT 'Giá khuyến mãi',
    stock INT DEFAULT 0 COMMENT 'Số lượng tồn kho',
    description TEXT COMMENT 'Mô tả chi tiết',
    short_desc VARCHAR(500) COMMENT 'Mô tả ngắn',
    image VARCHAR(255) COMMENT 'Ảnh đại diện',
    images TEXT COMMENT 'Nhiều ảnh (JSON)',
    specs TEXT COMMENT 'Thông số kỹ thuật (JSON)',
    is_featured TINYINT DEFAULT 0 COMMENT '1: Nổi bật, 0: Không',
    is_new TINYINT DEFAULT 1 COMMENT '1: Mới, 0: Cũ',
    views INT DEFAULT 0 COMMENT 'Lượt xem',
    status TINYINT DEFAULT 1 COMMENT '1: Hiển thị, 0: Ẩn',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
);

-- =============================================
-- 5. BẢNG KHÁCH HÀNG
-- =============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    avatar VARCHAR(255),
    status TINYINT DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- 6. BẢNG GIỎ HÀNG
-- =============================================
CREATE TABLE carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (user_id, product_id)
);

-- =============================================
-- 7. BẢNG ĐƠN HÀNG
-- =============================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    coupon_id INT NULL,
    order_code VARCHAR(20) UNIQUE NOT NULL,
    total_amount DECIMAL(12,0) NOT NULL COMMENT 'Tổng tiền',
    shipping_fee DECIMAL(10,0) DEFAULT 0 COMMENT 'Phí vận chuyển',
    discount DECIMAL(10,0) DEFAULT 0 COMMENT 'Giảm giá',
    final_amount DECIMAL(12,0) NOT NULL COMMENT 'Thành tiền',
    status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') DEFAULT 'pending' 
        COMMENT 'pending: Chờ xác nhận, confirmed: Đã xác nhận, shipping: Đang giao, completed: Hoàn thành, cancelled: Đã hủy',
    payment_method ENUM('cod', 'banking', 'momo') DEFAULT 'cod' 
        COMMENT 'cod: Tiền mặt, banking: Chuyển khoản, momo: Ví MoMo',
    payment_status ENUM('pending', 'paid') DEFAULT 'pending' 
        COMMENT 'pending: Chưa thanh toán, paid: Đã thanh toán',
    shipping_address TEXT NOT NULL COMMENT 'Địa chỉ giao hàng',
    shipping_phone VARCHAR(20) NOT NULL COMMENT 'SĐT nhận hàng',
    shipping_name VARCHAR(100) NOT NULL COMMENT 'Tên người nhận',
    note TEXT COMMENT 'Ghi chú',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- 8. BẢNG CHI TIẾT ĐƠN HÀNG
-- =============================================
CREATE TABLE order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL COMMENT 'Tên sản phẩm (lưu lại)',
    product_price DECIMAL(12,0) NOT NULL COMMENT 'Giá tại thời điểm mua',
    quantity INT NOT NULL,
    total_price DECIMAL(12,0) NOT NULL COMMENT 'Thành tiền = giá * số lượng',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =============================================
-- 9. BẢNG ĐÁNH GIÁ
-- =============================================
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5) COMMENT 'Số sao từ 1-5',
    comment TEXT,
    status TINYINT DEFAULT 1 COMMENT '1: Hiển thị, 0: Ẩn',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review_product_user (product_id, user_id)
);

-- =============================================
-- 10. BẢNG MÃ GIẢM GIÁ
-- =============================================
CREATE TABLE coupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_type ENUM('percent', 'fixed') NOT NULL COMMENT 'percent: Giảm %, fixed: Giảm cố định',
    discount_value DECIMAL(10,0) NOT NULL COMMENT 'Giá trị giảm',
    min_order_amount DECIMAL(10,0) DEFAULT 0 COMMENT 'Đơn hàng tối thiểu',
    start_date DATETIME NOT NULL COMMENT 'Ngày bắt đầu',
    end_date DATETIME NOT NULL COMMENT 'Ngày kết thúc',
    usage_limit INT DEFAULT 1 COMMENT 'Số lần sử dụng tối đa',
    used_count INT DEFAULT 0 COMMENT 'Số lần đã sử dụng',
    status TINYINT DEFAULT 1 COMMENT '1: Hoạt động, 0: Không hoạt động',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE orders
    ADD CONSTRAINT fk_orders_coupon
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL;

-- =============================================
-- ========== THÊM DỮ LIỆU MẪU ===============
-- =============================================

-- 1. Thêm quản trị viên (6 thành viên Nhóm 5 - mỗi người 1 tài khoản)
INSERT INTO admins (username, password, full_name, email, phone, role) VALUES
('luong',    MD5('luong123'),    'Nguyễn Văn Lượng',   'luong@gmail.com',    '0901000060', 'admin'),
('luc',      MD5('luc123'),      'Phạm Văn Lực',       'luc@gmail.com',      '0901000059', 'admin'),
('phung',    MD5('phung123'),    'Phạm Đình Phùng',    'phung@gmail.com',    '0901000093', 'admin'),
('binh',     MD5('binh123'),     'Trần Thế Bình',      'binh@gmail.com',     '0901000012', 'admin'),
('an',       MD5('an123'),       'Mai Bình An',        'an@gmail.com',       '0901000001', 'admin'),
('hung',     MD5('hung123'),     'Bùi Lê Quốc Hùng',   'hung@gmail.com',     '0901000043', 'admin'),
('staff1',   MD5('staff123'),    'Nhân viên 1',        'staff@gmail.com',    '0909789456', 'staff');

-- 2. Thêm Danh mục
INSERT INTO categories (name, slug, description) VALUES
('Điện thoại Cao cấp', 'dien-thoai-cao-cap', 'Các dòng điện thoại cao cấp, flagship mới nhất'),
('Điện thoại Tầm trung', 'dien-thoai-tam-trung', 'Điện thoại tầm trung giá tốt, cấu hình ổn'),
('Điện thoại Giá rẻ', 'dien-thoai-gia-re', 'Điện thoại giá rẻ, phù hợp sinh viên');

-- 3. Thêm Thương hiệu
INSERT INTO brands (name, slug, logo) VALUES
('Apple', 'apple', 'apple-logo.png'),
('Samsung', 'samsung', 'samsung-logo.png'),
('Xiaomi', 'xiaomi', 'xiaomi-logo.png'),
('OPPO', 'oppo', 'oppo-logo.png'),
('Vivo', 'vivo', 'vivo-logo.png'),
('Nokia', 'nokia', 'nokia-logo.png');

-- 4. Thêm Sản phẩm
INSERT INTO products (name, slug, category_id, brand_id, price, sale_price, stock, description, short_desc, image, is_featured, is_new) VALUES
('iPhone 15 Pro Max 256GB', 'iphone-15-pro-max-256gb', 1, 1, 34000000, 31900000, 50, 
 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP, màn hình 6.7 inch Super Retina XDR, pin trâu, thiết kế titan cao cấp', 
 'Siêu phẩm mới nhất từ Apple với chip A17 Pro', 'iphone-15-pro-max.jpg', 1, 1),

('Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 1, 2, 30000000, 28500000, 40,
 'Galaxy S24 Ultra với công nghệ AI tích hợp, camera 200MP zoom quang học, S Pen thông minh, pin 5000mAh',
 'Flagship Samsung với AI và camera 200MP', 'samsung-s24-ultra.jpg', 1, 1),

('Xiaomi 14 Pro', 'xiaomi-14-pro', 1, 3, 18000000, 16900000, 60,
 'Xiaomi 14 Pro với chip Snapdragon 8 Gen 3, camera Leica, màn hình 6.73 inch AMOLED 120Hz',
 'Flagship Xiaomi với camera Leica chuyên nghiệp', 'xiaomi-14-pro.jpg', 1, 1),

('iPhone 15', 'iphone-15', 1, 1, 23000000, 21900000, 80,
 'iPhone 15 với Dynamic Island, chip A16, camera 48MP, thiết kế màu sắc trẻ trung',
 'iPhone 15 chính hãng VN/A', 'iphone-15.jpg', 0, 1),

('Samsung Galaxy A55', 'samsung-galaxy-a55', 2, 2, 10000000, 9500000, 100,
 'Galaxy A55 với màn hình Super AMOLED 6.6 inch, pin 5000mAh, camera 50MP',
 'Tầm trung đáng mua nhất từ Samsung', 'samsung-a55.jpg', 0, 1),

('Xiaomi Redmi Note 13 Pro+', 'xiaomi-redmi-note-13-pro-plus', 2, 3, 9000000, 8500000, 120,
 'Redmi Note 13 Pro+ với camera 200MP, pin 5000mAh sạc 120W, màn hình AMOLED 6.67 inch',
 'Giá tốt, cấu hình mạnh', 'redmi-note-13-pro-plus.jpg', 0, 1),

('OPPO Reno 11', 'oppo-reno-11', 2, 4, 11000000, 10500000, 70,
 'OPPO Reno 11 với camera chân dung 32MP, thiết kế mỏng nhẹ, pin 5000mAh',
 'Đẹp - Mỏng - Camera xịn', 'oppo-reno-11.jpg', 0, 1),

('Vivo V30', 'vivo-v30', 2, 5, 12000000, 11500000, 55,
 'Vivo V30 với camera selfie 50MP, thiết kế sang trọng, pin 5000mAh sạc 80W',
 'Vivo V30 - Chuyên gia chụp ảnh selfie', 'vivo-v30.jpg', 0, 1),

('Nokia G22', 'nokia-g22', 3, 6, 3500000, 3200000, 150,
 'Nokia G22 với pin 5050mAh, màn hình 6.5 inch, thiết kế bền bỉ',
 'Điện thoại bền bỉ, pin trâu', 'nokia-g22.jpg', 0, 1),

('Xiaomi Redmi A3', 'xiaomi-redmi-a3', 3, 3, 2500000, 2300000, 200,
 'Redmi A3 với màn hình 6.71 inch, pin 5000mAh, Android 14',
 'Giá rẻ nhất, pin trâu', 'redmi-a3.jpg', 0, 1);

-- 5. Thêm Khách hàng (6 khách hàng tương ứng 6 thành viên Nhóm 5)
INSERT INTO users (email, password, full_name, phone, address) VALUES
('luong@gmail.com',    MD5('luong123'),    'Nguyễn Văn Lượng', '0901000060', '123 Đường Lê Lợi, Quận 1, TP.HCM'),
('luc@gmail.com',      MD5('luc123'),      'Phạm Văn Lực',     '0901000059', '456 Đường Nguyễn Huệ, Quận 2, TP.HCM'),
('phung@gmail.com',    MD5('phung123'),    'Phạm Đình Phùng',  '0901000093', '789 Đường Võ Văn Tần, Quận 3, TP.HCM'),
('binh@gmail.com',     MD5('binh123'),     'Trần Thế Bình',    '0901000012', '321 Đường Trần Hưng Đạo, Quận 5, TP.HCM'),
('an@gmail.com',       MD5('an123'),       'Mai Bình An',      '0901000001', '654 Đường Hai Bà Trưng, Quận 1, TP.HCM'),
('hung@gmail.com',     MD5('hung123'),     'Bùi Lê Quốc Hùng', '0901000043', '987 Đường Cách Mạng Tháng 8, Quận 10, TP.HCM');

-- 6. Thêm Giỏ hàng
INSERT INTO carts (user_id, product_id, quantity) VALUES
(1, 1, 2),
(1, 4, 1),
(2, 5, 1);

-- 7. Thêm Đơn hàng
INSERT INTO orders (user_id, order_code, total_amount, shipping_fee, discount, final_amount, status, payment_method, shipping_address, shipping_phone, shipping_name) VALUES
(1, 'ORD20260120001', 63800000, 30000, 0, 63830000, 'completed', 'cod', '123 Đường Lê Lợi, Quận 1, TP.HCM', '0912345678', 'Nguyễn Văn A'),
(2, 'ORD20260120002', 9500000, 30000, 50000, 9480000, 'shipping', 'banking', '456 Đường Nguyễn Huệ, Quận 2, TP.HCM', '0987654321', 'Trần Thị B'),
(3, 'ORD20260120003', 34000000, 30000, 0, 34030000, 'pending', 'momo', '789 Đường Võ Văn Tần, Quận 3, TP.HCM', '0909876543', 'Lê Thị C');

-- 8. Thêm Chi tiết đơn hàng
INSERT INTO order_details (order_id, product_id, product_name, product_price, quantity, total_price) VALUES
(1, 1, 'iPhone 15 Pro Max 256GB', 31900000, 2, 63800000),
(2, 5, 'Samsung Galaxy A55', 9500000, 1, 9500000),
(3, 1, 'iPhone 15 Pro Max 256GB', 34000000, 1, 34000000);

-- 9. Thêm Đánh giá
INSERT INTO reviews (product_id, user_id, rating, comment) VALUES
(1, 1, 5, 'Sản phẩm tuyệt vời, màn hình đẹp, pin trâu, camera siêu xịn'),
(1, 2, 4, 'Máy đẹp nhưng giá hơi cao'),
(5, 2, 5, 'Điện thoại tầm trung đáng mua nhất'),
(3, 3, 5, 'Camera Leica quá đỉnh, rất hài lòng');

-- 10. Thêm Mã giảm giá
INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, start_date, end_date, usage_limit) VALUES
('SALE50K', 'fixed', 50000, 1000000, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 100),
('SALE10P', 'percent', 10, 2000000, '2026-01-01 00:00:00', '2027-06-30 23:59:59', 50),
('WELCOME20', 'percent', 20, 3000000, '2026-01-01 00:00:00', '2027-03-31 23:59:59', 30),
('XUANKY2026', 'fixed', 100000, 5000000, '2026-01-20 00:00:00', '2027-02-28 23:59:59', 20);

-- =============================================
-- ========== TẠO KHUNG NHÌN SQL HỮU ÍCH =====
-- =============================================

-- View 1: Sản phẩm nổi bật
CREATE VIEW view_featured_products AS
SELECT p.*, c.name AS category_name, b.name AS brand_name,
       (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 1) AS avg_rating
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
WHERE p.status = 1 AND p.is_featured = 1;

-- View 2: Đơn hàng gần đây
CREATE VIEW view_recent_orders AS
SELECT o.*, u.full_name, u.email, u.phone
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC
LIMIT 50;

-- View 3: Thống kê sản phẩm bán chạy
CREATE VIEW view_best_sellers AS
SELECT p.id, p.name, p.image, p.price, p.sale_price,
       SUM(od.quantity) AS total_sold,
       COUNT(DISTINCT od.order_id) AS total_orders
FROM products p
JOIN order_details od ON p.id = od.product_id
JOIN orders o ON od.order_id = o.id
WHERE o.status IN ('completed', 'shipping')
GROUP BY p.id
ORDER BY total_sold DESC
LIMIT 10;

-- View 4: Tổng quan doanh thu
CREATE VIEW view_revenue_stats AS
SELECT 
    DATE(o.created_at) AS order_date,
    COUNT(DISTINCT o.id) AS total_orders,
    SUM(o.final_amount) AS revenue,
    SUM(od.quantity) AS products_sold,
    AVG(o.final_amount) AS avg_order_value
FROM orders o
JOIN order_details od ON o.id = od.order_id
WHERE o.status IN ('completed', 'shipping')
GROUP BY DATE(o.created_at)
ORDER BY order_date DESC;

-- =============================================
-- ========== TẠO THỦ TỤC LƯU TRỮ ============
-- =============================================

-- Procedure 1: Thêm sản phẩm vào giỏ hàng
DELIMITER //
CREATE PROCEDURE sp_add_to_cart(
    IN p_user_id INT,
    IN p_product_id INT,
    IN p_quantity INT
)
BEGIN
    -- Kiểm tra sản phẩm đã có trong giỏ chưa
    IF EXISTS (SELECT 1 FROM carts WHERE user_id = p_user_id AND product_id = p_product_id) THEN
        -- Nếu có thì cập nhật số lượng
        UPDATE carts 
        SET quantity = quantity + p_quantity,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id = p_user_id AND product_id = p_product_id;
    ELSE
        -- Nếu chưa có thì thêm mới
        INSERT INTO carts (user_id, product_id, quantity) 
        VALUES (p_user_id, p_product_id, p_quantity);
    END IF;
END //
DELIMITER ;

-- Procedure 2: Xóa sản phẩm khỏi giỏ hàng
DELIMITER //
CREATE PROCEDURE sp_remove_from_cart(
    IN p_user_id INT,
    IN p_product_id INT
)
BEGIN
    DELETE FROM carts 
    WHERE user_id = p_user_id AND product_id = p_product_id;
END //
DELIMITER ;

-- Procedure 3: Tạo mã đơn hàng tự động
DELIMITER //
CREATE PROCEDURE sp_generate_order_code(OUT new_code VARCHAR(20))
BEGIN
    SET new_code = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 9999), 4, '0'));
END //
DELIMITER ;

-- Procedure 4: Cập nhật tồn kho sau khi đặt hàng
DELIMITER //
CREATE PROCEDURE sp_update_stock_after_order(IN p_order_id INT)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE prod_id INT;
    DECLARE qty INT;
    DECLARE cur CURSOR FOR 
        SELECT product_id, quantity FROM order_details WHERE order_id = p_order_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO prod_id, qty;
        IF done THEN
            LEAVE read_loop;
        END IF;
        UPDATE products SET stock = stock - qty 
        WHERE id = prod_id AND stock >= qty;
    END LOOP;
    CLOSE cur;
END //
DELIMITER ;

-- =============================================
-- ========== TẠO BỘ KÍCH HOẠT ================
-- =============================================

-- Trigger: Tự động tạo mã đơn hàng trước khi insert
DELIMITER //
CREATE TRIGGER trg_before_insert_order 
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_code IS NULL OR NEW.order_code = '' THEN
        SET NEW.order_code = CONCAT('ORD', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 9999), 4, '0'));
    END IF;
END //
DELIMITER ;

-- Trigger: Cập nhật đánh giá trung bình của sản phẩm
DELIMITER //
CREATE TRIGGER trg_after_insert_review 
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    SELECT AVG(rating) INTO avg_rating 
    FROM reviews 
    WHERE product_id = NEW.product_id AND status = 1;
    
    -- Cập nhật vào bảng products (nếu có cột avg_rating)
    -- UPDATE products SET avg_rating = avg_rating WHERE id = NEW.product_id;
END //
DELIMITER ;

-- Ton kho duoc tru trong MBA_ThanhToanController bang transaction va khoa dong.
-- Khong tao trigger tru kho tai day de tranh tru hai lan.

-- =============================================
-- ========== TẠO CHỈ MỤC ======================
-- =============================================

-- Index cho bảng products
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_brand ON products(brand_id);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_products_created ON products(created_at);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_slug ON products(slug);

-- Index cho bảng orders
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created ON orders(created_at);
CREATE INDEX idx_orders_code ON orders(order_code);
CREATE INDEX idx_orders_coupon ON orders(coupon_id);

-- Index cho bảng order_details
CREATE INDEX idx_order_details_order ON order_details(order_id);
CREATE INDEX idx_order_details_product ON order_details(product_id);

-- Index cho bảng carts
CREATE INDEX idx_carts_user ON carts(user_id);
CREATE INDEX idx_carts_product ON carts(product_id);

-- Index cho bảng reviews
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- Index cho bảng users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_phone ON users(phone);

-- =============================================
-- ========== KIỂM TRA DỮ LIỆU ===============
-- =============================================

-- Kiểm tra số lượng bảng
SELECT COUNT(*) AS total_tables 
FROM information_schema.tables 
WHERE table_schema = 'db_phone_shop';

-- Kiểm tra dữ liệu đã insert
SELECT 'Admins' AS table_name, COUNT(*) AS records FROM admins
UNION ALL SELECT 'Categories', COUNT(*) FROM categories
UNION ALL SELECT 'Brands', COUNT(*) FROM brands
UNION ALL SELECT 'Products', COUNT(*) FROM products
UNION ALL SELECT 'Users', COUNT(*) FROM users
UNION ALL SELECT 'Carts', COUNT(*) FROM carts
UNION ALL SELECT 'Orders', COUNT(*) FROM orders
UNION ALL SELECT 'Order Details', COUNT(*) FROM order_details
UNION ALL SELECT 'Reviews', COUNT(*) FROM reviews
UNION ALL SELECT 'Coupons', COUNT(*) FROM coupons;

-- =============================================
-- ========== MỘT SỐ CÂU LỆNH SQL HỮU ÍCH =====
-- =============================================

-- 1. Lấy danh sách sản phẩm mới nhất
SELECT * FROM products WHERE status = 1 ORDER BY created_at DESC LIMIT 10;

-- 2. Lấy sản phẩm theo danh mục
SELECT p.*, c.name AS category_name, b.name AS brand_name
FROM products p
JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
WHERE c.slug = 'dien-thoai-cao-cap' AND p.status = 1;

-- 3. Lấy sản phẩm đang giảm giá
SELECT * FROM products 
WHERE sale_price IS NOT NULL AND sale_price < price AND status = 1;

-- 4. Lấy sản phẩm bán chạy nhất
SELECT p.*, SUM(od.quantity) AS total_sold
FROM products p
JOIN order_details od ON p.id = od.product_id
JOIN orders o ON od.order_id = o.id
WHERE o.status IN ('completed', 'shipping')
GROUP BY p.id
ORDER BY total_sold DESC
LIMIT 10;

-- 5. Thống kê doanh thu theo tháng
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') AS month,
    COUNT(*) AS total_orders,
    SUM(final_amount) AS revenue,
    AVG(final_amount) AS avg_order_value
FROM orders
WHERE status NOT IN ('cancelled')
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;

-- 6. Lấy đánh giá của sản phẩm
SELECT u.full_name, r.rating, r.comment, r.created_at
FROM reviews r
JOIN users u ON r.user_id = u.id
WHERE r.product_id = 1 AND r.status = 1
ORDER BY r.created_at DESC;

-- 7. Kiểm tra mã giảm giá còn hiệu lực
SELECT * FROM coupons 
WHERE code = 'SALE50K' 
  AND status = 1 
  AND NOW() BETWEEN start_date AND end_date 
  AND used_count < usage_limit;

-- 8. Xóa sản phẩm khỏi giỏ hàng
DELETE FROM carts WHERE user_id = 1 AND product_id = 2;

-- 9. Lấy tổng tiền trong giỏ hàng của khách hàng
SELECT u.full_name, 
       SUM(p.sale_price * c.quantity) AS total_cart
FROM carts c
JOIN products p ON c.product_id = p.id
JOIN users u ON c.user_id = u.id
WHERE c.user_id = 1
GROUP BY c.user_id;

-- 10. Cập nhật trạng thái đơn hàng
UPDATE orders 
SET status = 'confirmed', updated_at = CURRENT_TIMESTAMP
WHERE order_code = 'ORD20260120001';
