<?php

use yii\db\Migration;

/**
 * Class m221129_095103_create_table_index
 */
class m221129_095103_create_table_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        // create index for user table
        $this->createIndex(
            'idx-user-username',
            'user',
            'username'
        );
        $this->createIndex(
            'idx-user-email',
            'user',
            'email'
        );
        $this->createIndex(
            'idx-user-access_token',
            'user',
            'access_token'
        );
        // create index for user office table
        $this->createIndex("idx-user_office-user_id",
            "user_office",
            "user_id");
        $this->createIndex("idx-user_office-office_id", "user_office", "office_id");
        // create index for user profile
        $this->createIndex("idx-user_profile-user_id", "user_profile", "user_id");
        // create index for user supplier
        $this->createIndex("idx-user_supplier-user_id", "user_supplier", "user_id");
        $this->createIndex("idx-user_supplier-supplier_id", "user_supplier", "supplier_id");
        // create index for user token
        $this->createIndex("idx-user_token-user_id", "user_token", "user_id");
        // create index for sub_department
        $this->createIndex("idx-department_id-sub_department", "sub_department", "department_id");
        $this->createIndex("idx-user_id-sub_department", "sub_department", "user_id");
        // create index for stocktaking_item
        $this->createIndex("idx-stocktaking_id-stocktaking_item", "stocktaking_item", "stocktaking_id");
        $this->createIndex("idx-product_id-stocktaking_item", "stocktaking_item", "product_id");
        $this->createIndex("idx-product_variant_id-stocktaking_item", "stocktaking_item", "product_variant_id");
        // create index for stocktaking
        $this->createIndex("idx-office_id-stocktaking", "stocktaking", "office_id");
        $this->createIndex("idx-inventory_id-stocktaking", "stocktaking", "inventory_id");
        $this->createIndex("idx-created_by-stocktaking", "stocktaking", "created_by");
        // create index for shipper
        $this->createIndex("idx-created_by-shipper", "shipper", "created_by");
        // create index product variant
        $this->createIndex("idx-product_id-product_variant", "product_variant", "product_id");
        // create index product supplier
        $this->createIndex("idx-product_id-product_supplier", "product_supplier", "product_id");
        $this->createIndex("idx-supplier_id-product_supplier", "product_supplier", "supplier_id");
        // create index product meta
        $this->createIndex("idx-product_id-product_meta", "product_meta", "product_id");
        // create index product inventory
        $this->createIndex("idx-product_id-product_inventory", "product_inventory", "product_id");
        $this->createIndex("idx-product_variant_id-product_inventory", "product_inventory", "product_variant_id");
        $this->createIndex("idx-inventory_id-product_inventory", "product_inventory", "inventory_id");
        // create index product asset
        $this->createIndex("idx-product_id-product_asset", "product_asset", "product_id");
        // create index product
        $this->createIndex("idx-category_id-product", "product", "category_id");
        $this->createIndex("idx-brand_id-product", "product", "brand_id");
        // create index page
        $this->createIndex("idx-create_by-page", "page", "create_by");
        // create index order ship
        $this->createIndex("idx-order_id_order_ship", "order_ship", "order_id");
        $this->createIndex("idx-sender_province_id-order_ship", "order_ship", "sender_province_id");
        $this->createIndex("idx-sender_district_id-order_ship", "order_ship", "sender_district_id");
        $this->createIndex("idx-sender_ward_id-order_ship", "order_ship", "sender_ward_id");
        $this->createIndex("idx-receiver_province_id-order_ship", "order_ship", "receiver_province_id");
        $this->createIndex("idx-receiver_district_id-order_ship", "order_ship", "receiver_district_id");
        $this->createIndex("idx-receiver_ward_id-order_ship", "order_ship", "receiver_ward_id");
        $this->createIndex("idx-shipper_id-order_ship", "order_ship", "shipper_id");
        // create index for order return item
        $this->createIndex("idx-office_id-order_return_item", "order_return_item", "office_id");
        $this->createIndex("idx-inventory_id-order_return_item", "order_return_item", "inventory_id");
        $this->createIndex("idx-order_return_id-order_return_item", "order_return_item", "order_return_id");
        $this->createIndex("idx-product_id-order_return_item", "order_return_item", "product_id");
        $this->createIndex("idx-product_variant_id-order_return_item", "order_return_item", "product_variant_id");
        // create index for order return
        $this->createIndex("idx-client_id-order_return", "order_return", "client_id");
        $this->createIndex("idx-order_id-order_return", "order_return", "order_id");
        $this->createIndex("idx-created_by-order_return", "order_return", "created_by");
        $this->createIndex("idx-office_id-order_return", "order_return", "office_id");
        $this->createIndex("idx-inventory_id-order_return", "order_return", "inventory_id");
        // create index for order payment method
        $this->createIndex("idx-order_id-order_payment_method", "order_payment_method", "order_id");
        $this->createIndex("idx-payment_method_id-order_payment_method", "order_payment_method", "payment_method_id");
        // create index for order order return
        $this->createIndex("idx-order_id-order_order_return_item", "order_order_return", "order_id");
        $this->createIndex("idx-order_return_id-order_order_return_item", "order_order_return", "order_return_id");
        // create index for order item
        $this->createIndex("idx-order_id-order_item", "order_item", "order_id");
        $this->createIndex("idx-product_id-order_item", "order_item", "product_id");
        $this->createIndex("idx-product_variant_id-order_item", "order_item", "product_variant_id");
        // create index for order discount
        $this->createIndex("idx-order_id-order_discount", "order_discount", "order_id");
        $this->createIndex("idx-type_id-order_discount", "order_discount", "type_id");
        // create index for order
        $this->createIndex("idx-office_id-order", "order", "office_id");
        $this->createIndex("idx-inventory_id-order", "order", "inventory_id");
        $this->createIndex("idx-client_id-order", "order", "client_id");
        $this->createIndex("idx-created_by-order", "order", "created_by");
        $this->createIndex("idx-promotion_id-order", "order", "promotion_id");
        // create index for office policy
        $this->createIndex("idx-office_id-office_policy", "office_policy", "office_id");
        // create index for office
        $this->createIndex("idx-province_code-office", "office", "province_code");
        $this->createIndex("idx-district_code-office", "office", "district_code");
        $this->createIndex("idx-ward_code-office", "office", "ward_code");
        $this->createIndex("idx-contact_person_id-office", "office", "contact_person_id");
        // create index for inventory receipt item
        $this->createIndex("idx-receipt_id-inventory_receipt_item", "inventory_receipt_item", "receipt_id");
        $this->createIndex("idx-product_id-inventory_receipt_item", "inventory_receipt_item", "product_id");
        $this->createIndex("idx-product_variant_id-inventory_receipt_item", "inventory_receipt_item", "product_variant_id");
        // create index for inventory receipt
        $this->createIndex("idx-office_id-inventory_receipt", "inventory_receipt", "office_id");
        $this->createIndex("idx-inventory_id-inventory_receipt", "inventory_receipt", "inventory_id");
        $this->createIndex("idx-supplier_id-inventory_receipt", "inventory_receipt", "supplier_id");
        $this->createIndex("idx-created_by-inventory_receipt", "inventory_receipt", "created_by");
        $this->createIndex("idx-owner_id-inventory_receipt", "inventory_receipt", "owner_id");
        // create index for inventory issue item
        $this->createIndex("idx-inventory_issue_id-inventory_issue_item", "inventory_issue_item", "inventory_issue_id");
        $this->createIndex("idx-product_id-inventory_issue_item", "inventory_issue_item", "product_id");
        $this->createIndex("idx-product_variant_id-inventory_issue_item", "inventory_issue_item", "product_variant_id");
        // create index for inventory issue
        $this->createIndex("idx-office_id-inventory_issue", "inventory_issue", "office_id");
        $this->createIndex("idx-inventory_id-inventory_issue", "inventory_issue", "inventory_id");
        $this->createIndex("idx-office_receive_id-inventory_issue", "inventory_issue", "office_receive_id");
        $this->createIndex("idx-inventory_receive_id-inventory_issue", "inventory_issue", "inventory_receive_id");
        $this->createIndex("idx-created_by-inventory_issue", "inventory_issue", "created_by");
        $this->createIndex("idx-order_id-inventory_issue", "inventory_issue", "order_id");
        // create index for inventory history
        $this->createIndex("idx-created_by-inventory_history", "inventory_history", "created_by");
        $this->createIndex("idx-office_id-inventory_history", "inventory_history", "office_id");
        $this->createIndex("idx-inventory_id-inventory_history", "inventory_history", "inventory_id");
        $this->createIndex("idx-product_id-inventory_history", "inventory_history", "product_id");
        $this->createIndex("idx-product_variant_id-inventory_history", "inventory_history", "product_variant_id");
        // create index for inventory
        $this->createIndex("idx-office_id-inventory", "inventory", "office_id");
        $this->createIndex("idx-owner_id-inventory", "inventory", "owner_id");
        // create index department
        $this->createIndex("idx-office_id-department", "department", "office_id");
        $this->createIndex("idx-user_id-department", "department", "user_id");
        // create index customer note
        $this->createIndex("idx-customer_id-customer_note", "customer_note", "customer_id");
        $this->createIndex("idx-created_by-customer_note", "customer_note", "created_by");
        // create index for customer
        $this->createIndex("idx-owner_id-customer", "customer", "owner_id");
        $this->createIndex("idx-province_code-customer", "customer", "province_code");
        $this->createIndex("idx-district_code-customer", "customer", "district_code");
        $this->createIndex("idx-ward_code-customer", "customer", "ward_code");
        // create index for contact customer
        $this->createIndex("idx-contact_id-contact_customer", "contact_customer", "contact_id");
        $this->createIndex("idx-customer_id-contact_customer", "contact_customer", "customer_id");
        // create index for category brand
        $this->createIndex("idx-brand_id-category_brand", "category_brand", "brand_id");
        $this->createIndex("idx-category_id-category_brand", "category_brand", "category_id");
        // create index for category
        $this->createIndex("idx-owner_id-category", "category", "owner_id");
        // create index for brand
        $this->createIndex("idx-group_id-brand", "brand", "group_id");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // drop index for user table
        $this->dropIndex(
            'idx-user-username',
            'user'
        );
        $this->dropIndex(
            'idx-user-email',
            'user'
        );
        $this->dropIndex(
            'idx-user-access_token',
            'user'
        );
        // drop index for user office table
        $this->dropIndex("idx-user_office-user_id",
            "user_office");
        $this->dropIndex("idx-user_office-office_id", "user_office");
        // drop index for user profile
        $this->dropIndex("idx-user_profile-user_id", "user_profile");
        // drop index for user supplier
        $this->dropIndex("idx-user_supplier-user_id", "user_supplier");
        $this->dropIndex("idx-user_supplier-supplier_id", "user_supplier");
        // drop index for user token
        $this->dropIndex("idx-user_token-user_id", "user_token");
        // drop index for sub_department
        $this->dropIndex("idx-department_id-sub_department", "sub_department");
        $this->dropIndex("idx-user_id-sub_department", "sub_department");
        // drop index for stocktaking_item
        $this->dropIndex("idx-stocktaking_id-stocktaking_item", "stocktaking_item");
        $this->dropIndex("idx-product_id-stocktaking_item", "stocktaking_item");
        $this->dropIndex("idx-product_variant_id-stocktaking_item", "stocktaking_item");
        // drop index for stocktaking
        $this->dropIndex("idx-office_id-stocktaking", "stocktaking");
        $this->dropIndex("idx-inventory_id-stocktaking", "stocktaking");
        $this->dropIndex("idx-created_by-stocktaking", "stocktaking");
        // drop index for shipper
        $this->dropIndex("idx-created_by-shipper", "shipper");
        // drop index product variant
        $this->dropIndex("idx-product_id-product_variant", "product_variant");
        // drop index product supplier
        $this->dropIndex("idx-product_id-product_supplier", "product_supplier");
        $this->dropIndex("idx-supplier_id-product_supplier", "product_supplier");
        // drop index product meta
        $this->dropIndex("idx-product_id-product_meta", "product_meta");
        // drop index product inventory
        $this->dropIndex("idx-product_id-product_inventory", "product_inventory");
        $this->dropIndex("idx-product_variant_id-product_inventory", "product_inventory");
        $this->dropIndex("idx-inventory_id-product_inventory", "product_inventory");
        // drop index product asset
        $this->dropIndex("idx-product_id-product_asset", "product_asset");
        // drop index product
        $this->dropIndex("idx-category_id-product", "product");
        $this->dropIndex("idx-brand_id-product", "product");
        // drop index page
        $this->dropIndex("idx-create_by-page", "page");
        // drop index order ship
        $this->dropIndex("idx-order_id_order_ship", "order_ship");
        $this->dropIndex("idx-sender_province_id-order_ship", "order_ship");
        $this->dropIndex("idx-sender_district_id-order_ship", "order_ship");
        $this->dropIndex("idx-sender_ward_id-order_ship", "order_ship");
        $this->dropIndex("idx-receiver_province_id-order_ship", "order_ship");
        $this->dropIndex("idx-receiver_district_id-order_ship", "order_ship");
        $this->dropIndex("idx-receiver_ward_id-order_ship", "order_ship");
        $this->dropIndex("idx-shipper_id-order_ship", "order_ship");
        // drop index for order return item
        $this->dropIndex("idx-office_id-order_return_item", "order_return_item");
        $this->dropIndex("idx-inventory_id-order_return_item", "order_return_item");
        $this->dropIndex("idx-order_return_id-order_return_item", "order_return_item");
        $this->dropIndex("idx-product_id-order_return_item", "order_return_item");
        $this->dropIndex("idx-product_variant_id-order_return_item", "order_return_item");
        // drop index for order return
        $this->dropIndex("idx-client_id-order_return", "order_return");
        $this->dropIndex("idx-order_id-order_return", "order_return");
        $this->dropIndex("idx-created_by-order_return", "order_return");
        $this->dropIndex("idx-office_id-order_return", "order_return");
        $this->dropIndex("idx-inventory_id-order_return", "order_return");
        // drop index for order payment method
        $this->dropIndex("idx-order_id-order_payment_method", "order_payment_method");
        $this->dropIndex("idx-payment_method_id-order_payment_method", "order_payment_method");
        // drop index for order order return
        $this->dropIndex("idx-order_id-order_order_return_item", "order_order_return");
        $this->dropIndex("idx-order_return_id-order_order_return_item", "order_order_return");
        // drop index for order item
        $this->dropIndex("idx-order_id-order_item", "order_item");
        $this->dropIndex("idx-product_id-order_item", "order_item");
        $this->dropIndex("idx-product_variant_id-order_item", "order_item");
        // drop index for order discount
        $this->dropIndex("idx-order_id-order_discount", "order_discount");
        $this->dropIndex("idx-type_id-order_discount", "order_discount");
        // drop index for order
        $this->dropIndex("idx-office_id-order", "order");
        $this->dropIndex("idx-inventory_id-order", "order");
        $this->dropIndex("idx-client_id-order", "order");
        $this->dropIndex("idx-created_by-order", "order");
        $this->dropIndex("idx-promotion_id-order", "order");
        // drop index for office policy
        $this->dropIndex("idx-office_id-office_policy", "office_policy");
        // drop index for office
        $this->dropIndex("idx-province_code-office", "office");
        $this->dropIndex("idx-district_code-office", "office");
        $this->dropIndex("idx-ward_code-office", "office");
        $this->dropIndex("idx-contact_person_id-office", "office");
        // drop index for inventory receipt item
        $this->dropIndex("idx-receipt_id-inventory_receipt_item", "inventory_receipt_item");
        $this->dropIndex("idx-product_id-inventory_receipt_item", "inventory_receipt_item");
        $this->dropIndex("idx-product_variant_id-inventory_receipt_item", "inventory_receipt_item");
        // drop index for inventory receipt
        $this->dropIndex("idx-office_id-inventory_receipt", "inventory_receipt");
        $this->dropIndex("idx-inventory_id-inventory_receipt", "inventory_receipt");
        $this->dropIndex("idx-supplier_id-inventory_receipt", "inventory_receipt");
        $this->dropIndex("idx-created_by-inventory_receipt", "inventory_receipt");
        $this->dropIndex("idx-owner_id-inventory_receipt", "inventory_receipt");
        // drop index for inventory issue item
        $this->dropIndex("idx-inventory_issue_id-inventory_issue_item", "inventory_issue_item");
        $this->dropIndex("idx-product_id-inventory_issue_item", "inventory_issue_item");
        $this->dropIndex("idx-product_variant_id-inventory_issue_item", "inventory_issue_item");
        // drop index for inventory issue
        $this->dropIndex("idx-office_id-inventory_issue", "inventory_issue");
        $this->dropIndex("idx-inventory_id-inventory_issue", "inventory_issue");
        $this->dropIndex("idx-office_receive_id-inventory_issue", "inventory_issue");
        $this->dropIndex("idx-inventory_receive_id-inventory_issue", "inventory_issue");
        $this->dropIndex("idx-created_by-inventory_issue", "inventory_issue");
        $this->dropIndex("idx-order_id-inventory_issue", "inventory_issue");
        // drop index for inventory history
        $this->dropIndex("idx-created_by-inventory_history", "inventory_history");
        $this->dropIndex("idx-office_id-inventory_history", "inventory_history");
        $this->dropIndex("idx-inventory_id-inventory_history", "inventory_history");
        $this->dropIndex("idx-product_id-inventory_history", "inventory_history");
        $this->dropIndex("idx-product_variant_id-inventory_history", "inventory_history");
        // drop index for inventory
        $this->dropIndex("idx-office_id-inventory", "inventory");
        $this->dropIndex("idx-owner_id-inventory", "inventory");
        // drop index department
        $this->dropIndex("idx-office_id-department", "department");
        $this->dropIndex("idx-user_id-department", "department");
        // drop index customer note
        $this->dropIndex("idx-customer_id-customer_note", "customer_note");
        $this->dropIndex("idx-created_by-customer_note", "customer_note");
        // drop index for customer
        $this->dropIndex("idx-owner_id-customer", "customer");
        $this->dropIndex("idx-province_code-customer", "customer");
        $this->dropIndex("idx-district_code-customer", "customer");
        $this->dropIndex("idx-ward_code-customer", "customer");
        // drop index for contact customer
        $this->dropIndex("idx-contact_id-contact_customer", "contact_customer");
        $this->dropIndex("idx-customer_id-contact_customer", "contact_customer");
        // drop index for category brand
        $this->dropIndex("idx-brand_id-category_brand", "category_brand");
        $this->dropIndex("idx-category_id-category_brand", "category_brand");
        // drop index for category
        $this->dropIndex("idx-owner_id-category", "category");
        // drop index for brand
        $this->dropIndex("idx-group_id-brand", "brand");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_095103_create_table_index cannot be reverted.\n";

        return false;
    }
    */
}
