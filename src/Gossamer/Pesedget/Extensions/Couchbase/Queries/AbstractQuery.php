<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/** *
 * Author: dave
 * Date: 9/27/2016
 * Time: 10:39 AM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Queries;


use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;

abstract class AbstractQuery
{
    const SAVE_QUERY = 'save';
    const DELETE_QUERY = 'delete';
    const GET_ITEM_QUERY = 'get';
    const GET_ALL_ITEMS_QUERY = 'getall';
    const GET_COUNT_QUERY = 'getcount';

    protected $fields;
    protected $documentColumns;
    protected $limit;
    protected $groupBy;
    protected $orderBy;
    protected $direction;
    protected $offset;
    protected $andFilter;
    protected $isLikeSearch = false;

    public function setFields(array $fields = null)
    {
        $this->fields = $fields;
    }

    public function setFilter(array $filter)
    {
        $this->andFilter = $filter;
    }

    public abstract function buildQuery(Document $document, $bucketName, array $fields, $documentColumns = null, $isLikeSearch = false, $firstRowOnly = false);


    protected function parseDirectives()
    {
        if (is_null($this->andFilter)) {
            return;
        }

        $this->limit = '';
        $this->offset = 0;

        foreach ($this->andFilter as $key => $value) {

            if (strpos($key, 'directive::') === FALSE) {
                continue;
            }

            if ('directive::GROUP_BY' == $key) {
                $this->setGroupBy($this->andFilter['directive::GROUP_BY']);
                unset($this->andFilter['directive::GROUP_BY']);
            }

            if ('directive::ORDER_BY' == $key) {

                $this->setOrderBy($value);
                unset($this->andFilter['directive::ORDER_BY']);
            }
            if ('directive::DIRECTION' == $key) {
                $this->setDirection($value);
                unset($this->andFilter['directive::DIRECTION']);
            }
            if ('directive::LIMIT' == $key) {
                $this->setLimit($this->andFilter['directive::OFFSET'], $this->andFilter['directive::LIMIT']);
                unset($this->andFilter['directive::OFFSET']);
                unset($this->andFilter['directive::LIMIT']);
            }
        }
    }

    protected function getGroupBy()
    {
        return $this->groupBy;
    }

    protected function setGroupBy($groupBy)
    {
        $this->groupBy = $groupBy;
    }

    protected function getOrderBy()
    {
        return $this->orderBy;
    }

    protected function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    protected function getDirection()
    {
        return $this->direction;
    }

    protected function setDirection($direction)
    {
        $this->direction = $direction;
    }

    protected function setLimit($offset, $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }


    protected function getOffset($queryType, $firstRowOnly = false)
    {
        if ($firstRowOnly) {
            return ' LIMIT 1';
        }
        if ($this->limit > 0 && $queryType != self::GET_ALL_ITEMS_QUERY) {
            return ' LIMIT ' . $this->offset . ',' . $this->limit;
        }
        return '';
    }


    protected function getWhereStatement(Document $document)
    {

        if (is_null($this->andFilter) && is_null($this->orFilter)) {

            return '';
        }

        $where = ' WHERE type=\'' . $document->getIdentityField() . '\'';
        $hasFilter = false;

        $andWhere = $this->buildAndWhereFilter();

        //$orWhere = $this->buildOrWhereFilter();

        if (strlen($andWhere) > 0) {
            $where .= $andWhere;
            $hasFilter = true;
        }
//        if (strlen($orWhere) > 0) {
//            if ($hasFilter) {
//
//            }
//            $where .= (($hasFilter) ? ' OR ' : '') . $orWhere;
//            $hasFilter = true;
//        }

        return $where;
    }


    protected function buildAndWhereFilter()
    {

        if (is_null($this->andFilter) || count($this->andFilter) == 0) {

            return '';
        }

        //method has passed in a hard string for filtering
        if (!is_null($this->andFilter) && !is_array($this->andFilter)) {
            return $this->andFilter;
        }

        $where = '';
       
        foreach ($this->andFilter as $key => $val) {

            if (!$this->checkColumnExists($key) || strpos($key, 'directive::') !== false) {

                continue;
            }

            $whereTable = '';

            if ($this->isLikeSearch) {
                $where .= ' AND (`' . $key . '` like \'%' . $val . '%\')';
            } else {
                if ($val == 'null') {
                    $where .= ' AND ( IFNULL(' . $whereTable . '`' . $key . '`, 0) = 0';
                } else {
                    $where .= ' AND (`' . $key . '` = \'' . $val . '\')';
                }
            }
        }

        return strlen($where) > 0 ?  ' AND (' . substr($where, 4) . ')' : '';
    }

    private function checkColumnExists($key)
    {
        if (is_null($this->documentColumns)) {
            //give everything a pass if we didn't specify
            return true;

        }
        return array_key_exists($key, $this->documentColumns);
    }
}