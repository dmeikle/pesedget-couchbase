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


    public function getConnection($bucketName = null)
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


/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/16/2016
 * Time: 4:23 PM
 */
/*
namespace Gossamer\Pesedget\Collections;


use Gossamer\Pesedget\Entities\AbstractEntity;
use Monolog\Logger;
use triagens\ArangoDb\Collection;
use triagens\ArangoDb\CollectionHandler;
use triagens\ArangoDb\Connection;
use triagens\ArangoDb\ConnectionOptions;
use triagens\ArangoDb\DocumentHandler;
use triagens\ArangoDb\ConnectException;
use triagens\ArangoDb\UpdatePolicy;
use Gossamer\Pesedget\Database\GossamerDBConnection;


class ArangoDBConnection  implements NoSQLConnectionInterface, GossamerDBConnection
{

    protected $credentials;

    protected $logger = null;

    protected $conn = null;
    private $rowCount = 0;

    public function __construct(array $credentials = null) {
        if (!is_null($credentials)) {
            $this->credentials = $credentials;
        } else {
            //TODO: uh-oh... no db credentials exist. This will cause a known bug
            //since at design time this EntityManager only knows about SQL
            //based connections...
            //$this->credentials = EntityManager::getInstance()->getCredentials();
            throw new ConnectException('no credentials specified for ArangoDBConnection::_construct(array)');
        }
    }

    public function __destruct() {
        $this->logger = null;
        $this->conn = null;
    }


    public function getRowCount() {
        return $this->rowCount;
    }

    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    public function beginTransaction() {
        $this->getConnection();
    }

    public function commitTransaction() {
        $this->getConnection();
    }

    public function rollbackTransaction() {
        $this->getConnection();
    }

    public function getConnection() {
        if (is_null($this->conn) ) {
            $this->conn = new Connection($this->getCredentials());
            if (is_bool($this->conn)) {
                throw new \Exception('unable to connect to ArangoDb with provided credentials');
            }
        }

        return $this->conn;
    }


    public function getCollectionHandler(Connection $connection = null) {
        if(is_null($connection)) {
            return new CollectionHandler($this->getConnection());
        }

        return new CollectionHandler($connection);
    }

    public function getCredentials() {
        return array(
            ConnectionOptions::OPTION_DATABASE      => $this->credentials['database'],
            // server endpoint to connect to
            ConnectionOptions::OPTION_ENDPOINT => $this->credentials['endpoint'],
            // authorization type to use (currently supported: 'Basic')
            ConnectionOptions::OPTION_AUTH_TYPE => $this->credentials['AuthType'],
            // user for basic authorization
            ConnectionOptions::OPTION_AUTH_USER => $this->credentials['AuthUser'],
            // password for basic authorization
            ConnectionOptions::OPTION_AUTH_PASSWD => $this->credentials['AuthPasswd'],
            // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
            ConnectionOptions::OPTION_CONNECTION => $this->credentials['Connection'],
            // connect timeout in seconds
            ConnectionOptions::OPTION_TIMEOUT => $this->credentials['timeout'],
            // whether or not to reconnect when a keep-alive connection has timed out on server
            ConnectionOptions::OPTION_RECONNECT => $this->credentials['Reconnect'],
            // optionally create new collections when inserting documents
            ConnectionOptions::OPTION_CREATE => $this->credentials['createCollection'],
            // optionally create new collections when inserting documents
            ConnectionOptions::OPTION_UPDATE_POLICY => UpdatePolicy::LAST,
        );

    }
}

*/