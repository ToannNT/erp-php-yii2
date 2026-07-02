<?php

use common\components\inventory\Product;
use common\components\sharedpref\SharedPreferences;

/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocompletion.
 * Note: To avoid "Multiple Implementations" PHPStorm warning and make autocomplete faster
 * exclude or "Mark as Plain Text" vendor/yiisoft/yii2/Yii.php file
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 * @property trntv\filekit\Storage $fileStorage
 * @property common\components\keyStorage\KeyStorage $keyStorage
 * @property common\components\plivo\Client $plivo
 * @property \common\components\gourl\Client $gourl
 * @property common\components\sharedpref\SharedPreferences $sharedTask
 * @property common\components\sharedpref\SharedPreferences $sharedCreateInventoryReceipt
 * @property common\components\sharedpref\SharedPreferences $sharedUpdateInventoryReceipt
 * @property common\components\sharedpref\SharedPreferences $sharedChangeAddress
 * @property common\components\sharedpref\SharedPreferences $sharedChangeAddressUpdate
 * @property common\components\sharedpref\SharedPreferences $sharedCreateSupplier
 * @property common\components\sharedpref\SharedPreferences $sharedCreateClient
 * @property common\components\sharedpref\SharedPreferences $sharedChangeInfoClient
 * @property common\components\sharedpref\SharedPreferences $sharedChangeInfoClientUpdate
 * @property common\components\sharedpref\SharedPreferences $sharedCreateStocktaking
 * @property common\components\sharedpref\SharedPreferences $sharedUpdateStocktaking
 * @property common\components\sharedpref\SharedPreferences $sharedCreateVat
 * @property common\components\sharedpref\SharedPreferences $sharedUpdateVat
 * @property common\components\sharedpref\SharedPreferences $sharedCreateDiscount
 * @property common\components\sharedpref\SharedPreferences $sharedUpdateDiscount
 * @property common\components\sharedpref\SharedPreferences $sharedCreateDeliveryFee
 * @property common\components\sharedpref\SharedPreferences $sharedUpdateDeliveryFee
 * @property common\components\sharedpref\SharedPreferences $shareCreateProduct
 * @property common\components\sharedpref\SharedPreferences $sharedCreateDiscountInventoryReceipt
 * @property common\components\sharedpref\SharedPreferences $sharedUpdateDiscountInventoryReceipt
 * @property common\components\sharedpref\SharedPreferences $sharedCreateCostInventoryReceipt
 * @property common\components\sharedpref\SharedPreferences $sharedUpdateCostInventoryReceipt
 * @property common\components\helperDb\BaseDatabase $helperDb
 * @property yii\web\UrlManager $urlManagerFrontend UrlManager for frontend application.
 * @property yii\web\UrlManager $urlManagerBackend UrlManager for backend application.
 * @property yii\web\UrlManager $urlManagerStorage UrlManager for storage application.
 * @property trntv\glide\components\Glide $glide
 * @property trntv\bus\CommandBus $commandBus
 * @property \common\components\inventory\History $inventoryHistory
 * @property Product $productInventory
 * @property common\components\log\BuildLogDbTarget $build_log
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * Class WebApplication
 * Include only Web application related components here
 *
 * @property User $user User component.
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 * Include only Console application related components here
 */
class ConsoleApplication extends yii\console\Application
{
}

/**
 * User component
 * Include only Web application related components here
 *
 * @property \common\models\User $identity User model.
 * @method \common\models\User getIdentity() returns User model.
 */
class User extends \yii\web\User
{
}
