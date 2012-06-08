<?php

namespace Botlife\Entity;

class HttpRequest
{
    
    private $_curl;
    
    public function doCurl($url, $postData = null, $contentType = null)
    {
        if (!$this->_curl) {
            $this->_curl = curl_init();
            curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        }
        $curl = $this->_curl;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt_array($curl, $this->_getOptions());
        if ($postData) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            if ($contentType) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: ' . $contentType,
                ));
            }
        }
        return curl_exec($curl);
    }
    
    public function fileGetContents(
        $url, $postData = null,
        $contentType = 'application/x-www-form-urlencoded'
    ) {
        if ($postData) {
            $options = stream_context_get_options(stream_context_get_default());
            $options['http']['method']  = 'POST';
            $options['http']['content'] = $postData;
            $options['http']['header']  = 'Content-type: ' . $contentType;
            return file_get_contents(
                $url, false, stream_context_create($options)
            );
        } else {
            return file_get_contents($url);
        }
    }
    
    private function _getOptions()
    {
        $context = stream_context_get_options(stream_context_get_default());
        $options = array();
        if (isset($context['http']['proxy'])) {
            $options[CURLOPT_PROXY] = str_replace(
            	'tcp', 'http', $context['http']['proxy']
            ); 
        }
        if (isset($context['http']['timeout'])) {
            $options[CURLOPT_TIMEOUT] = (int) $context['http']['timeout'];
        }
        return $options;
    }
    
}
