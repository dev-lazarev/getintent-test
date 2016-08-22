<?php

/**
 * Created by PhpStorm.
 * User: Lazarev Aleksey
 * Date: 22.08.16
 * Time: 17:37
 */

namespace app\models;

use app\components\Requester;
use app\components\ExchangeRateProvider;

class CbrExchangeRateProvider implements ExchangeRateProvider
{
    private $requester = null;

    public function __construct(Requester $requester)
    {
        $this->requester = $requester;
    }

    public function getRateValues($currencies = 'USD, EUR')
    {

        if (!is_array($currencies)) {
            $currenciesArray = explode(',', $currencies);
        } else {
            $currenciesArray = trim($currencies);
        }
        $currenciesArray = array_map('trim', $currenciesArray);
        $data = $this->requester->http_response($this->getUrl());
        try {
            $file = simplexml_load_string($data);
            $rates = [];
            foreach ($file AS $el) {
                if (in_array(strval($el->CharCode), $currenciesArray)) {
                    $rates[strval($el->CharCode)] = strval($el->Value);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $date = new \DateTime();
        return ['date' => $date->format('Y-m-d H:i:s'), 'rates' => $rates];
    }

    private function getUrl()
    {
        return 'http://www.cbr.ru/scripts/XML_daily.asp';
    }
}