<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\Category;

class CategoryImportForm extends ImportForm
{
    protected array $allowedColumns = ['name'];

    protected function processRow(array $data, int $rowIndex): ?string
    {
        $name = $data['name'] ?? '';
        if (empty($name)) {
            return 'Cột "name" không được trống.';
        }

        $exists = Category::find()->where(['name' => $name])->exists();
        if ($exists) {
            return null;
        }

        $category = new Category();
        $category->name        = $name;
        $category->description = $data['description'] ?? null;
        $category->status      = isset($data['status']) && $data['status'] !== ''
            ? (int)$data['status']
            : Category::STATUS_INACTIVE;

        if (!$category->save()) {
            return implode(', ', $category->getFirstErrors());
        }

        return null;
    }
}
