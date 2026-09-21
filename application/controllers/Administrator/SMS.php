<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class SMS extends CI_Controller{
    public function __construct() {
        parent::__construct();
        $access = $this->session->userdata('userId');
         if($access == '' ){
            redirect("Login");
        }

        $this->load->model('Model_table', 'mt', true);
        $this->load->model('SMS_model', 'sms', true);
    }
    public function index(){
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Send SMS";
        $data['content'] = $this->load->view('Administrator/SMS/sms', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function sendSms() {
        $res = ['success'=>false, 'message'=>''];
        $data = json_decode($this->input->raw_input_stream);

        $provider = (isset($data->provider) && $data->provider !== '') ? $data->provider : null;
        $result = $this->sms->sendSms($data->number, $data->smsText, $provider);

        if(!$result['success']){
            $res = ['success'=>false, 'message'=>$result['message']];
            echo json_encode($res);
            exit;
        }

        $smsLog = array(
            'number' => $data->number,
            'sms_text' => $data->smsText,
            'sent_by' => $this->session->userdata('userId'),
            'sent_datetime' => date('Y-m-d h:i:s')
        );

        $this->db->insert('tbl_sms', $smsLog);

        $res = ['success'=>true, 'message'=>'SMS sent successfully'];
        echo json_encode($res);
    }

    public function sendBulkSms(){
        $res = ['success'=>false, 'message'=>''];
        $data = json_decode($this->input->raw_input_stream);

        $provider = (isset($data->provider) && $data->provider !== '') ? $data->provider : null;
        $result = $this->sms->sendBulkSms($data->numbers, $data->smsText, $provider);

        if(!$result['success']){
            $res = ['success'=>false, 'message'=>$result['message']];
            echo json_encode($res);
            exit;
        }

        foreach($data->numbers as $number){
            $smsLog = array(
                'number' => $number,
                'sms_text' => $data->smsText,
                'sent_by' => $this->session->userdata('userId'),
                'sent_datetime' => date('Y-m-d h:i:s')
            );

            $this->db->insert('tbl_sms', $smsLog);
        }

        $res = ['success'=>true, 'message'=>'SMS sent successfully'];
        echo json_encode($res);
    }

    public function smsSettings(){
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }

        $data['title'] = "SMS Settings";
        $data['content'] = $this->load->view("Administrator/SMS/sms_settings", $data, true);
        $this->load->view("Administrator/index", $data);
    }

    public function getSmsSettings(){
        $settings = $this->db->query("
            select * from tbl_sms_settings limit 1
        ")->row();

        echo json_encode($settings);
    }

    public function saveSmsSettings(){
        $res = ['success'=>false, 'message'=>'Nothing'];
        try {
            $data = json_decode($this->input->raw_input_stream);

            // Only footer/general fields live in tbl_sms_settings now; provider
            // credentials are managed separately in tbl_sms_providers.
            $allowed = ['sender_name', 'sender_phone'];
            $settings = array();
            foreach($allowed as $field){
                if(property_exists($data, $field)){
                    $settings[$field] = $data->$field;
                }
            }

            $count = $this->db->query("select * from tbl_sms_settings")->num_rows();
            if($count == 0){
                $this->db->insert('tbl_sms_settings', $settings);
            } else {
                $this->db->update('tbl_sms_settings', $settings);
            }

            $res = ['success'=>true, 'message'=>'Saved successfully'];
        } catch (Exception $ex){
            throw new Exception($ex->getMessage());
        }

        echo json_encode($res);
    }

    public function getSmsProviders(){
        $providers = $this->db->query("select * from tbl_sms_providers order by provider_name asc")->result();
        echo json_encode($providers);
    }

    public function saveSmsProvider(){
        $res = ['success'=>false, 'message'=>'Nothing'];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $allowed = ['provider_key', 'provider_name', 'is_enabled', 'api_key', 'sms_type', 'url', 'bulk_url', 'url_2', 'bulk_url_2', 'sender_id', 'sender_id_2', 'user_id', 'password', 'country_code'];
            $providerData = array();
            foreach($allowed as $field){
                if(property_exists($data, $field)){
                    $providerData[$field] = $data->$field;
                }
            }

            if(empty($providerData['provider_key'])){
                echo json_encode(['success'=>false, 'message'=>'Provider is required']);
                return;
            }

            $existing = $this->db->query("select id from tbl_sms_providers where provider_key = ?", array($providerData['provider_key']))->row();

            if($existing){
                $providerData['updated_at'] = date('Y-m-d H:i:s');
                $this->db->where('id', $existing->id);
                $this->db->update('tbl_sms_providers', $providerData);
            } else {
                $providerData['created_at'] = date('Y-m-d H:i:s');
                $providerData['updated_at'] = date('Y-m-d H:i:s');
                if(!isset($providerData['is_enabled'])){
                    $providerData['is_enabled'] = 1;
                }
                $this->db->insert('tbl_sms_providers', $providerData);

                // The first provider ever added is automatically set as default.
                $count = $this->db->query("select count(*) as c from tbl_sms_providers")->row()->c;
                if($count == 1){
                    $this->db->where('provider_key', $providerData['provider_key']);
                    $this->db->update('tbl_sms_providers', ['is_default' => 1]);
                }
            }

            $res = ['success'=>true, 'message'=>'Provider saved successfully'];
        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function setDefaultSmsProvider(){
        $res = ['success'=>false, 'message'=>'Nothing'];
        try {
            $data = json_decode($this->input->raw_input_stream);

            if(empty($data->provider_key)){
                echo json_encode(['success'=>false, 'message'=>'Provider is required']);
                return;
            }

            $this->db->update('tbl_sms_providers', ['is_default' => 0]);

            $this->db->where('provider_key', $data->provider_key);
            $this->db->update('tbl_sms_providers', ['is_default' => 1]);

            $res = ['success'=>true, 'message'=>'Default provider updated'];
        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteSmsProvider(){
        $res = ['success'=>false, 'message'=>'Nothing'];
        try {
            $data = json_decode($this->input->raw_input_stream);

            if(empty($data->provider_key)){
                echo json_encode(['success'=>false, 'message'=>'Provider is required']);
                return;
            }

            $this->db->where('provider_key', $data->provider_key);
            $this->db->delete('tbl_sms_providers');

            $res = ['success'=>true, 'message'=>'Provider deleted'];
        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function testSmsProvider(){
        $res = ['success'=>false, 'message'=>'Nothing'];
        try {
            $data = json_decode($this->input->raw_input_stream);

            if(empty($data->provider_key) || empty($data->number)){
                echo json_encode(['success'=>false, 'message'=>'Provider and mobile number are required']);
                return;
            }

            // Re-read settings in case a provider was just saved in this same session.
            $this->sms->getSettings();

            $result = $this->sms->sendSms($data->number, 'This is a test SMS to verify your SMS provider settings.', $data->provider_key);

            $res = ['success'=>$result['success'], 'message'=>$result['message']];
        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }
}
