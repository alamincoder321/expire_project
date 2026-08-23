<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class DayClose extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $access = $this->session->userdata('userId');
        $this->brunch = $this->session->userdata('BRANCHid');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
    }
    public function index()
    {
        $data['title'] = "Day Close Entry";
        $data['content'] = $this->load->view('Administrator/add_dayclose', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addDayClose()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $daycloseObj = json_decode($this->input->raw_input_stream);

            $check = $this->db->where('date', $daycloseObj->date)->where('status', 'a')->where('user_id', $daycloseObj->user_id)->get('tbl_dayclose')->row();
            if (!empty($check)) {
                $res = ['success' => false, 'message' => 'Already exist this data'];
                echo json_encode($res);
                exit;
            }

            $dayclose = (array)$daycloseObj;
            unset($dayclose['id']);
            $dayclose["branch_id"] = $this->session->userdata("BRANCHid");

            $dayclose["AddBy"] = $this->session->userdata("FullName");
            $dayclose["AddTime"] = date("Y-m-d H:i:s");
            $this->db->insert('tbl_dayclose', $dayclose);

            $res = ['success' => true, 'message' => 'Day Close added successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateDayClose()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $daycloseObj = json_decode($this->input->raw_input_stream);

            $check = $this->db->where('date', $daycloseObj->date)->where('status', 'a')->where('user_id', $daycloseObj->user_id)->where('id !=', $daycloseObj->id)->get('tbl_dayclose')->row();
            if (!empty($check)) {
                $res = ['success' => false, 'message' => 'Already exist this data'];
                echo json_encode($res);
                exit;
            }

            $dayclose = (array)$daycloseObj;

            unset($dayclose["id"]);
            $dayclose["branch_id"] = $this->session->userdata("BRANCHid");
            $dayclose["UpdateBy"] = $this->session->userdata("FullName");
            $dayclose["UpdateTime"] = date("Y-m-d H:i:s");



            $res = ['success' => true, 'message' => 'Day Close updated successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteDayClose()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $this->db->query("update tbl_dayclose set status = 'd' where id = ?", $data->daycloseId);

            $res = ['success' => true, 'message' => 'Day Close deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getDayCloses()
    {
        $data = json_decode($this->input->raw_input_stream);

        $queries = $this->db->query("
            select 
            dc.*,
            u.FullName, u.User_Name
            from tbl_dayclose dc
            left join tbl_user u on u.User_SlNo = dc.user_id
            where dc.status = 'a'
            and dc.branch_id = ?
            " . (!empty($data->userId) ? "and dc.user_id = '$data->userId'" : "") . "
            order by dc.id desc
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($queries);
    }
}
