<?php
namespace InstagramAPI;

use InstagramAPI\Devices\Device;

class State {
    protected $_data = [];

    public function __construct($initial = null) {
        if ($initial)
            $this->handleData($initial);
    }

    public function toArray() {
        return $this->_data;
    }

    public function set($key, $value) {
        $this->_data[$key] = $value;
    }

    public function setMulti($data) {
        $this->_data = $data + $this->_data;
    }

    public function handleData($data) {
        $data = is_string($data) ? json_decode($data, true) : $data;

        foreach ($data as $key => $value) {
            if ($key == 'user_agent') {
                $device = new Device();
                $device->generateFromUserAgent($value);

                $this->setMulti([
                    'ig_version' => $device->getAppVersion(),
                    'version_code' => $device->getVersionCode(),
                    'locale' => $device->getLocale(),
                    'devicestring' => $device->getDeviceString()
                ]);
            } else if ($key == 'session')
                $this->detectSessionData($value);
            else if ($key == 'cookies')
                $this->handleCookies($value);
            else if ($key == 'headers')
                $this->handleHeaders($value);
            else
                $this->set($key, $value);
        }
    }

    public function detectSessionData($data): self {
        $cookies = $headers = [];
        if (is_string($data)) {
            $data = explode(';', $data);
            foreach ($data as $datum) {
                list($key, $value) = explode('=', $datum);
                if (preg_match('#^[A-Z]+#', $key))
                    $headers[$key] = $value;
                else
                    $cookies[$key] = $value;
            }
        }

        $this
            ->handleHeaders($headers)
            ->handleCookies($cookies);

        return $this;
    }

    public function handleHeaders($headers): self {
        $outputHeaders = [];

        $headersToStateMap = [
            'x-mid' => 'mid',
            'authorization' => 'authorization',
            'x-ig-www-claim' => 'www_claim',
            'ig-u-ds-user-id' => 'account_id',
        ];

        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);

            if (empty($value))
                continue;

            $value = urldecode($value);

            if ($headersToStateMap[$lowerKey])
                $this->set($headersToStateMap[$lowerKey], $value);
            else
                $outputHeaders[$lowerKey] = $value;
        }

        $this->set('headers', $outputHeaders);

        return $this;
    }

    public function handleCookies($cookies): self {
        $out = [];

        $cookiesToStateMap = [
            'mid' => 'mid',
            'ds_user_id' => 'account_id'
        ];

        $cookiesMap = [
            'mid'        => [63072000, false],
            'shbid'      => [604800, true],
            'shbts'      => [604800, true],
            'sessionid'  => [31536000, true],
            'csrftoken'  => [31449600, false],
            'rur'        => [null, true],
            'ds_user_id' => [7776000, false],
        ];

        $defaultMaxAge = 31536000; // 1 year
        foreach ($cookies as $cookieName => $cookieValue) {
            if (empty($cookieName))
                continue;

            if ($cookiesToStateMap[$cookieName])
                $this->set($cookiesToStateMap[$cookieName], $cookieValue);

            list($maxAge, $httpOnly) = $cookiesMap[$cookieName];
            $out[] = [
                'Name' => $cookieName,
                'Value' => $cookieValue,
                'Domain' => '.instagram.com',
                'Path' => '/',
                'Max-Age' => $cookiesMap[$cookieName] ? $maxAge : $defaultMaxAge,
                'Expires' => $cookiesMap[$cookieName] ? ($maxAge === null ? $maxAge : time() + $maxAge) : time() + $defaultMaxAge,
                'Secure' => true,
                'Discard' => false,
                'HttpOnly' => $httpOnly ?? true
            ];
        }

        $this->set('cookies', json_encode($out));

        return $this;
    }
}