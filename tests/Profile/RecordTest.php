<?php

use \XhprofFlamegraph\Profile\Record;

class RecordTest extends PHPUnit_Framework_TestCase
{

    public function testRecordParent()
    {
        $record = new Record();
        $record->parent_function = null;
        $this->assertFalse($record->hasParent());

        $record = new Record();
        $record->parent_function = 'hogehoge';
        $this->assertTrue($record->hasParent());
    }
}
