-- CAP NHAT CHUC NANG CHO DATABASE DA NHAP TU BAN CU
-- Chay 1 lan trong phpMyAdmin neu khong muon xoa va nhap lai database.

USE db_phone_shop;

-- Laravel la noi duy nhat tru ton kho. Trigger cu lam ton kho bi tru hai lan.
DROP TRIGGER IF EXISTS trg_after_insert_order_detail;

-- Luu ma giam gia theo don de co the hoan luot su dung khi huy don.
SET @coupon_column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND COLUMN_NAME = 'coupon_id'
);
SET @add_coupon_column = IF(
    @coupon_column_exists = 0,
    'ALTER TABLE orders ADD COLUMN coupon_id INT NULL AFTER user_id',
    'SELECT 1'
);
PREPARE stmt FROM @add_coupon_column;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @coupon_fk_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND CONSTRAINT_NAME = 'fk_orders_coupon'
);
SET @add_coupon_fk = IF(
    @coupon_fk_exists = 0,
    'ALTER TABLE orders ADD CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt FROM @add_coupon_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @review_unique_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'reviews'
      AND INDEX_NAME = 'unique_review_product_user'
);
DELETE newer
FROM reviews AS newer
JOIN reviews AS older
  ON older.product_id = newer.product_id
 AND older.user_id = newer.user_id
 AND older.id < newer.id
WHERE @review_unique_exists = 0;
SET @add_review_unique = IF(
    @review_unique_exists = 0,
    'ALTER TABLE reviews ADD UNIQUE KEY unique_review_product_user (product_id, user_id)',
    'SELECT 1'
);
PREPARE stmt FROM @add_review_unique;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Sua du lieu cu tung ghi sai enum thanh unpaid (neu cot da duoc mo rong truoc do).
UPDATE orders SET payment_status = 'pending'
WHERE payment_status NOT IN ('pending', 'paid');

SELECT 'Cap nhat database thanh cong' AS message;
