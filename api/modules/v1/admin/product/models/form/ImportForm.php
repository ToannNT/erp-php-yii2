<?php

namespace api\modules\v1\admin\product\models\form;

use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\base\Model;
use yii\web\UploadedFile;

abstract class ImportForm extends Model
{
    /** @var UploadedFile */
    public $file;

    protected array $allowedColumns = [];

    public function rules(): array
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => ['xlsx', 'xls'], 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function import(): array
    {
        $result = ['success' => 0, 'skipped' => 0, 'errors' => []];

        $spreadsheet = IOFactory::load($this->file->tempName);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            $result['errors'][] = 'File Excel trống.';
            return $result;
        }

        $header = array_map('strtolower', array_map('trim', array_shift($rows)));
        $columnMap = array_flip($header);
        foreach ($this->allowedColumns as $col) {
            if (!isset($columnMap[$col])) {
                $result['errors'][] = "File Excel thiếu cột bắt buộc: \"{$col}\".";
                return $result;
            }
        }

        $rowIndex = 1;
        foreach ($rows as $row) {
            $rowIndex++;
            $data = [];
            foreach ($columnMap as $field => $letter) {
                $data[$field] = trim((string)($row[$letter] ?? ''));
            }

            if (empty(array_filter($data))) {
                $result['skipped']++;
                continue;
            }

            $error = $this->processRow($data, $rowIndex);
            if ($error) {
                $result['errors'][] = "Dòng {$rowIndex}: {$error}";
            } else {
                $result['success']++;
            }
        }

        return $result;
    }

    abstract protected function processRow(array $data, int $rowIndex): ?string;
}
