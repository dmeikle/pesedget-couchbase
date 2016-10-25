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
 * Time: 3:01 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Client\Commands;


use core\commands\AbstractCommand;
use core\commands\POST;
use core\commands\URI;
use core\http\HTTPRequest;
use Gossamer\Pesedget\Database\GossamerDBConnection;
use Gossamer\Pesedget\Database\SQLInterface;
use Gossamer\Pesedget\Extensions\Couchbase\Connections\Bucket;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use libraries\utils\YAMLParser;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;


class AbstractCouchbaseCommand extends AbstractCommand
{

    protected $bucketName;

    private $bucket = null;

    public function __construct(SQLInterface $entity, HTTPRequest &$request, $credentials = null, GossamerDBConnection $connection = null)
    {

        if (!is_null($credentials) && is_null($connection)) {
            error_log(get_class($this));
            error_log("abstractcommand conn is null");
            error_log(__YML_KEY);
            //new requirements - new connection types are now available in pesedget - MSSQL, NoSql...
            //throw an error for now - we'll use it to see where we are dropping the credentials in legacy code
            throw new \Exception('no conn set ' . get_class($this));
            //this is a last ditch effort - we prefer to use the same connection
            //throughout rather than creating a new one each time
            //$this->dbConnection = new DBConnection($credentials);
        } elseif (!is_null($connection)) {

            $this->bucket = $connection->getBucket();
        }
        $this->httpRequest = $request;
        $this->entity = $entity;


    }

    /**
     * executes code specific to the child class
     *
     * @param array     URI params
     * @param array     POST params
     */
    public function execute($params = array(), $request = array())
    {
        throw new \Exception('AbstractCouchbaseCommand::execute() must be overridden');
    }


    protected function getBucket()
    {
        if (is_null($this->bucket)) {
            $connName = $this->httpRequest->getAttribute('NODE_LEVEL_CLIENT_DATABASE');
            $this->bucket = $this->container->get('EntityManager')->getConnection($connName)->getBucket($this->bucketName);
        }

        return $this->bucket;
    }

    protected function getBucketName()
    {
        $config = $this->httpRequest->getAttribute('CLIENT_SERVER_DB_CONFIG');

        if(!is_null($config)) {
            return $config['bucketName'];
        }

        $connName = $this->httpRequest->getAttribute('NODE_LEVEL_CLIENT_DATABASE');

        return $this->container->get('EntityManager')->getConnection($connName)->getCredential('dbName');
    }


    protected function getMasterBucketName() {
        $config = $this->httpRequest->getAttribute('CLIENT_SERVER_DB_CONFIG');

        if(!is_null($config)) {
            return $config['masterBucketName'];
        }
    }


    protected function getSchema(Document $document, $filepath) {
        $loader = new YAMLParser();
        $loader->setFilepath($filepath);
        $config = $loader->loadConfig();

        if(!is_array($config)) {
            throw new ConfigurationNotFoundException($filepath . ' not found');
        }
        if(!array_key_exists($document->getIdentityField(), $config)) {
            throw new KeyNotFoundException($document->getIdentityField() . ' not found in configuration');
        }

        return $config[$document->getIdentityField()];
    }


    protected function resultsToArray($results) {
        if(!is_object($results)) {
            return array();
        }
        return json_decode(json_encode($results->rows),TRUE);
    }


    protected function getFilter(array $params) {
        $retval = '';
        foreach ($params as $key => $value) {
            if($key == 'locale' || strpos($key,'directive::') !== false) {
                continue;
            }
            $retval .= " AND ($key = '$value')";
        }

        return $retval;
    }



    protected function removeRowHeadings(array $result) {
        $retval = array();

        foreach($result as  $row) {
            $retval[] = json_decode(json_encode(current($row)),true);
            unset($row);
        }

        return $retval;
    }
}