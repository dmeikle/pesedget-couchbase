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
 * Time: 5:18 PM
 */

namespace Gossamer\Pesedget\Extensions\Couchbase\Client\Commands;


class AbstractCouchbaseGetCommand extends AbstractCouchbaseCommand
{
    protected function getFilter(array $params) {
        $retval = '';
        foreach ($params as $key => $value) {
            $retval .= " AND ($key = '$value')";
        }

        return $retval;
    }

}