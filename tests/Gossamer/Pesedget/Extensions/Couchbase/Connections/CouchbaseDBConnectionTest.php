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
 * Date: 9/26/2016
 * Time: 2:06 PM
 */

namespace tests\Gossamer\Extensions\Pesedget\Couchbase\Connections;


use Gossamer\Pesedget\Extensions\Couchbase\Connections\CouchbaseDBConnection;

class CouchbaseDBConnectionTest extends \tests\BaseTest
{

    public function testGetCluster() {
        $couchbaseConnection = new CouchbaseDBConnection($this->getCredentials());

        $couchbaseConnection->getCouchBaseCluster();
    }


    public function testGetBucket() {
        $couchbaseConnection = new CouchbaseDBConnection($this->getCredentials());

        $couchbaseConnection->getConnection('travel-sample');

    }
    
    public function testGetAirLine() {
        $couchbaseConnection = new CouchbaseDBConnection($this->getCredentials());

        $bucket = $couchbaseConnection->getConnection();

        $result = $bucket->get('airline_10123');

        $this->assertTrue(array_key_exists('value', $result));
    }
}