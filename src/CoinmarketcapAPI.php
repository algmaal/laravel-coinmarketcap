<?php 
namespace adman9000\coinmarketcap;

class CoinmarketcapAPI
{
    protected $key;     // API key
    protected $secret;  // API secret
    protected $url;     // API base URL
    protected $version; // API version
    protected $curl;    // curl handle

    /**
     * Constructor for BinanceAPI
     */
    function __construct()
    {
        $this->url  = config('coinmarketcap.urls.api');
        $this->curl = curl_init();

        curl_setopt_array($this->curl, [
            CURLOPT_SSL_VERIFYPEER => config('binance.settings.ssl'),
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT      => 'CMC PHP API Agent',
            CURLOPT_RETURNTRANSFER => true
        ]);
    }

    /**
     * Deconstruct cURL
     */
    function __destruct()
    {
        curl_close($this->curl);
    }


    /**
     * Get tickers
     *
     * @param string      $ticker  Return a ticker
     * @param bool|string $convert Return price, 24h volume, and market cap in terms of another currency. Valid values are
     *                             "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF",
     *                             "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN",
     *                             "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     * @return mixed
     * @throws \Exception
     */
    public function getTicker($ticker, $convert = false)
    {
        $params = [];
        if($convert !== false) $params['convert'] = $convert;

        return $this->request("v1/ticker/{$ticker}/", $params);
    }

    /**
     * Get tickers
     *
     * @param bool|int    $start   Return results from rank [start] and above
     * @param bool|int    $limit   Return a maximum of [limit] results (default is 100, use 0 to return all results)
     * @param bool|string $convert Return price, 24h volume, and market cap in terms of another currency. Valid values are
     *                             "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF",
     *                             "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN",
     *                             "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     * @return mixed
     * @throws \Exception
     */
    public function getTickers($start = false, $limit = false, $convert = false)
    {
        $params = [];
        if($start !== false)   $params['start']   = $start;
        if($limit !== false)   $params['limit']   = $limit;
        if($convert !== false) $params['convert'] = $convert;

        return $this->request('v1/ticker/', $params);
}
     /**
     * Get global data
     *
     * @return total_market_cap_usd, total_24h_volume_usd, bitcoin_percentage_of_market_cap, active_currencies, active_assets, active_markets,last_updated
	 * Optional parameters:
	 * (string) convert - return price, 24h volume, and market cap in terms of another currency. Valid values are: 
	 * "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF", "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN", "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     */
    public function getGlobal($convert=FALSE)
    {
        $params = array();
        if($convert!==FALSE) $params['convert'] = $convert;
        return $this->request("v1/global/", $params);

    }

    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @return mixed
     * @throws \Exception
     */
    private function request($url, $params = [], $method = 'GET')
    {
        //Add post vars
        if($method == 'POST') {
            curl_setopt($this->curl, CURLOPT_POST, count($params));
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        }
        else if(sizeof($params)>0) {
            $url .= '?'.http_build_query($params);
        }

        // Set URL & Header
        curl_setopt($this->curl, CURLOPT_URL, $this->url . $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array());

        //Get result
        $result = curl_exec($this->curl);

        if($result === false)
            throw new \Exception('CURL error: ' . curl_error($this->curl));

         // decode results
        $result = json_decode($result, true);
        if(!is_array($result))
            throw new \Exception('JSON decode error');

        return $result;
    }


}
