<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\Brand;

class BrandImportForm extends ImportForm
{
    protected array $allowedColumns = ['name'];

    protected function processRow(array $data, int $rowIndex): ?string
    {
        $name = $data['name'] ?? '';
        if (empty($name)) {
            return 'Cột "name" không được trống.';
        }

        // Skip nếu đã tồn tại
        $exists = Brand::find()->where(['name' => $name])->exists();
        if ($exists) {
            return null;
        }

        $brand = new Brand();
        $brand->name        = $name;
        $brand->description = $data['description'] ?? null;
        $brand->status      = isset($data['status']) && $data['status'] !== ''
            ? (int)$data['status']
            : Brand::STATUS_INACTIVE;

        if (!$brand->save()) {
            return implode(', ', $brand->getFirstErrors());
        }

        return null;
    }
}
