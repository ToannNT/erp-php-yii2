<?php
/**
 * Smart Digitech LLC (c) 2016, All right reverse
 * Fast Boy Marketing CRM Project
 */

namespace common\models\base;

use yii\caching\DbQueryDependency;
use yii\db\Query;
use yii\db\ActiveQuery as BaseActiveQuery;

/**
 * Class ActiveQuery
 *
 * @see ActiveRecord
 *
 * @author Vuong Minh <minhvx@dtsmart.vn>
 * @since 2.0.0
 */
class ActiveQuery extends BaseActiveQuery
{
    /**
     * @var array|null
     */
    private $_tableNameAndAlias;

    /**
     * @inheritdoc
     */
    protected function getTableNameAndAlias()
    {
        if ($this->_tableNameAndAlias === null) {
            return $this->_tableNameAndAlias = $this->getTableNameAndAliasOverride();
        } else {
            return $this->_tableNameAndAlias;
        }
    }

    /**
     * @return static
     */
    public function cacheByTime()
    {
        $dependency = static::createDbQueryDependency($this->getPrimaryTableName());
        $this->cache(0, $dependency);

        return $this;
    }

    /**
     * @param $table
     * @return DbQueryDependency
     */
    public static function createDbQueryDependency($table)
    {
        $query = (new Query)
            ->select(['MAX([[created_at]]) as MCA', 'MAX([[updated_at]]) as MUA', 'COUNT(*) as C'])
            ->from($table);

        return new DbQueryDependency([
            'query' => $query,
            'reusable' => true,
            'method' => 'all'
        ]);
    }

    public function createModels($rows)
    {
        return parent::createModels($rows);
    }

    private function getTableNameAndAliasOverride()
    {
        if (empty($this->from)) {
            $tableName = $this->getPrimaryTableName();
        } else {
            $tableName = '';
            foreach ($this->from as $alias => $tableName) {
                if (is_string($alias)) {
                    return [$tableName, $alias];
                }
                break;
            }
        }

        if (preg_match('/^(.*?)\s+({{\w+}}|\w+)$/', $tableName, $matches)) {
            $alias = $matches[2];
        } else {
            $alias = $tableName;
        }

        return [$tableName, $alias];
    }
}