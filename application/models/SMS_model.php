<?php
class SMS_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->getSettings();
    }

    private $providers = array();
    private $defaultProviderKey = null;
    private $senderName = "";
    private $senderPhone = "";

    private function getSmsFooter(){
        return "\n\nThank you,\n{$this->senderName}\nPhone: {$this->senderPhone}";
    }

    private function generateCsmsId(){
        return strtoupper(substr(md5(uniqid('', true)), 0, 20));
    }

    public function getSettings(){
        $query = $this->db->query("select * from tbl_sms_settings limit 1");
        if($query->num_rows() > 0){
            $settings = $query->row();
            $this->senderName  = $settings->sender_name;
            $this->senderPhone = $settings->sender_phone;
        }

        $this->providers = array();
        $this->defaultProviderKey = null;

        $rows = $this->db->query("select * from tbl_sms_providers where is_enabled = 1")->result();
        foreach($rows as $row){
            $this->providers[$row->provider_key] = $row;
            if($row->is_default == 1){
                $this->defaultProviderKey = $row->provider_key;
            }
        }

        if($this->defaultProviderKey === null && count($this->providers) > 0){
            $first = reset($this->providers);
            $this->defaultProviderKey = $first->provider_key;
        }
    }

    /**
     * Returns enabled providers, e.g. for populating a "send with" dropdown.
     */
    public function getAvailableProviders(){
        $list = array();
        foreach($this->providers as $key => $provider){
            $list[] = array(
                'provider_key'  => $provider->provider_key,
                'provider_name' => $provider->provider_name,
                'is_default'    => $provider->is_default == 1
            );
        }
        return $list;
    }

    private function resolveProvider($providerKey = null){
        if($providerKey && isset($this->providers[$providerKey])){
            return $this->providers[$providerKey];
        }
        if($this->defaultProviderKey && isset($this->providers[$this->defaultProviderKey])){
            return $this->providers[$this->defaultProviderKey];
        }
        return null;
    }

    private function buildSingleRequest($provider, $recipient, $smsText){
        $isJson = false;

        switch($provider->provider_key){
            case 'gateway1':
                $url = $provider->url;
                $postData = array(
                    "api_key" => $provider->api_key,
                    "type" => $provider->sms_type,
                    "senderid" => $provider->sender_id,
                    "msg" => $smsText,
                    "contacts" => "88{$recipient}"
                );
                break;

            case 'mram':
                $url = $provider->url;
                $postData = array(
                    "api_key" => $provider->api_key,
                    "type" => $provider->sms_type,
                    "contacts" => "88{$recipient}",
                    "senderid" => $provider->sender_id,
                    "msg" => $smsText
                );
                break;

            case 'gennet':
                $url = $provider->url;
                $postData = array(
                    "api_token" => $provider->api_key,
                    "sid" => $provider->sender_id,
                    "msisdn" => "88{$recipient}",
                    "sms" => $smsText,
                    "csms_id" => $this->generateCsmsId()
                );
                $isJson = true;
                break;

            case 'gateway2':
            default:
                $url = $provider->url_2;
                $postData = array(
                    "user" => $provider->user_id,
                    "sender" => $provider->sender_id_2,
                    "pwd" => $provider->password,
                    "CountryCode" => $provider->country_code,
                    "mobileno" => $recipient,
                    "msgtext" => $smsText
                );
                break;
        }

        return array($url, $postData, $isJson);
    }

    private function buildBulkRequest($provider, $recipients, $smsText){
        $isJson = false;

        switch($provider->provider_key){
            case 'gateway1':
                $url = $provider->bulk_url;
                $messages = array_map(function($recipient) use ($smsText){
                    $recipient = trim($recipient);
                    return array(
                        'to' => "88{$recipient}",
                        'message' => $smsText
                    );
                }, $recipients);

                $postData = array(
                    "api_key" => $provider->api_key,
                    "type" => $provider->sms_type,
                    "senderid" => $provider->sender_id,
                    "messages" => json_encode($messages)
                );
                break;

            case 'mram':
                $url = $provider->url;
                $recipient = implode("+88", array_map('trim', $recipients));
                $postData = array(
                    "api_key" => $provider->api_key,
                    "type" => $provider->sms_type,
                    "contacts" => $recipient,
                    "senderid" => $provider->sender_id,
                    "msg" => $smsText
                );
                break;

            case 'gennet':
                $url = $provider->bulk_url;
                $postData = array(
                    "api_token" => $provider->api_key,
                    "sid" => $provider->sender_id,
                    "msisdn" => array_map('trim', $recipients),
                    "sms" => $smsText,
                    "batch_csms_id" => $this->generateCsmsId()
                );
                $isJson = true;
                break;

            case 'gateway2':
            default:
                $url = $provider->bulk_url_2;
                $recipient = implode(",", array_map('trim', $recipients));
                $postData = array(
                    "user" => $provider->user_id,
                    "senderid" => $provider->sender_id_2,
                    "pwd" => $provider->password,
                    "CountryCode" => $provider->country_code,
                    "mobileno" => $recipient,
                    "msgtext" => $smsText,
                    "priority" => 'High'
                );
                break;
        }

        return array($url, $postData, $isJson);
    }

    private function executeCurl($url, $postData, $isJson){
        if(empty($url)){
            $message = 'SMS provider URL is not configured';
            log_message('error', "SMS send failed: {$message}");
            return array('success' => false, 'message' => $message, 'raw' => null);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        if($isJson){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        }else{
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($curlErrno){
            log_message('error', "SMS send failed (curl error {$curlErrno}): {$curlError} [url={$url}]");
            return array('success' => false, 'message' => "Connection error: {$curlError}", 'raw' => null);
        }

        if($httpCode >= 400){
            log_message('error', "SMS send failed (HTTP {$httpCode}): {$result} [url={$url}]");
            return array('success' => false, 'message' => "Gateway returned HTTP {$httpCode}: {$result}", 'raw' => $result);
        }

        if($result === '' || $result === false){
            log_message('error', "SMS send failed: empty response from gateway [url={$url}]");
            return array('success' => false, 'message' => 'Empty response from SMS gateway', 'raw' => $result);
        }

        return array('success' => true, 'message' => 'SMS sent', 'raw' => $result);
    }

    /**
     * @param string $recipient
     * @param string $message
     * @param string|null $providerKey Optional provider override; falls back to the default provider.
     * @return array ['success' => bool, 'message' => string, 'raw' => mixed]
     */
    public function sendSms($recipient, $message, $providerKey = null) {
        $provider = $this->resolveProvider($providerKey);
        if(!$provider){
            $message = 'No SMS provider is configured/enabled';
            log_message('error', "SMS send failed: {$message}");
            return array('success' => false, 'message' => $message, 'raw' => null);
        }

        $recipient = trim($recipient);
        $smsText = urldecode($message) . $this->getSmsFooter();

        list($url, $postData, $isJson) = $this->buildSingleRequest($provider, $recipient, $smsText);

        return $this->executeCurl($url, $postData, $isJson);
    }

    /**
     * @param array $recipients
     * @param string $message
     * @param string|null $providerKey Optional provider override; falls back to the default provider.
     * @return array ['success' => bool, 'message' => string, 'raw' => mixed]
     */
    public function sendBulkSms($recipients, $message, $providerKey = null) {
        $provider = $this->resolveProvider($providerKey);
        if(!$provider){
            $message = 'No SMS provider is configured/enabled';
            log_message('error', "SMS bulk send failed: {$message}");
            return array('success' => false, 'message' => $message, 'raw' => null);
        }

        $smsText = urldecode($message);

        list($url, $postData, $isJson) = $this->buildBulkRequest($provider, $recipients, $smsText);

        return $this->executeCurl($url, $postData, $isJson);
    }
}
