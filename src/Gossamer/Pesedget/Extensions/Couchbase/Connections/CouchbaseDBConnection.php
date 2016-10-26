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
 * Time: 1:24 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Connections;


use Gossamer\Pesedget\Collections\NoSQLConnectionInterface;
use Gossamer\Pesedget\Database\GossamerDBConnection;
use Gossamer\Pesedget\Couchbase\Exceptions\KeyNotFoundException;

class CouchbaseDBConnection implements NoSQLConnectionInterface, GossamerDBConnection
{
    protected $credentials;

    protected $logger = null;

    protected $cluster = null;

    protected $buckets = array();

    private $rowCount = 0;

    public function __construct(array $credentials = null)
    {
        if (!is_null($credentials)) {
            $this->credentials = $credentials;
        } else {
            //TODO: uh-oh... no db credentials exist. This will cause a known bug
            //since at design time this EntityManager only knows about SQL
            //based connections...
            //$this->credentials = EntityManager::getInstance()->getCredentials();
            throw new ConnectException('no credentials specified for CouchbaseDBConnection::_construct(array)');
        }
    }

    public function __destruct()
    {
        $this->logger = null;
        $this->conn = null;
    }


    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }


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


    public function getCouchBaseCluster() {
        if (is_null($this->cluster)) {
            try {
                $remoteUrl = $this->getCredential('host');
                $this->cluster = new \CouchbaseCluster('couchbase://' . $remoteUrl);
                if (is_bool($this->cluster)) {
                    throw new \Exception('unable to connect to Couchbase with provided credentials');
                }
            } catch (\Exception $e) {
                throw new \Exception("unable to connect to Couchbase with provided credentials\r\n" . $e->getMessage());
            }
        }

        return $this->cluster;
    }

    public function getCredential($key)
    {
        if (!array_key_exists($key, $this->credentials)) {
            throw new KeyNotFoundException("Unable to locate $key in credentials provided");
        }

        return $this->credentials[$key];
    }
}
