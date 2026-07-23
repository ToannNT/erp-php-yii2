<?php

namespace api\modules\v1\frontend\sitemap\controllers;

use common\models\Article;
use common\models\Brand;
use common\models\Category;
use common\models\Page;
use common\models\Product;
use yii\rest\Controller;

/**
 * Nguồn dữ liệu cho Nuxt Sitemap (@nuxtjs/sitemap `sources`).
 *
 * Mỗi action trả về MẢNG THÔ [{ loc, lastmod }] (KHÔNG bọc ResponseBuilder),
 * để Nuxt tự sinh <urlset> cho từng file .xml tương ứng.
 *
 * Route (khai báo tường minh trong api/config/_urlManager.php):
 *   GET /api/v1/frontend/sitemap/products
 *   GET /api/v1/frontend/sitemap/categories
 *   GET /api/v1/frontend/sitemap/brands
 *   GET /api/v1/frontend/sitemap/articles
 *   GET /api/v1/frontend/sitemap/pages
 *
 * `loc` trả về đường dẫn TƯƠNG ĐỐI; Nuxt tự ghép domain (siteUrl) vào.
 * Đổi các PATH_* dưới đây cho khớp cấu trúc route của frontend Nuxt.
 */
class DefaultController extends Controller
{
    const PATH_PRODUCT  = '/products/';
    const PATH_CATEGORY = '/categories/';
    const PATH_BRAND    = '/brands/';
    const PATH_ARTICLE  = '/articles/';
    const PATH_PAGE     = '/';

    public function verbs(): array
    {
        return [
            'products'   => ['GET'],
            'categories' => ['GET'],
            'brands'     => ['GET'],
            'articles'   => ['GET'],
            'pages'      => ['GET'],
        ];
    }

    public function actionProducts(): array
    {
        $rows = Product::find()
            ->select(['slug', 'updated_at'])
            ->where(['status' => Product::STATUS_ACTIVE])
            ->andWhere(['not', ['slug' => null]])
            ->asArray()
            ->all();
        return $this->buildUrls($rows, self::PATH_PRODUCT);
    }

    public function actionCategories(): array
    {
        $rows = Category::find()
            ->select(['slug', 'updated_at'])
            ->where(['status' => Category::STATUS_ACTIVE])
            ->andWhere(['not', ['slug' => null]])
            ->asArray()
            ->all();
        return $this->buildUrls($rows, self::PATH_CATEGORY);
    }

    public function actionBrands(): array
    {
        $rows = Brand::find()
            ->select(['slug', 'updated_at'])
            ->where(['status' => Brand::STATUS_ACTIVE])
            ->andWhere(['not', ['slug' => null]])
            ->asArray()
            ->all();
        return $this->buildUrls($rows, self::PATH_BRAND);
    }

    public function actionArticles(): array
    {
        $rows = Article::find()
            ->select(['slug', 'updated_at'])
            ->where(['status' => Article::STATUS_ACTIVE])
            ->andWhere(['not', ['slug' => null]])
            ->asArray()
            ->all();
        return $this->buildUrls($rows, self::PATH_ARTICLE);
    }

    public function actionPages(): array
    {
        $rows = Page::find()
            ->select(['slug', 'updated_at'])
            ->where(['status' => Page::STATUS_ACTIVE])
            ->andWhere(['not', ['slug' => null]])
            ->asArray()
            ->all();
        return $this->buildUrls($rows, self::PATH_PAGE);
    }

    /**
     * Chuyển list bản ghi {slug, updated_at} thành list {loc, lastmod} cho Nuxt.
     */
    private function buildUrls(array $rows, string $prefix): array
    {
        $urls = [];
        foreach ($rows as $row) {
            if (empty($row['slug'])) {
                continue;
            }
            $item = ['loc' => $prefix . $row['slug']];
            $lastmod = $this->formatLastmod($row['updated_at'] ?? null);
            if ($lastmod !== null) {
                $item['lastmod'] = $lastmod;
            }
            $urls[] = $item;
        }
        return $urls;
    }

    /**
     * Chuẩn hoá lastmod về "Y-m-d". Hỗ trợ cả int timestamp (Article) lẫn datetime string (Product/Category/...).
     */
    private function formatLastmod($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $timestamp = is_numeric($value) ? (int)$value : strtotime((string)$value);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }
}
