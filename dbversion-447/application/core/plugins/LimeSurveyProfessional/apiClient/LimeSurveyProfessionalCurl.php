<?php

namespace LimeSurveyProfessional\apiClient;

use Curl\Curl;
use Twig\TokenParser\ApplyTokenParser;
use Yii;

class LimeSurveyProfessionalCurl extends Curl
{
    public const CACHE_KEY_PREFIX = 'LimeSurveyProfessionalCurl.';

    /** @var bool */
    public $cache = false;

    /** @var int */
    public $duration;

    /** @var string */
    public $accessToken;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param int $duration Cache duration in seconds. 0 means never expire
     * sets $cache and $duration properties
     *
     * @return $this
     */
    public function cache(int $duration)
    {
        $this->cache = true;
        $this->duration = $duration;

        return $this;
    }

    /**
     * @param $url
     * @return mixed|null
     */
    private function beforeCallCurl($url)
    {
        $response = null;
        if ($this->cache) {
            $response = $this->loadFromCache($url);
        }
        return $response;
    }

    /**
     * If not cached returns null, else returns cached data
     * @param string $url
     * @return mixed
     */
    private function loadFromCache(string $url)
    {
        $userId = App()->user->id;
        $cacheKey = self::CACHE_KEY_PREFIX . $userId . '.' . $url;
        $cache = Yii::app()->getComponent('cache');
        $cachedData = $cache->get($cacheKey);

        return $cachedData != false ? $cachedData : null;
    }

    /**
     * saves succesful response to cache, using user_id and url as unique id
     * @param string $url
     * @param LimeSurveyProfessionalCurl $response
     *
     * @return void
     */
    private function saveToCache(string $url, LimeSurveyProfessionalCurl $response)
    {
        if ($this->cache && $response->isSuccess()) {
            $userId = App()->user->id;
            $cacheKey = self::CACHE_KEY_PREFIX . $userId . '.' . $url;
            $cache = Yii::app()->getComponent('cache');
            $cache->set($cacheKey, $response, $this->duration);
        }
    }

    /**
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public function get($url, $data = array())
    {
        $response = $this->beforeCallCurl($url);

        if ($response === null) {
            $response = parent::get($url, $data);
            $this->saveToCache($url, $response);
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $asJson
     * @return mixed
     */
    public function post($url, $data = array(), $asJson = false)
    {
        $response = $this->beforeCallCurl($url);
        if ($response === null) {
            $response = parent::post($url, $data, $asJson);
            $this->saveToCache($url, $response);
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $payload
     * @return mixed
     */
    public function put($url, $data = array(), $payload = false)
    {
        $response = $this->beforeCallCurl($url);
        if ($response === null) {
            $response = parent::put($url, $data, $payload);
            $this->saveToCache($url, $response);
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $payload
     * @return mixed
     */
    public function patch($url, $data = array(), $payload = false)
    {
        $response = $this->beforeCallCurl($url);
        if ($response === null) {
            $response = parent::patch($url, $data, $payload);
            $this->saveToCache($url, $response);
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $payload
     * @return mixed
     */
    public function delete($url, $data = array(), $payload = false)
    {
        $response = $this->beforeCallCurl($url);
        if ($response === null) {
            $response = parent::delete($url, $data, $payload);
            $this->saveToCache($url, $response);
        }

        return $response;
    }

    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }
}