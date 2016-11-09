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

class AbstractCouchbaseListener extends AbstractListener
{


    protected function populateDocument(Document &$document, array $request) {
        $filepath = __COMPONENT_FOLDER . '/config/schemas.yml';

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
}