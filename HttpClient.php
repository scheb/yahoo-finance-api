<?php
namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\HttpException;

class HttpClient
{

    /**
     * @var string $url
     */
    private $url;


    /**
     * Init with URL
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }


    /**
     * Execute the HTTP query
     * @return string
     */
    public function execute()
    {
        $ch = curl_init($this->url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ));

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpStatus !== 200)
        {
            throw new HttpException("HTTP call failed with error ".$httpStatus.".", $httpStatus);
        }
        elseif ($response === false)
        {
            throw new HttpException("HTTP call failed empty response.", 0);
        }

        return $response;
    }

}