<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12/29/2016
 * Time: 10:54 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Client\Traits;


use Gossamer\Pesedget\Extensions\Couchbase\Client\Utils\YAMLParser;
use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\ConfigurationNotFoundException;
use Gossamer\Pesedget\Extensions\Couchbase\Exceptions\KeyNotFoundException;

trait LoadSchemaTrait
{



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

}