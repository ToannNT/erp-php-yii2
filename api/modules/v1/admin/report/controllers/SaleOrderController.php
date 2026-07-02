<?php

namespace api\modules\v1\admin\report\controllers;

use api\modules\v1\admin\report\models\Order;
use api\modules\v1\admin\report\models\SaleOrderSearchProductVariant;
use common\models\PaymentMethod;
use common\models\ProductVariant;
use common\models\User;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use yii\base\InvalidConfigException;
use yii\rest\Controller;
use yii\rest\Serializer;
use yii\web\HttpException;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\report\models\search\SaleOrderProductVariantSearch;
use api\modules\v1\admin\report\models\search\OrderSearch;

class SaleOrderController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => [User::ROLE_STAFF]
                ],
                [
                    'allow' => true,
                    'actions' => ['product-variant', 'export-excel-product-variant'],
                    'roles' => [User::ROLE_SUPPLIER]
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR],
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * @throws HttpException
     */
    public function actionProductVariant(): array
    {
        $dataProvider = (new SaleOrderProductVariantSearch())->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception|InvalidConfigException
     */
    public function actionExportExcelProductVariant()
    {
        $dataProvider = (new SaleOrderProductVariantSearch())->search(Yii::$app->request->queryParams);
        $orderItems = $dataProvider->query->all();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getSheet(0);
        $sheet = $spreadsheet->getActiveSheet();
        $serial = 1;
        $numColumn = 1;
        $numRow = 2;
        $headers = ["STT", "Nhà Cung Cấp", "Mã Sản Phẩm", "Tên Sản Phẩm", "Số Lượng Bán", "Tổng Tiền Hàng", "Giảm Giá", "Hoàn Trả", "Tổng Cộng"];
        $sheet->getStyle("A1:I1")->getFont()->setSize(20)->setBold(true);
        $sheet->getStyle("A2:I2")->getFont()->setBold(true);
        $sheet->mergeCells("A1:I1");
        $sheet->setCellValueByColumnAndRow(1, 1, "Báo cáo doanh thu theo nhà cung cấp");
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($numColumn, $numRow, $header);
            $numColumn++;
        }
        foreach ($orderItems as $orderItem) {
            $total_price_return = $orderItem->getOrderReturnItems()->sum("total_price");
            /**
             * @var SaleOrderSearchProductVariant $orderItem
             */
            $numRow++;
            $sheet->setCellValueByColumnAndRow(1, $numRow, $serial);
            $sheet->setCellValueByColumnAndRow(2, $numRow, $orderItem->getSuppliers()->one()->name);
            $sheet->setCellValueByColumnAndRow(3, $numRow, $orderItem->productVariant->sku);
            $sheet->setCellValueByColumnAndRow(4, $numRow, $orderItem->productVariant->name);
            $sheet->setCellValueByColumnAndRow(5, $numRow, $orderItem->bought ?? 0);
            $sheet->setCellValueByColumnAndRow(6, $numRow, $orderItem->sum_sub_total ?? 0);
            $sheet->setCellValueByColumnAndRow(7, $numRow, $orderItem->total_discount_price ?? 0);
            $sheet->setCellValueByColumnAndRow(8, $numRow, $total_price_return ?? 0);
            $sheet->setCellValueByColumnAndRow(9, $numRow, $orderItem->payment_before_return - $total_price_return);
            $serial++;
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        if (!is_dir("file/exports")) {
            mkdir("file/exports", 0700, "true");
        }
        $filename = "file/exports/export_report_product_variant_overview.xlsx";
        $writer->save($filename);
        return Yii::$app->response->sendFile($filename, "export_report_product_variant_overview.xlsx");
    }

    /**
     * @throws HttpException
     * @throws Exception
     */
    public function actionIndex()
    {
        $order = new OrderSearch();
        $dataProvider = $order->search(Yii::$app->request->queryParams);
        $orders = $dataProvider->query->all();
        $sumQuantity = 0;
        $sumTotalPrice = 0;
        $sumDiscount = 0;
        $sumDeliveryFee = 0;
        $sumPayment = 0;
        $sumReturn = 0;
        $sumCashPayment = 0;
        $sumTransferPayment = 0;
        $sumCardPayment = 0;
        $sumAllSubTotalProduct = 0;
        /**
         * @var Order $order
         */
        foreach ($orders as $order) {
            $sumQuantity += $order->quantity;
            $sumTotalPrice += $order->total_price;
            $sumAllSubTotalProduct += $order->sumSubTotalOrderItems;
            $sumDiscount += $order->discount + $order->sumDiscountPriceOrderItems;
            $sumDeliveryFee += $order->delivery_fee + $order->other_fee;
            $sumPayment += $order->payments - $order->delivery_fee - $order->other_fee - $order->totalPriceReturn;
            $sumReturn += $order->totalPriceReturn;
            $orderPaymentMethods = $order->orderPaymentMethods;
            foreach ($orderPaymentMethods as $orderPaymentMethod) {
                if ($orderPaymentMethod->payment_method_id === PaymentMethod::CASH_PAYMENT) {
                    $sumCashPayment += $orderPaymentMethod->payment;
                } elseif ($orderPaymentMethod->payment_method_id === PaymentMethod::TRANSFER_PAYMENT) {
                    $sumTransferPayment += $orderPaymentMethod->payment;
                } elseif ($orderPaymentMethod->payment_method_id === PaymentMethod::CARD_PAYMENT) {
                    $sumCardPayment += $orderPaymentMethod->payment;
                }
            }
        }
        //        $sumOrder = clone($dataProvider->query);
        //        /**
        //         * @var Query $sumOrder
        //         */
        //        $sumOrder = $sumOrder->select(["SUM(`quantity`) as `sum_quantity`,SUM(`total_price`) as `sum_total_price`,SUM(`discount`) as `sum_discount`, SUM(`payments`) as `sum_payments`"])->one();
        $serializer = new Serializer(['collectionEnvelope' => 'items']);
        return ResponseBuilder::responseJson(true, array_merge($serializer->serialize($dataProvider), [
            "sum_order" => [
                "sum_quantity" => $sumQuantity,
                "sum_total_price" => $sumTotalPrice,
                "sum_discount" => $sumDiscount,
                "sum_delivery_fee" => $sumDeliveryFee,
                "sum_return" => $sumReturn,
                "sum_card_payment" => $sumCardPayment,
                "sum_cash_payment" => $sumCashPayment,
                "sum_transfer_payment" => $sumTransferPayment,
                "sum_payments" => $sumPayment,
                "sum_all_sub_total_product" => $sumAllSubTotalProduct
            ]
        ]));
    }
}
