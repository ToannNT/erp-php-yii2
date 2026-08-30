<?php

use yii\db\Migration;

/**
 * Thêm index cho `product.sku`.
 *
 * Import sản phẩm nhận diện bản ghi đã tồn tại bằng `WHERE sku = ?` cho **mỗi dòng** file Excel.
 * Bảng `product` hiện chỉ có PRIMARY, 2 index khoá ngoại và FULLTEXT `idx_product_fulltext`
 * (name, sku, slug) — FULLTEXT không dùng được cho phép so sánh `=`, nên mỗi dòng import là một
 * lần full table scan. File 500 dòng trên kho 10k sản phẩm là 500 lần quét.
 *
 * Cố tình **KHÔNG** để unique: `sku` của sản phẩm đã xoá mềm (`status = -99`) vẫn nằm trong bảng,
 * unique sẽ khoá luôn sku đó, tạo lại sản phẩm cùng mã là lỗi. MariaDB 10.4 không có partial index
 * / functional key part để loại hàng đã xoá ra khỏi ràng buộc.
 *
 * Class m260827_100100_add_index_sku_product
 */
class m260827_100100_add_index_sku_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-sku-product', 'product', 'sku');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-sku-product', 'product');
    }
}
