<?php

namespace common\components\sharedpref;

use common\components\sharedpref\storage\StorageInterface;
use common\components\sharedpref\models\SharedItemInterface;
use Yii;
use yii\base\InvalidConfigException;

class SharedPreferences extends \yii\base\Component
{

    public $storageName = "sharedPref";
    const ITEM_PRODUCT = 'common\components\sharedpref\models\SharedItemInterface';
    /**
     * Override this to provide custom (e.g. database) storage for cart data
     *
     * @var string|StorageInterface
     */
    public $storageClass = 'common\components\sharedpref\storage\SessionStorage';

    /**
     * @var array cart items
     */
    protected $items;
    /**
     * @var StorageInterface
     */
    private $_storage;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->clear(false);
        try {
            $this->setStorage(Yii::createObject($this->storageClass));
            $this->storage->setName($this->storageName);
            $this->items = $this->storage->load($this);
        } catch (InvalidConfigException $e) {
        }
        $this->items = $this->storage->load($this);
    }

    /**
     * Assigns cart to logged in user
     *
     * @param string
     * @param string
     */
    public function reassign($sessionId, $userId)
    {
        if (get_class($this->getStorage()) === 'yii2mod\cart\storage\DatabaseStorage') {
            if (!empty($this->items)) {
                $storage = $this->getStorage();
                $storage->reassign($sessionId, $userId);
                self::init();
            }
        }
    }

    /**
     * Delete all items from the cart
     *
     * @param bool $save
     *
     * @return $this
     */
    public function clear($save = true): self
    {
        $this->items = [];
        $save && $this->storage->save($this);
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface
    {
        return $this->_storage;
    }

    /**
     * @param mixed $storage
     */
    public function setStorage($storage)
    {
        $this->_storage = $storage;
    }

    /**
     * Add an item to the cart
     *
     * @param models\SharedItemInterface $element
     * @param bool $save
     *
     * @return $this
     */
    public function add(SharedItemInterface $element, $save = true): self
    {
        $this->addItem($element);
        $save && $this->storage->save($this);
        return $this;
    }

    /**
     * @param SharedItemInterface $item
     */
    protected function addItem(SharedItemInterface $item)
    {
        $uniqueId = $item->getUniqueId();
        $this->items[$uniqueId] = $item;
    }

    /**
     * Removes an item from the cart
     *
     * @param string $uniqueId
     * @param bool $save
     *
     * @return $this
     * @throws \yii\base\InvalidParamException
     *
     */
    public function remove($uniqueId, $save = true): self
    {
        if (!isset($this->items[$uniqueId])) {
            throw new InvalidParamException('Item not found');
        }
        unset($this->items[$uniqueId]);
        $save && $this->storage->save($this);
        return $this;
    }

    /**
     * @param string $itemType If specified, only items of that type will be counted
     *
     * @return int
     */
    public function getCount($itemType = null): int
    {
        return count($this->getItems($itemType));
    }

    /**
     * Returns all items of a given type from the cart
     *
     * @param string $itemType One of self::ITEM_ constants
     *
     * @return SharedItemInterface[]
     */
    public function getItems($itemType = null): array
    {
        $items = $this->items;
        if (!is_null($itemType)) {
            $items = array_filter(
                $items,
                function ($item) use ($itemType) {
                    /* @var $item SharedItemInterface */
                    return is_a($item, $itemType);
                }
            );
        }
        return $items;
    }

    public function updateItem($uniqueId, $model = null, $save = true)
    {
        if (null != $model) {
            $this->items[$uniqueId] = $model;
            $save && $this->storage->save($this);
        } else {
            unset($this->items[$uniqueId]);
            $save && $this->storage->save($this);
        }
    }

    public function reload()
    {
        $this->items = $this->storage->load($this);
    }
    public function save()
    {
        $this->storage->save($this);
    }

    public function getItem($id = null)
    {
        $items = $this->items;
        $neededObject = array_filter(
            $items,
            function ($e) use (&$id) {
                return $e->id == $id;
            }
        );
        return count($neededObject) == 1 ? $neededObject[$id] : null;
    }

    /**
     * Finds all items of type $itemType, sums the values of $attribute of all models and returns the sum.
     *
     * @param string $attribute
     * @param string|null $itemType
     *
     * @return int
     */
    public function getAttributeTotal($attribute, $itemType = null): int
    {
        $sum = 0;
        foreach ($this->getItems($itemType) as $model) {
            $sum += $model->{$attribute};
        }
        return $sum;
    }
}

