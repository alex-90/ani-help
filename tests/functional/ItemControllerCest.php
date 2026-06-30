<?php

use app\models\Item;
use app\models\ItemStatus;
use yii\helpers\Url;

class ItemControllerCest
{
    public function _fixtures()
    {
        return [
            'roles'    => \tests\fixtures\UserRoleFixture::class,
            'users'    => \tests\fixtures\UserFixture::class,
            'statuses' => \tests\fixtures\ItemStatusFixture::class,
            'reasons'  => \tests\fixtures\ReasonFixture::class,
            'items'    => \tests\fixtures\ItemFixture::class,
        ];
    }

    // ---- публикация ------------------------------------------------------

    public function ownerCanPublishItem(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->sendAjaxPostRequest(Url::to(['/item/toggle-published', 'id' => 1]));

        $I->seeRecord(Item::class, ['id' => 1, 'published' => 1]);
    }

    public function publishRequiresPost(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        // GET-запрос к POST-only экшену должен быть запрещён (405)
        $I->amOnRoute('item/toggle-published', ['id' => 1]);
        $I->seeResponseCodeIs(405);
    }

    public function strangerCannotPublishForeignItem(\FunctionalTester $I)
    {
        // user 1 пытается опубликовать чужой сбор (владелец — user 2)
        $I->amLoggedInAs(1);
        $I->sendAjaxPostRequest(Url::to(['/item/toggle-published', 'id' => 3]));
        $I->seeResponseCodeIs(403);

        $I->seeRecord(Item::class, ['id' => 3, 'published' => 0]);
    }

    // ---- остановка сбора с причиной --------------------------------------

    public function ownerCanStopItemWithReasonFromList(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->sendAjaxPostRequest(Url::to(['/item/stop', 'id' => 2]), ['reason_id' => 1]);

        $I->seeRecord(Item::class, [
            'id' => 2,
            'status_id' => ItemStatus::STATUS_CLOSED,
            'reason_id' => 1,
        ]);
    }

    public function ownerCanStopItemWithReasonText(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->sendAjaxPostRequest(Url::to(['/item/stop', 'id' => 2]), ['reason_text' => 'Передумал']);

        $I->seeRecord(Item::class, [
            'id' => 2,
            'status_id' => ItemStatus::STATUS_CLOSED,
            'reason_text' => 'Передумал',
        ]);
    }

    public function strangerCannotStopForeignItem(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->sendAjaxPostRequest(Url::to(['/item/stop', 'id' => 3]), ['reason_text' => 'x']);
        $I->seeResponseCodeIs(403);
    }

    // ---- дозапись в опубликованный сбор ----------------------------------

    // ---- редактирование --------------------------------------------------

    public function ownerCanOpenUpdateForUnpublishedItem(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('item/update', ['id' => 1]);
        $I->seeResponseCodeIs(200);
    }

    public function publishedItemOpensRestrictedUpdate(\FunctionalTester $I)
    {
        // опубликованный сбор открывается в форме (ограниченное редактирование), а не 403
        $I->amLoggedInAs(1);
        $I->amOnRoute('item/update', ['id' => 2]);
        $I->seeResponseCodeIs(200);
    }

    public function publishedItemEditChangesOnlySumAndAppendsText(\FunctionalTester $I)
    {
        $old = \app\models\Item::findOne(2)->description;

        $I->amLoggedInAs(1);
        // пытаемся сменить имя (нельзя) + сумму (можно) + дозапись текста
        $I->sendAjaxPostRequest(Url::to(['/item/update', 'id' => 2]), [
            'Item' => [
                'name' => 'HACKED',
                'sum_need' => '2222.00',
                'appendText' => 'Дополнение',
            ],
        ]);

        $item = \app\models\Item::findOne(2);
        // имя не изменилось, сумма обновилась
        $I->assertSame('Published item', $item->name);
        $I->assertSame('2222.00', $item->sum_need);
        // старый текст сохранён, новый дописан после пометки UPD
        $I->assertStringStartsWith($old, $item->description);
        $I->assertStringContainsString('UPD', $item->description);
        $I->assertStringContainsString('Дополнение', $item->description);
        // публикация и статус не тронуты
        $I->seeRecord(Item::class, ['id' => 2, 'published' => 1, 'status_id' => 1]);
    }

    public function strangerCannotEditForeignItem(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('item/update', ['id' => 3]);
        $I->seeResponseCodeIs(403);
    }

    // ---- удаление запрещено ----------------------------------------------

    public function deleteIsForbidden(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->sendAjaxPostRequest(Url::to(['/item/delete', 'id' => 1]));
        $I->seeResponseCodeIs(403);

        $I->seeRecord(Item::class, ['id' => 1]);
    }

    // ---- мои сборы -------------------------------------------------------

    public function ownItemsShowsOnlyOwnItems(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('item/own-items');
        $I->seeResponseCodeIs(200);
        $I->see('Draft item');
        $I->see('Published item');
        $I->dontSee('Foreign item');
    }
}
