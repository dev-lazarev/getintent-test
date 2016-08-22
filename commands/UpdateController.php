<?php


namespace app\commands;


use app\components\Requester;
use app\models\CbrExchangeRateProvider;
use yii\console\Controller;
use app\models\Rates;


class UpdateController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionStart()
    {

        $rates = new Rates();
        $requester = new Requester();
        $provider = new CbrExchangeRateProvider($requester);
        $rates->updateFromProvider($provider);
        echo 'update complete' . PHP_EOL;
    }
}
