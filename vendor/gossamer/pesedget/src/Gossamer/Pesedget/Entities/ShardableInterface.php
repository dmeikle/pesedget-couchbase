<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Entities;

/**
 * sharding is the separation of tables across multiple databases
 * 
 * @author davem
 */
interface ShardableInterface {
    
    public function getDbName();
    
    public function setDbName($dbName);
}
