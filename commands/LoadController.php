<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Category;
use app\models\Document;
use app\models\Donate;
use app\models\Item;
use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LoadController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex()
    {
        $category = new Category();
        $category->name = 'Люди';
        $category->save();

        $category = new Category();
        $category->name = 'Животные';
        $category->save();



        $user = new User();
        $user->fio = 'alex';
        $user->iin = '1234567890';
        $user->phone = '+77077777777';
        $user->email = 'alex@mail.ru';
        $user->type = 1;
        $user->inactive = 0;
        $password = '123';
        $hash = \Yii::$app->getSecurity()->generatePasswordHash($password);

        $user->password = $hash;
        // $user->auth_key = '';
        $user->created_at = date('Y-m-d H:i:s');
        $r = $user->validate();
        
        // var_dump($user->getErrors());exit;
        // var_dump($r);exit;
        
        $user->save();

        $item = new Item();
        $item->name = 'jhjhkhk';
        $item->description = 'sfvjdsjkgsjbndfjknvjsdf';

        $item->status = 0;
        $item->created_at = date('Y-m-d H:i:s');
        $item->category_id = 1;
        $item->created_user_id = 1;
        $item->sum_need = 1000000;
        $item->sum_current = 0;
        $item->save();

        $donate = new Donate();
        $donate->user_id = 1;
        $donate->item_id = 1;
        $donate->sum = 1000;
        $donate->comment = 'sadsf';
        $donate->created_at = date('Y-m-d H:i:s');
        $donate->save();

        $donate = new Donate();
        // $donate->user_id = 1;
        $donate->item_id = 1;
        $donate->sum = 2000;
        $donate->created_at = date('Y-m-d H:i:s');
        $donate->save();


        $document = new Document();
        $document->title = 'sfjkdskjfg';
        $document->created_at = date('Y-m-d H:i:s');
        $document->type = 0;
        $document->item_id = 1;
        $document->save();


        echo 'OK' . "\n";

        return ExitCode::OK;
    }
}
