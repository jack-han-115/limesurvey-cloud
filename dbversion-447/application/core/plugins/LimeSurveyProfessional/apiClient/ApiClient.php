<?php

namespace LimeSurveyProfessional\apiClient;

use http\Exception\InvalidArgumentException;
use LimeSurveyProfessional\InstallationData;

class ApiClient
{
    /** @var string */
    public $accessToken;

    /** @var string */
    public $apiId;

    /** @var string */
    public $apiSecret;

    /** @var LimeSurveyProfessionalCurl */
    public $curl;

    public $config;

    /**
     * Class containing all the different account-api calls and handling auth
     */
    public function __construct(InstallationData $installationData, LimeSurveyProfessionalCurl $curl, $config = null)
    {
        /**
         * Import the config constants
         */
        $this->accessToken = $installationData->accessToken;
        $this->apiId = $installationData->apiId;
        $this->apiSecret = $installationData->apiSecret;
        $this->curl = $curl;
        $this->config = $config;
    }

    /**
     * calls API route "hasUnpaidInvoicesDuringGracePeriod"
     * @return \stdClass|null
     */
    public function getHasUnpaidInvoicesDuringGracePeriod()
    {
        $curlWithResponse = $this->signinAndCall('get', $this->getUrlFromConfig('apiUriHasUnpaidDuringGp'), 300);

        return $this->getDecodedResponseData($curlWithResponse);
    }

    /**
     * Tries to call with accessToken.
     * if not successful: signs in to obtain new token and tries again.
     *
     *
     * @param string $type
     * @param string $url
     * @param int $cache duration in seconds
     * @param array $data
     * @return LimeSurveyProfessionalCurl
     */
    private function signinAndCall(string $type, string $url, int $cache = -1, array $data = [])
    {
        $curlWithResponse = $this->callApi($type, $url, $cache, $data);
        if (!$curlWithResponse->isSuccess()) {
            if ($this->signin()) {
                $curlWithResponse = $this->callApi($type, $url, $cache, $data);
            }
        }

        return $curlWithResponse;
    }

    /**
     *
     * @param string $type get|post|put|patch|delete
     * @param string $url
     * @param int $cache duration in seconds
     * @param array $data
     * @return LimeSurveyProfessionalCurl
     */
    private function callApi(string $type, string $url, int $cache = -1, array $data = [])
    {
        $this->curl->setHeader('Authorization', 'Bearer ' . $this->accessToken);
        if ($cache >= 0) {
            $this->curl->cache($cache);
        }
        switch ($type) {
            case 'post':
                $curlWithResponse = $this->curl->post($url, $data);
                break;
            case 'put':
                $curlWithResponse = $this->curl->put($url, $data);
                break;
            case 'patch':
                $curlWithResponse = $this->curl->patch($url, $data);
                break;
            case 'delete':
                $curlWithResponse = $this->curl->delete($url, $data);
                break;
            case 'get':
                $curlWithResponse = $this->curl->get($url, $data);
                break;
            default:
                throw new InvalidArgumentException("Invalid type parameter: $type");
        }

        return $curlWithResponse;
    }

    /**
     * calls api's signin route
     *
     * @return bool true if signin was successful
     */
    private function signin()
    {
        $curlWithResponse = $this->curl->post(
            $this->getUrlFromConfig('apiUriSignin'),
            ['client_id' => $this->apiId, 'client_secret' => $this->apiSecret]
        );

        return $this->handleReturnedSigninData($this->getDecodedResponseData($curlWithResponse));
    }

    /**
     * Checks if signin data has property "access_token" and, if present, saves it.
     *
     * @param $data
     * @return bool
     */
    public function handleReturnedSigninData($data)
    {
        $success = false;

        if (is_object($data) && property_exists($data, 'access_token')) {
            $this->accessToken = $data->access_token;
            $success = true;
            try {
                $this->saveAccessToken();
            } catch (\CDbException $e) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * saves accessToken in setting_global table
     */
    private function saveAccessToken()
    {
        $accessTokenSetting = \SettingGlobal::model()->findByAttributes(['stg_name' => 'AccessToken']);
        if (!$accessTokenSetting) {
            $accessTokenSetting = new \SettingGlobal();
            $accessTokenSetting->stg_name = 'AccessToken';
        }
        $accessTokenSetting->stg_value = $this->accessToken;
        if (!$accessTokenSetting->save()) {
            throw new \CDbException("AccessToken could not be saved!");
        }
    }

    /**
     * Returns extracted and decoded data property from response
     * @param LimeSurveyProfessionalCurl $curlWithResponse
     * @return \stdClass|null
     */
    public function getDecodedResponseData(LimeSurveyProfessionalCurl $curlWithResponse)
    {
        $data = null;
        if ($curlWithResponse->isSuccess()) {
            $decodedResponse = json_decode($curlWithResponse->getResponse());
            if (property_exists($decodedResponse, 'data')) {
                $data = $decodedResponse->data;
            }
        }
        // else Notify user, notify LS???

        return $data;
    }

    /**
     * returns full Url for API call based on given paramName
     * @param string $paramName
     * @return string
     */
    public function getUrlFromConfig(string $paramName)
    {
        $url = '';
        if (!is_null($this->config) && property_exists($this->config->metadata, $paramName)) {
            $baseUrlParam = App()->getConfig('debug') == 2 ? 'apiUrlDev' : 'apiUrl';
            $url = (string)$this->config->metadata->$baseUrlParam;
            $paramUrl = (string)$this->config->metadata->$paramName;
            $url .= $paramUrl;
        }

        return $url;
    }
}
