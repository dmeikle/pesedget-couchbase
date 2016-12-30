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
 * Date: 12/30/2016
 * Time: 2:27 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Client\Commands;


class SetInactiveCommand  extends AbstractCouchbaseCommand
{

    public function execute($params = array(), $request = array())
    {
        $queryString = "UPDATE " . $this->getBucketName() .  " use keys ('" . $request['id'] . "') SET isActive = '0'";

        $query = \CouchbaseN1qlQuery::fromString($queryString);

        $rows = $this->getBucket()->query($query);

        $this->httpRequest->setAttribute('result', array('success'=>'true', 'code'=> '200'));
    }
}