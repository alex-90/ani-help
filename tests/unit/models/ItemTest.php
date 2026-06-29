<?php

namespace tests\unit\models;

use app\models\Item;
use yii\behaviors\TimestampBehavior;

class ItemTest extends \Codeception\Test\Unit
{
    public function testFormatSum()
    {
        verify(Item::formatSum(1234.5))->equals('1 234.50');
        verify(Item::formatSum(0))->equals('0.00');
        verify(Item::formatSum(1000000))->equals('1 000 000.00');
    }

    public function testBeforeValidateSetsCreatedAtForNewRecord()
    {
        $item = new Item();
        verify($item->created_at)->empty();

        $item->beforeValidate();

        verify($item->created_at)->notEmpty();
        // формат "Y-m-d H:i:s"
        verify((bool) preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $item->created_at))->true();
    }

    public function testBeforeValidateKeepsExistingCreatedAt()
    {
        $item = new Item();
        $item->created_at = '2020-01-01 00:00:00';

        $item->beforeValidate();

        verify($item->created_at)->equals('2020-01-01 00:00:00');
    }

    public function testBeforeValidateTrimsAndStripsTags()
    {
        $item = new Item();
        $item->name = '  <b>Сбор</b>  ';
        $item->description = ' <script>alert(1)</script>текст ';

        $item->beforeValidate();

        verify($item->name)->equals('Сбор');
        verify($item->description)->equals('alert(1)текст');
    }

    public function testTimestampBehaviorConfigured()
    {
        $item = new Item();

        $found = false;
        foreach ($item->behaviors() as $behavior) {
            if (($behavior['class'] ?? null) === TimestampBehavior::class) {
                $found = true;
                verify($behavior['createdAtAttribute'])->equals('created_at');
                verify($behavior['updatedAtAttribute'])->equals('updated_at');
                verify(is_callable($behavior['value']))->true();
                // значение должно быть строкой формата datetime
                $value = call_user_func($behavior['value']);
                verify((bool) preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value))->true();
            }
        }

        verify($found)->true();
    }
}
