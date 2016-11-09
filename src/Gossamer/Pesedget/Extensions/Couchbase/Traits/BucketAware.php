<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace Gossamer\Pesedget\Extensions\Couchbase\Traits;

/** *
 * Author: dave
 * Date: 11/9/2016
 * Time: 2:13 PM
 */
trait BucketAware
{

    public function getBucket($bucketName = null)
    {
        if(is_null($bucketName)) {
            $bucketName = $this->getCredential('dbName');
        }

        if(!array_key_exists($bucketName, $this->buckets)) {
            $this->buckets[$bucketName] = $this->getCouchBaseCluster()->openBucket($bucketName);
        }

        return $this->buckets[$bucketName];

    }
}