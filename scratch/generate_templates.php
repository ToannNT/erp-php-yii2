<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ensure docs directory exists
$docsDir = __DIR__ . '/../docs';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0777, true);
}

// 1. Generate Brand Import Template
$brandSpreadsheet = new Spreadsheet();
$brandSheet = $brandSpreadsheet->getActiveSheet();
$brandSheet->setTitle('Brands');

// Headers
$brandSheet->setCellValue('A1', 'name');
$brandSheet->setCellValue('B1', 'description');
$brandSheet->setCellValue('C1', 'status');

// Example data
$brandSheet->setCellValue('A2', 'Samsung');
$brandSheet->setCellValue('B2', 'Thương hiệu Samsung');
$brandSheet->setCellValue('C2', '1');

$brandSheet->setCellValue('A3', 'Apple');
$brandSheet->setCellValue('B3', 'Thương hiệu Apple');
$brandSheet->setCellValue('C3', '1');

// Style headers
foreach (range('A', 'C') as $col) {
    $brandSheet->getColumnDimension($col)->setAutoSize(true);
}
$brandSheet->getStyle('A1:C1')->getFont()->setBold(true);

$brandWriter = new Xlsx($brandSpreadsheet);
$brandWriter->save($docsDir . '/brand_import_template.xlsx');
echo "Created brand_import_template.xlsx\n";

// 2. Generate Category Import Template
$categorySpreadsheet = new Spreadsheet();
$categorySheet = $categorySpreadsheet->getActiveSheet();
$categorySheet->setTitle('Categories');

// Headers
$categorySheet->setCellValue('A1', 'name');
$categorySheet->setCellValue('B1', 'description');
$categorySheet->setCellValue('C1', 'status');

// Example data
$categorySheet->setCellValue('A2', 'Điện thoại');
$categorySheet->setCellValue('B2', 'Danh mục điện thoại');
$categorySheet->setCellValue('C2', '1');

$categorySheet->setCellValue('A3', 'Laptop');
$categorySheet->setCellValue('B3', 'Danh mục máy tính xách tay');
$categorySheet->setCellValue('C3', '1');

// Style headers
foreach (range('A', 'C') as $col) {
    $categorySheet->getColumnDimension($col)->setAutoSize(true);
}
$categorySheet->getStyle('A1:C1')->getFont()->setBold(true);

$categoryWriter = new Xlsx($categorySpreadsheet);
$categoryWriter->save($docsDir . '/category_import_template.xlsx');
echo "Created category_import_template.xlsx\n";

// 3. Generate Product Import Template
//
// 19 cột cố định + 2 nhóm cột động do ProductImportForm đọc để dựng `additional_data`:
//   - `attr_<nhãn>`  : mỗi cột = 1 dòng thông số, gom thành khối "specs" dạng bảng CKEditor.
//                      Tên cột giữ nguyên chữ hoa/thường vì nó chính là nhãn hiển thị.
//   - `html_<khối>`  : dán nguyên khối HTML soạn sẵn, thành 1 khối cùng tên.
// Thêm/bớt cột `attr_*` thoải mái, không cần sửa code.
$productSpreadsheet = new Spreadsheet();
$productSheet = $productSpreadsheet->getActiveSheet();
$productSheet->setTitle('Products');

$productHeaders = [
    'name', 'sku', 'bar_code', 'category_code', 'category', 'brand_code', 'brand',
    'unit_price', 'sll_price', 'compare_price', 'import_price',
    'weight', 'weight_type', 'dimension', 'short_description', 'description', 'tags',
    'allow_sell', 'status', 'images',
    'attr_Model', 'attr_CPU', 'attr_Memory', 'attr_SIM Slot', 'html_info',
];

$productRows = [
    [
        'Bộ phát WiFi 4G Ruijie ZXECS1110I', '', '', 'Category0000006', '', '', '',
        '3500000', '3300000', '4200000', '2800000',
        '0.5', 'kg', '20x15x5', 'Bộ phát WiFi 4G công nghiệp 1 SIM',
        '<p>Thiết bị phát WiFi từ SIM 4G, phù hợp văn phòng nhỏ.</p>', 'hot,4g',
        '1', '1',
        'https://wifi.com.vn/media/lib/04-06-2025/ruijie-zxecs1110i-mtfi-m5209.jpg',
        'ZXECS1110l', 'Dual core 1.4G', '1GB', '1',
        '<h2><strong>THÔNG SỐ NỔI BẬT</strong></h2><ul><li><strong>Chuẩn mạng:</strong> 4G LTE Cat4</li></ul>',
    ],
    [
        'Access Point WiFi 6 Huawei AP361', 'AP361', '', 'Category0000006', '', 'Brand0000004', '',
        '2100000', '2000000', '2600000', '1750000',
        '0.4', 'kg', '18x18x4', 'Access Point WiFi 6 cho doanh nghiệp nhỏ',
        '', 'new',
        '1', '1',
        '',
        'AP361', '', '512MB', '',
        '',
    ],
];

foreach ($productHeaders as $index => $header) {
    $productSheet->setCellValueByColumnAndRow($index + 1, 1, $header);
}
foreach ($productRows as $rowIndex => $row) {
    foreach ($row as $index => $value) {
        $productSheet->setCellValueExplicitByColumnAndRow(
            $index + 1,
            $rowIndex + 2,
            $value,
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
        );
    }
}

$lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($productHeaders));
for ($i = 1; $i <= count($productHeaders); $i++) {
    $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    // Cột HTML dài, để autosize sẽ giãn ra cả nghìn pixel.
    if (strncmp($productHeaders[$i - 1], 'html_', 5) === 0) {
        $productSheet->getColumnDimension($letter)->setWidth(40);
        continue;
    }
    $productSheet->getColumnDimension($letter)->setAutoSize(true);
}
$productSheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
$productSheet->freezePane('A2');

$productWriter = new Xlsx($productSpreadsheet);
$productWriter->save($docsDir . '/product_import_template.xlsx');
echo "Created product_import_template.xlsx\n";
