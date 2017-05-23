<?php

namespace Paymill\Paymill\Services\Apiclient;

if (!function_exists('json_decode')) {
    throw new \Paymill\Paymill\Services\Exception("Please install the PHP JSON extension");
}

if (!function_exists('curl_init')) {
    throw new \Paymill\Paymill\Services\Exception("Please install the PHP cURL extension");
}

/**
 *   Services_Paymill cURL HTTP client
 */
class Curl implements \Paymill\Paymill\Services\Apiclient\InterfaceCurl
{

    /**
     * Paymill API merchant key
     *
     * @var string
     */
    private $_apiKey = null;
    private $_responseArray = null;

    /**
     *  Paymill API base url
     *
     *  @var string
     */
    private $_apiUrl = '/';

    const USER_AGENT = 'Paymill-Magento-2/1.0.1';

    public static $lastRawResponse;
    public static $lastRawCurlOptions;

    /**
     * cURL HTTP client constructor
     *
     * @param string $apiKey
     * @param string $apiEndpoint
     */
    public function __construct($apiKey, $apiEndpoint)
    {
        $this->_apiKey = $apiKey;
        $this->_apiUrl = $apiEndpoint;
    }

    /**
     * Perform API and handle exceptions
     *
     * @param $action
     * @param array $params
     * @param string $method
     * @return mixed
     * @throws Services_Paymill_Exception
     * @throws Exception
     */
    public function request($action, $params = array(), $method = 'POST')
    {
        if (!is_array($params))
            $params = array();

        try {
            $this->_responseArray = $this->_requestApi($action, $params, $method);
            $httpStatusCode = $this->_responseArray['header']['status'];
            if ($httpStatusCode != 200) {
                $errorMessage = 'Client returned HTTP status code ' . $httpStatusCode;
                if (isset($this->_responseArray['body']['error'])) {
                    $errorMessage = $this->_responseArray['body']['error'];
                }
                $responseCode = '';
                if (isset($this->_responseArray['body']['response_code'])) {
                    $responseCode = $this->_responseArray['body']['response_code'];
                }
                if ($responseCode === '' && isset($this->_responseArray['body']['data']['response_code'])) {
                    $responseCode = $this->_responseArray['body']['data']['response_code'];
                }

                return array("data" => array(
                        "error" => $errorMessage,
                        "response_code" => $responseCode,
                        "http_status_code" => $httpStatusCode
                        ));
            }

            return $this->_responseArray['body'];
        } catch (\Paymill\Paymill\Services\Exception $e) {
            return array("data" => array("error" => $e->getMessage()));
        }
    }

    /**
     * Perform HTTP request to REST endpoint
     *
     * @param string $action
     * @param array $params
     * @param string $method
     * @return array
     */
    protected function _requestApi($action = '', $params = array(), $method = 'POST')
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dir = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
        $logDir = $dir->getPath(\Magento\Framework\App\Filesystem\DirectoryList::APP);

        $curlOpts = array(
            CURLOPT_URL => $this->_apiUrl . $action,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_SSL_VERIFYPEER => true
         );

        if (\Paymill\Paymill\Services\Apiclient\InterfaceCurl::HTTP_GET === $method) {
            if (0 !== count($params)) {
                $curlOpts[CURLOPT_URL] .= false === strpos($curlOpts[CURLOPT_URL], '?') ? '?' : '&';
                $curlOpts[CURLOPT_URL] .= http_build_query($params, null, '&');
            }
        } else {
            $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        }

        if ($this->_apiKey) {
            $curlOpts[CURLOPT_USERPWD] = $this->_apiKey . ':';
        }

        $curl = curl_init();

        curl_setopt_array($curl, $curlOpts);
        $responseBody = curl_exec($curl);
        self::$lastRawCurlOptions = $curlOpts;
        self::$lastRawResponse = $responseBody;
        $responseInfo = curl_getinfo($curl);
        if ($responseBody === false) {
            $responseBody = array('error' => curl_error($curl));
        }
        curl_close($curl);

        if ('application/json' === $responseInfo['content_type']) {
            $responseBody = json_decode($responseBody, true);
        }

        return array(
            'header' => array(
                'status' => $responseInfo['http_code'],
                'reason' => null,
            ),
            'body' => $responseBody
        );
    }

    /**
     * Returns the response of the request as an array.
     * @return mixed Response
     * @todo Create Unit Test
     */
    public function getResponse()
    {
        return $this->_responseArray;
    }

}
