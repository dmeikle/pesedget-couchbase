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
 * Time: 3:23 PM
 */

namespace tests\Gossamer\Pesedget\Extensions\Couchbase\Documents;


use Gossamer\Pesedget\Extensions\Couchbase\Documents\Document;

class PopulateNestedTest extends \tests\BaseTest
{

    public function testLoad()
    {
        $document = new Document();

        $document->populateNested($this->getPostedParams(), $this->getSchema());
        print_r($document->toArray());
    }

    private function getPostedParams()
    {
        return array(
            'content' => 'this is a test',
            'Author' => 'Dave',
            'PostImages' => array(

                'filename' => 'test.jpg',
                'isActive' => '1'
            )
        );
    }

    private function getSchema()
    {

        return array
        (
            'fields' => array(
                'id',
                'Author',
                'isActive',
                'content',
                'tags',
                'postDate'
            ),
            'joins' => array(
                array
                (
                    'PostImages' => array
                    (
                        'filename',
                        'isActive'
                    ),

                    'MemberLikes' => array
                    (
                        'memberId',
                        'name'
                    ),

                    'MemberComments' => array
                    (
                        'memberId',
                        'comments',
                        'lastModified',
                        'isActive',
                        'name'
                    )
                )
            )
        );
    }
}