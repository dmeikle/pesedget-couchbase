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
 * Date: 10/6/2016
 * Time: 12:34 AM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Client\Commands;


class AbstractCouchbaseListCommand extends AbstractCouchbaseCommand
{


    protected function getTotalRowCount(array $params) {
        $queryString = "SELECT count(id) as rowCount FROM `BHDB6` WHERE type ='" . $this->entity->getIdentityField() .
            "' AND isActive = '1'";


        $query = \CouchbaseN1qlQuery::fromString($queryString);
        $rows = $this->getBucket()->query($query);

        $this->httpRequest->setAttribute( $this->entity->getIdentityField() . 'Count', $this->resultsToArray($rows));
    }


    protected function getOrderBy(array &$params)
    {
        $orderBy = '';

        if (array_key_exists('directive::ORDER_BY', $params)) {
            $column = $params['directive::ORDER_BY'];

            $orderBy = ' ORDER BY ' . $column;
            unset($params['directive::ORDER_BY']);
            if (array_key_exists('directive::DIRECTION', $params)) {
                $orderBy .= ' ' . $params['directive::DIRECTION'];
                unset($params['directive::DIRECTION']);
            }
        }

        return $orderBy;
    }


    protected function getLimit(array &$params)
    {
        $limit = '';
        $offset = '';

        if (array_key_exists('directive::OFFSET', $params)) {
            $offset = ' OFFSET ' . intval($params['directive::OFFSET']);
            unset($params['directive::OFFSET']);
        }

        if (array_key_exists('directive::LIMIT', $params)) {
            $limit = ' LIMIT ' . intval($params['directive::LIMIT']);
            unset($params['directive::LIMIT']);
        }

        return $limit . $offset;
    }
}