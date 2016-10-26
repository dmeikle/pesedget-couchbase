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
 * Date: 10/4/2016
 * Time: 5:17 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Client\Commands;

use core\commands\POST;
use core\commands\URI;
use Gossamer\Pesedget\Database\GossamerDBConnection;
use Gossamer\Pesedget\Database\SQLInterface;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;

class AbstractCouchbaseSaveCommand extends AbstractCouchbaseCommand
{

    public function execute($params = array(), $requestParams = array())
    {

        $this->prepare($this->entity, $requestParams);
        $this->populateDocument($this->entity, $requestParams);

        $id = $requestParams['id'];
        $this->getBucket()->upsert($id, $this->entity->toArray());
        $result = $this->getBucket()->get($id);

        $this->httpRequest->setAttribute($this->entity->getClassName(), json_decode(json_encode($result->value),TRUE));
    }

    protected function populateDocument(Document &$document, array $request) {
        $filepath = __COMPONENT_FOLDER . '/config/schemas.yml';

        $schema = $this->getSchema($document, $filepath);

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


//        try {
//            // Do not override default name, fail if it is exists already, and wait for completion
//            $this->getConnection()->manager()->createN1qlPrimaryIndex('posts', false, false);
//            echo "Primary index has been created\n";
//        } catch (CouchbaseException $e) {
//            printf("Couldn't create index. Maybe it already exists? (code: %d)\n", $e->getCode());
//        }
//

        //  return $this->getConnection()->counter("posts", 1);
    }

    protected function setRandomId(array &$params) {
        if(array_key_exists('id', $params)) {
            return;
        }

        $params['id'] = uniqid();
    }
}