<?php

namespace SimpleStructure\Http;

use SimpleStructure\Exception\RuntimeException;
use SimpleStructure\Model\Resource;

/**
 * HTTP client
 */
class Client
{
    /**
     * Delete content
     *
     * @param string $url     URL
     * @param mixed  $data    data
     * @param array  $headers headers
     * @param array  $options options
     *
     * @return Resource
     */
    public function deleteContent($url, $data = '', array $headers = [], array $options = [])
    {
        return $this->makeRequest(Request::DELETE, $url, $data, $headers, $options);
    }

    /**
     * Get content
     *
     * @param string $url     URL
     * @param array  $headers headers
     * @param array  $options options
     *
     * @return Resource
     */
    public function getContent($url, array $headers = [], array $options = [])
    {
        return $this->makeRequest(Request::GET, $url, '', $headers, $options);
    }

    /**
     * Patch content
     *
     * @param string $url     URL
     * @param mixed  $data    data
     * @param array  $headers headers
     * @param array  $options options
     *
     * @return Resource
     */
    public function patchContent($url, $data = '', array $headers = [], array $options = [])
    {
        return $this->makeRequest(Request::PATCH, $url, $data, $headers, $options);
    }

    /**
     * Post content
     *
     * @param string $url     URL
     * @param mixed  $data    data
     * @param array  $headers headers
     * @param array  $options options
     *
     * @return Resource
     */
    public function postContent($url, $data = '', array $headers = [], array $options = [])
    {
        return $this->makeRequest(Request::POST, $url, $data, $headers, $options);
    }

    /**
     * Put content
     *
     * @param string $url     URL
     * @param mixed  $data    data
     * @param array  $headers headers
     * @param array  $options options
     *
     * @return Resource
     */
    public function putContent($url, $data = '', array $headers = [], array $options = [])
    {
        return $this->makeRequest(Request::PUT, $url, $data, $headers, $options);
    }

    /**
     * Make request
     *
     * @param string $type           type
     * @param string $url            URL
     * @param mixed  $data           data
     * @param array  $requestHeaders request headers
     * @param array  $options        options
     *
     * @return Resource
     *
     * @throws RuntimeException
     */
    public function makeRequest($type, $url, $data = '', array $requestHeaders = [], array $options = [])
    {
        $type = strtolower($type);
        $body = $type === Request::GET ? '' :
            (is_array($data) || is_object($data) ? json_encode($data) : (string) $data);

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $body);
        curl_setopt($connection, CURLOPT_TIMEOUT, 30);
        curl_setopt($connection, CURLOPT_MAXREDIRS, 0);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $requestHeaders);

        $headers = [];
        if (array_key_exists('returnHeaders', $options) && $options['returnHeaders']) {
            curl_setopt($connection, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$headers) {
                $length = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) {
                    return $length;
                }
                $name = strtolower(trim($header[0]));
                $value = trim($header[1]);
                if (array_key_exists($name, $headers)) {
                    if (!is_array($headers[$name])) {
                        $headers[$name] = (array) $headers[$name];
                    }
                    $headers[$name][] = $value;
                } else {
                    $headers[$name] = $value;
                }
                return $length;
            });
        }

        $content = curl_exec($connection);
        $status = curl_getinfo($connection, CURLINFO_HTTP_CODE);
        if ($status < Response::OK || $status >= 300) {
            throw new RuntimeException('An exception occurred during content receiving.');
        }
        curl_close($connection);

        return new Resource($content, $headers);
    }
}