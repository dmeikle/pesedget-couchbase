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
 * Time: 10:30 AM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Queries;


use Gossamer\Caching\CacheManager;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\QueryTypeMismatchException;

class QueryBuilder
{


    const SAVE_QUERY = 'save';
    const DELETE_QUERY = 'delete';
    const GET_ITEM_QUERY = 'get';
    const GET_ALL_ITEMS_QUERY = 'getall';
    const GET_COUNT_QUERY = 'getcount';
    const PARENT_ONLY = 'parentOnly';
    const CHILD_ONLY = 'childOnly';
    const PARENT_AND_CHILD = 'parentAndChild';

    private $fields = null; //derived from passed in array
    private $fieldNames = array(); //derived from values
    private $values = null;
    private $andFilter = null;
    private $orFilter = null;
    private $offset = 0;
    private $limit = 0;
    private $concatenator = ' AND ';
    private $tableName = '';
    private $primaryKeys = null;
    private $tableColumns = null;
    private $tableI18nColumns = null;
    private $tableColumnTypes = null;
    private $i18nJoin = null;
    private $orderBy = null;
    private $direction = null;
    private $groupBy = null;
    private $dbConnection = null;
    private $joinTables = null;
    private $encodingHandler = null;
    private $queryingI18n = false;
    private $isBulkInsert = false;
    private $isLikeSearch = false;
    
    private $cacheManager = null;
    private $cachePath;

    public function __construct(CacheManager $cacheManager, $cachePath)
    {
        $this->cacheManager = $cacheManager;
        $this->cachePath = $cachePath;
    }

    /**
     * @param array|null $fields
     * used for telling the query builder which fields to be returned
     */
    public function setFields(array $fields = null) {
        $this->fields = $fields;
    }
    
    public function getQuery(Document $document, $bucketName, $queryType = 'getall', $resetParams = true ) {
        $builder = $this->getQueryBuilder($queryType);
        $builder->buildQuery($document, $bucketName, $this->fields, ($queryType == self::GET_ITEM_QUERY));
    }

    private function getQueryBuilder($queryType) {
        switch($queryType) {
            case self::DELETE_QUERY:
                return new DeleteQuery;
            case self::GET_ALL_ITEMS_QUERY:
                return new SelectQuery();
            default:
                throw new QueryTypeMismatchException('Unable to build query with query type specified');
        }
                
    }
}
    
