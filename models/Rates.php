<?php

namespace app\models;

use Yii;
use app\components\ExchangeRateProvider;

class Rates
{
    public function updateFromProvider(ExchangeRateProvider $rateProvider, $currencies = 'USD, EUR')
    {
        $data = $rateProvider->getRateValues($currencies);
        if (!empty($data)) {

            $date = $data['date'];
            $rates = $data['rates'];
            $values = [];
            foreach ($rates as $rateKey => $rateValue) {
                $values[] = [$rateKey, str_replace(',', '.', $rateValue), $date];
            }

            if (!empty($values)) {
                Yii::$app->db->createCommand()->batchInsert('rates', ['name', 'value', 'date'], $values)->execute();
            }
        }

    }

    public function get($currencies = ['USD', 'EUR'])
    {
        $sql = '';
        if(!empty($currencies)){
            foreach ($currencies as $key=>$currency){
                $sql .= "(select * from rates where name = :currency{$key} order by date DESC LIMIT 1) UNION ";
            }

            $sql = rtrim($sql, 'UNION ');
            $command = Yii::$app->db->createCommand($sql);
            foreach ($currencies as $key=>$currency){
                $command->bindValue(":currency{$key}", $currency);
            }
            $data = $command->queryAll();
            return $data;
        }
        return [];
    }
}
