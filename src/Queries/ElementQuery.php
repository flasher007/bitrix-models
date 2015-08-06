<?php

namespace Arrilot\BitrixModels\Queries;

use Arrilot\BitrixModels\Models\ElementModel;

class ElementQuery extends BaseQuery
{
    /**
     * Query sort.
     *
     * @var array
     */
    protected $sort = ['SORT' => 'ASC'];

    /**
     * Query group by.
     *
     * @var array
     */
    protected $groupBy = false;

    /**
     * Iblock id.
     *
     * @var int
     */
    protected $iblockId;

    /**
     * Constructor.
     *
     * @param object $object
     * @param string $modelName
     * @param int $iblockId
     */
    public function __construct($object, $modelName, $iblockId)
    {
        $this->object = $object;
        $this->modelName = $modelName;
        $this->iblockId = $iblockId;

        $this->filter = ['IBLOCK_ID' => $iblockId];
    }

    /**
     * Setter for groupBy.
     *
     * @param $value
     *
     * @return $this
     */
    public function groupBy($value)
    {
        $this->groupBy = $value;

        return $this;
    }

    /**
     * Setter for filter.
     *
     * @param array $filter
     *
     * @return $this
     */
    public function filter(array $filter = [])
    {
        $this->filter = $filter;
        $this->filter['IBLOCK_ID'] = $this->iblockId;

        return $this;
    }

    /**
     * Get item by its id.
     *
     * @param int $id
     *
     * @return ElementModel|false
     */
    public function getById($id)
    {
        return parent::getById($id);
    }

    /**
     * Get list of items.
     *
     * @return ElementModel[]
     */
    public function getList()
    {
        $select = $this->fieldsMustBeSelected() ? [] : $this->prepareSelectForGetList();

        $items = [];
        $rsItems = $this->object->getList($this->sort, $this->filter, $this->groupBy, $this->navigation, $select);
        while($obItem = $rsItems->getNextElement()) {
            $arItem = $obItem->getFields();
            if ($this->propsMustBeSelected()) {
                $arItem['PROPERTIES'] = $obItem->getProperties();
                $this->setPropertyValues($arItem);
            }

            /** @var ElementModel $item */
            $item = new $this->modelName;
            $item->fill($arItem);

            $this->addUsingKeyBy($items, $item);
        }

        return $items;
    }

    /**
     * Get count of elements that match $filter.
     *
     * @return int
     */
    public function count()
    {
        return $this->object->getList(false, $this->filter, []);
    }

    /**
     * Set $field['PROPERTY_VALUES'] from $field['PROPERTIES'].
     *
     * @param array $fields
     *
     * @return null
     */
    protected function setPropertyValues(&$fields)
    {
        if (empty($fields) || empty($fields['PROPERTIES'])) {
            return;
        }

        foreach ($fields['PROPERTIES'] as $code => $prop) {
            $fields['PROPERTY_VALUES'][$code] = $prop['VALUE'];
        }

        return;
    }
}
