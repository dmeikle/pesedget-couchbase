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
 * Date: 9/27/2016
 * Time: 11:49 AM
 */

namespace tests\Gossamer\Pesedget\Extensions\Couchbase\Queries;


use Gossamer\Pesedget\Extensions\Couchbase\Queries\SelectQuery;
use tests\Gossamer\Pesedget\Extensions\Couchbase\Documents\Member;

class SelectQueryTest extends \tests\BaseTest
{

    public function testSelect() {
        $builder = new SelectQuery();
        $filter = array(
            'firstname' => 'dave',
            'lastname' => 'meikle'
        );
        $fields = array('*');
        $builder->setFilter($filter);
        $member = new Member();
        $query = $builder->buildQuery($member, 'mybucket', $fields);
        echo $query;
    }
}