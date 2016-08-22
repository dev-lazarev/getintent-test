<?php
/**
 * Created by PhpStorm.
 * User: Lazarev Aleksey
 * Date: 22.08.16
 * Time: 17:40
 */


namespace app\components;
class Requester
{

    /**
     * @param $code
     * @return mixed
     */
    protected function getResponse($code)
    {
        return $this->getResponseCodes()[$code];
    }

    /**
     * @return array
     */
    protected function getResponseCodes()
    {
        return [
            '400' => 'Bad request',
            '401' => 'Unauthorized',
            '403' => 'Forbidden',
            '404' => '404',
            '429' => 'Rate limit exceeded',
            '500' => 'Internal server error',
            '503' => 'Service unavailable',
        ];
    }

    /**
     * @param $url
     * @return array|mixed
     */
    public function http_response($url)
    {
        try {
            $ch = curl_init();
            if (FALSE === $ch)
                throw new \Exception('failed to initialize');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout in seconds
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new \Exception('API Service unavailable... try again later...');
            }
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = substr($response, $header_size);
            if ($statusCode == 200) {
                return $body;
            } else {
                throw new \Exception($this->getResponse($statusCode));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}