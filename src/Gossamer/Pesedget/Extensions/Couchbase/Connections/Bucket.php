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
 * Time: 11:17 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Connections;

/**
 * Class Bucket
 * @package Gossamer\Pesedget\Extensions\Couchbase\Connections
 *
 * This class is used as a wrapper since the current Couchbase Bucket
 * does not have an accessor to the name of the bucket being referenced.
 * We want to be able to ask 'who are you' without refering to the entity
 * manager because it won't know which one we are holding at that moment.
 *
 * So this class wraps the CouchbaseBucket and keeps a reference to the name
 * so we can ask for it when building N1QL queries.
 */
class Bucket
{

    private $bucketName;

    private $bucket;

    public function __construct(\CouchbaseBucket $bucket, $bucketName = null)
    {
        $this->bucket = $bucket;
        $this->bucketName = $bucketName;
    }

    public function getName() {
        return $this->bucketName;
    }

    public function getBucket() {
        return $this->bucket;
    }
}