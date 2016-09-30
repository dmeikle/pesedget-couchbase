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
 * Time: 10:40 AM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Queries;


use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;

class SelectQuery extends AbstractQuery
{

    
    public function buildQuery(Document $document, $bucketName, array $fields, $documentColumns = null,  $isLikeSearch = false, $firstRowOnly = false) {
        //store this in parent for building query filters
        $this->documentColumns = $documentColumns;

        //this is the fields for the columns we want to return
        $this->fields = $fields;

        $this->isLikeSearch = $isLikeSearch;

        $select = 'SELECT ' . implode(',', $fields);



        $select .= ' FROM `' . $bucketName . '`';

       
        $this->parseDirectives();
        $select .= $this->getWhereStatement($document);


        $select .= $this->getGroupBy();
        $select .= $this->getOrderBy();
        $select .= $this->getDirection();

        if($firstRowOnly) {
            $select .= $this->getOffset(self::GET_ITEM_QUERY, $firstRowOnly);
        } else{
            $select .= $this->getOffset(self::GET_ALL_ITEMS_QUERY, $firstRowOnly);
        }


        return $select;
    }
}