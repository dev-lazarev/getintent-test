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

class YahhoExchangeRateProvider implements ExchangeRateProvider
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
            $currenciesArray = $currencies;
        }
        $currenciesArray = array_map('trim', $currenciesArray);
        $data = $this->requester->http_response($this->getUrl($currenciesArray));
        try {
            $result = json_decode($data, true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $date = new \DateTime($result['query']['created']);
        $rates = [];
        foreach ($result['query']['results']['rate'] as $currency) {
            $id = str_replace('RUB', '', $currency['id']);
            $rates[$id] = $currency['Rate'];
        }
        return ['date' => $date->format("Y-m-d H:i:s"), 'rates' => $rates];
    }

    private function getUrl($currencies = ['USD', 'EUR'])
    {

        $string = '';
        if (!empty($currencies)) {
            foreach ($currencies as $currency) {
                $string .= trim($currency) . 'RUB,';
            }
        }
        $string = rtrim($string, ',');
        $url = "https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+%22{$string}%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
        return $url;
    }
}