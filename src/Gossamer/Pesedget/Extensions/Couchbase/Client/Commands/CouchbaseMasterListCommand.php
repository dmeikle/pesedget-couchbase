<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/7/2016
 * Time: 11:51 AM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Client\Commands;


class CouchbaseMasterListCommand extends AbstractCouchbaseListCommand
{
    public function execute($params = array(), $request = array())
    {
        $queryString = "SELECT " . $this->getFields() . " FROM `" . $this->getMasterBucketName() .
            "` as " . $this->entity->getClassName() . " WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params) . $this->getOrderBy($params, 'id') .
            $this->getLimit($params);

        $query = \CouchbaseN1qlQuery::fromString($queryString);

        $rows = $this->getBucket()->query($query);

        $this->httpRequest->setAttribute($this->entity->getIdentityField(),  $this->removeRowHeadings($this->resultsToArray($rows)));
        $this->getTotalRowCount($params);
    }

    public function getTotalRowCount($params = array(), $request = array())
    {
        $queryString = "SELECT count('id') as rowCount FROM `" . $this->getMasterBucketName() .
            "` WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params);

        $query = \CouchbaseN1qlQuery::fromString($queryString);

        $rows = $this->getBucket()->query($query);

        $this->httpRequest->setAttribute($this->entity->getIdentityField(). 'Count',  $this->resultsToArray($rows));

    }
}