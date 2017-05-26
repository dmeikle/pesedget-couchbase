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
 * Date: 11/9/2016
 * Time: 1:34 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Listeners;

use core\eventlisteners\AbstractListener;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\KeyNotFoundException;
use Gossamer\Pesedget\Utils\YAMLParser;

class AbstractCouchbaseListener extends AbstractListener
{

    protected $bucket;

    protected function setBucket(\CouchbaseBucket $bucket) {
        $this->bucket = $bucket;
    }

    protected function getBucket() {
        return $this->bucket;
    }

    protected function populateDocument(Document &$document, array $request, $_COMPONENT_FOLDER) {
        $filepath = $_COMPONENT_FOLDER . '/config/schemas.yml';

        $schema = $this->getSchema($document, $filepath);

        //automagically add the new timestamp to the request if it's part of the schema
        if(array_key_exists('lastModified', $schema)) {
            $request['lastModified'] = $this->getTimestamp();
        }

        $document->populate($request, $schema);
    }

    protected function prepare(Document $document, array &$params)
    {
        $this->setDocumentId($document, $params);
        $this->setDocumentType($document, $params);
        $this->setActive($params);
        $this->setUniqueKey($params);
    }

    protected function setUniqueKey(array &$params)
    {
        if (!array_key_exists('uniqueKey', $params)) {
            $params['uniqueKey'] = uniqid();
        }

    }

    protected function setActive(array &$params)
    {
        if (!array_key_exists('isActive', $params)) {
            $params['isActive'] = '1';
        }
    }

    protected function setDocumentType(Document $document, array &$params)
    {
        if (array_key_exists('type', $params)) {
            return;
        }

        $params['type'] = $document->getIdentityField();
    }

    protected function setDocumentId(Document $document, array &$params)
    {
        if (array_key_exists('id', $params) && strlen($params['id']) > 0) {

            return;
        }

        $counter = $this->getBucket()->counter($document->getDocumentKey(), 1, array('initial' => 100));
        $params['id'] = $document->getDocumentKey() . $counter->value;
    }


    protected function resultsToArray($results, $shiftArray = false)
    {
        if (!is_object($results)) {
            return array();
        }
        if ($shiftArray) {
            if (isset($results->rows)) {
                return current(json_decode(json_encode($results->rows), TRUE));
            }
            return current(json_decode(json_encode($results->values), TRUE));
        }
        if (isset($results->rows)) {
            return json_decode(json_encode($results->rows), TRUE);
        }
        return json_decode(json_encode($results->value), TRUE);
    }



    protected function getSchema(Document $document, $filepath)
    {
        $loader = new YAMLParser();
        $loader->setFilepath($filepath);
        $config = $loader->loadConfig();

        if (!is_array($config)) {
            throw new ConfigurationNotFoundException($filepath . ' not found');
        }
        if (!array_key_exists($document->getIdentityField(), $config)) {
            throw new KeyNotFoundException($document->getIdentityField() . ' not found in configuration');
        }

        return $config[$document->getIdentityField()];
    }



    protected function populateSubArray(Document &$document, array $params, Document $subDocument, $key = null)
    {
        //no need to do any work - just go back
        if(!array_key_exists($key, $params) && !($subDocument instanceof  DefaultValuesInterface)) {
            return;
        }
        //define the key
        if(is_null($key)) {
            $key = $subDocument->getClassName().'s';
        }
        //we have defaults in the event nothing exists - let's use them and go back
        if(!array_key_exists($key, $params) && $subDocument instanceof  DefaultValuesInterface) {
            $document->set($key, $subDocument->getDefaults());

            return;
        }

        $this->prepare($subDocument, $params);
        unset($params['id']);
        $this->populateDocument($subDocument, $params[$key]);

        $document->set($key, $subDocument->toArray());
    }
}