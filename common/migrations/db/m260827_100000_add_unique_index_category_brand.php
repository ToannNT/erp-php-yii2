<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Thêm unique index (category_id, brand_id) cho bảng nối `category_brand`.
 *
 * Bảng được tạo ở m220511_023523 không có unique/FK nào, nên gọi create 2 lần với cùng cặp
 * category/brand sẽ sinh dòng trùng => list nhãn hiệu của category bị lặp. Form category/brand
 * gửi mảng id nên chỉ cần 1 dòng cho mỗi cặp.
 *
 * Kèm index `brand_id` để tra ngược (brand -> categories) không phải full scan.
 *
 * Class m260827_100000_add_unique_index_category_brand
 */
class m260827_100000_add_unique_index_category_brand extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Dọn dòng trùng trước, giữ lại id nhỏ nhất của mỗi cặp — nếu không thì createIndex sẽ fail.
        $rows = (new Query())
            ->select(["id", "category_id", "brand_id"])
            ->from("category_brand")
            ->orderBy(["id" => SORT_ASC])
            ->all($this->db);

        $seen = [];
        $duplicateIds = [];
        foreach ($rows as $row) {
            $key = $row["category_id"] . "-" . $row["brand_id"];
            if (isset($seen[$key])) {
                $duplicateIds[] = $row["id"];
                continue;
            }
            $seen[$key] = true;
        }
        if ($duplicateIds) {
            echo "    > xoá " . count($duplicateIds) . " dòng category_brand trùng\n";
            $this->delete("category_brand", ["id" => $duplicateIds]);
        }

        $this->createIndex("idx_category_brand_unique", "category_brand", ["category_id", "brand_id"], true);
        $this->createIndex("idx_category_brand_brand_id", "category_brand", "brand_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex("idx_category_brand_brand_id", "category_brand");
        $this->dropIndex("idx_category_brand_unique", "category_brand");
    }
}
