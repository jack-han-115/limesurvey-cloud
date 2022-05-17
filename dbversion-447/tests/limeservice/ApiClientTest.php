<?php

namespace ls\tests;

use LimeSurveyProfessional;
use Yii;
use LimeSurvey\PluginManager\PluginManager;

class ApiClientTest extends TestBaseClass
{
    public static function setupBeforeClass(): void
    {
        $pluginDir = 'application.core.plugins.LimeSurveyProfessional.*';
        Yii::import($pluginDir);
        $pm = new PluginManager();
        $plugin = new LimeSurveyProfessional($pm, null);
        $plugin->init(false);
    }

    public function testApiCache()
    {
        $curlClass = new LimeSurveyProfessional\apiClient\LimeSurveyProfessionalCurl();
        $curlClass->cache(100);

        $this->assertTrue($curlClass->cache && $curlClass->duration == 100);
    }

    public function testNoApiCache()
    {
        $curlClass = new LimeSurveyProfessional\apiClient\LimeSurveyProfessionalCurl();

        $this->assertTrue($curlClass->cache == false);
    }

    public function testApiDataDecodingEmptyResponse()
    {
        $installationData = $this->getInstallationData();
        $curlClass = new LimeSurveyProfessional\apiClient\LimeSurveyProfessionalCurl();
        $apiClient = new LimeSurveyProfessional\apiClient\ApiClient($installationData, $curlClass);
        $decodedData = $apiClient->getDecodedResponseData($curlClass);

        $this->assertTrue(is_null($decodedData));
    }

    public function testApiDataDecoding()
    {
        $installationData = $this->getInstallationData();
        $curlClass = new LimeSurveyProfessional\apiClient\LimeSurveyProfessionalCurl();
        $apiClient = new LimeSurveyProfessional\apiClient\ApiClient($installationData, $curlClass);
        $curlClass->http_status_code = 200;
        $curlClass->response = $this->getMockedApiResponseJson();
        $decodedData = $apiClient->getDecodedResponseData($curlClass);

        $this->assertTrue(
            is_object($decodedData[0])
            && property_exists($decodedData[0], 'order_no')
            && $decodedData[0]->order_no == 123456
        );
    }

    public function testApiDataDecodingSimple()
    {
        $installationData = $this->getInstallationData();
        $curlClass = new LimeSurveyProfessional\apiClient\LimeSurveyProfessionalCurl();
        $apiClient = new LimeSurveyProfessional\apiClient\ApiClient($installationData, $curlClass);
        $curlClass->http_status_code = 200;
        $curlClass->response = $this->getMockedApiResponseJson(true);
        $decodedData = $apiClient->getDecodedResponseData($curlClass);

        $this->assertTrue(
            is_object($decodedData[0])
            && property_exists($decodedData[0], 'result')
            && $decodedData[0]->result == true
        );
    }

    public function testInvalidSignin() {
        $installationData = $this->getInstallationData();
        $curlClass = new LimeSurveyProfessional\apiClient\LimeSurveyProfessionalCurl();
        $apiClient = new LimeSurveyProfessional\apiClient\ApiClient($installationData, $curlClass);
        $curlClass->http_status_code = 400;
        $curlClass->response = $this->getMockedApiErrorResponseJson();
        $signin = $apiClient->handleReturnedSigninData($apiClient->getDecodedResponseData($curlClass));

        $this->assertFalse($signin);
    }

    private function getMockedApiResponseJson($simple = false)
    {
        $orderObj = new \stdClass();
        if ($simple) {
            $orderObj->result = true;
        } else {
            $invoiceObj = new \stdClass();
            $invoiceObj->id = 1;
            $invoiceObj->document_type = 'IN';
            $invoiceObj->paid_in_full = 0;

            $orderObj->id = 1;
            $orderObj->order_no = 123456;
            $orderObj->last_due_date = 1634248800;
            $orderObj->next_due_date = 1636930800;
            $orderObj->invoices = [
                $invoiceObj
            ];
        }

        $responseObj = new \stdClass();
        $responseObj->status = 'success';
        $responseObj->error_message = '';
        $responseObj->error_code = null;
        $responseObj->data = [
            $orderObj
        ];

        return json_encode($responseObj);
    }

    private function getMockedApiErrorResponseJson()
    {
        $responseObj = new \stdClass();
        $responseObj->status = 'failed';
        $responseObj->error_message = 'Error description message';
        $responseObj->error_code = 400;
        $responseObj->data = null;

        return json_encode($responseObj);
    }

    /**
     * @return LimeSurveyProfessional\InstallationData
     */
    private function getInstallationData()
    {
        $installationData = new LimeSurveyProfessional\InstallationData();
        $installationData->isHardLocked = false;
        $installationData->plan = 'free';
        $installationData->isSiteAdminUser = true;
        $installationData->isPayingUser = false;
        $installationData->outOfResponses = false;
        $installationData->locked = false;
        $installationData->emailLock = 0;
        $installationData->dateSubscriptionCreated = '2020-10-29 00:00:00';
        $installationData->dateSubscriptionPaid = '2021-12-31 00:00:00';
        $installationData->paymentPeriod = 'M';
        $installationData->reminderLimitStorage = 10;
        $installationData->reminderLimitResponses = 10;
        $installationData->hasResponseNotification = false;
        $installationData->hasStorageNotification = false;
        $installationData->accessToken = 12345;
        $installationData->apiId = '';
        $installationData->apiSecret = '';

        return $installationData;
    }
}