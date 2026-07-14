<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("model_myclass", "mmc", TRUE);
		$this->load->model('model_table', "mt", TRUE);
		$this->load->helper('JWT');
		$this->load->helper('my_helper');
	}
	public function index()
	{
		$data['title'] = "Login";
		$this->load->view('login/login', $data);
	}

	public function register()
	{
		$data['title'] = "Register";
		$this->load->view('login/register', $data);
	}

	function procedure()
	{
		$res = ['status' => false, 'message' => ''];
		$user = $this->input->post('username');
		$pass = md5($this->input->post('password'));
		$userLat = $this->input->post('latitude');
		$userLng = $this->input->post('longitude');

		$query = $this->db->query("SELECT u.User_SlNo, u.latitude, u.longitude, u.User_ID, u.FullName, u.User_Name, u.userBrunch_id, u.UserType, u.image_name as user_image, u.status AS userstatus, br.brunch_id, br.Brunch_name, br.Brunch_sales FROM tbl_user AS u LEFT JOIN tbl_brunch AS br ON br.brunch_id = u.userBrunch_id where br.status = 'a' and u.User_Name = ? AND u.User_Password = ?", [$user, $pass]);
		$data = $query->row();


		if (isset($data)) {
			if ($data->userstatus == 'a') {
				$company = $this->db->select(['Company_Logo_org', 'Currency_Name'])->get('tbl_company')->row();
				$this->db->insert(
					'tbl_user_activity',
					[
						'user_id' 		=>	$data->User_SlNo,
						'ip_address' 	=>	get_client_ip(),
						'login_time' 	=>	date("Y-m-d H:i:s"),
						'status' 		=>	'a',
						'branch_id' 	=>	$data->userBrunch_id,
					]
				);

				$sdata['user_activity_id'] = $this->db->insert_id();

				$sdata['userId'] = $data->User_SlNo;
				$sdata['BRANCHid'] = $data->userBrunch_id;
				$sdata['FullName'] = $data->FullName;
				$sdata['User_Name'] = $data->User_Name;
				$sdata['user_image'] = $data->user_image;
				$sdata['accountType'] = $data->UserType;
				$sdata['userBrunch'] = $data->Brunch_sales;
				$sdata['Brunch_name'] = $data->Brunch_name;
				$sdata['Brunch_image'] = $company->Company_Logo_org;
				$sdata['Currency_Name'] = $company->Currency_Name;



				$distance = $this->distance($userLat, $userLng, $data->latitude, $data->longitude);
				$allowedRadius = 100;

				if (in_array($data->UserType, ['e', 'u'])) {
					if ($distance <= $allowedRadius) {
						$this->session->set_userdata($sdata);
						$res['status'] = true;
						$res['message'] = "Login success";
					} else {
						$res['status'] = false;
						$res['message'] = "You are outside of office area.";
					}
				} else {
					$this->session->set_userdata($sdata);
					$res['status'] = true;
					$res['message'] = "Login success";
				}
			} else {
				$res['message'] = "Sorry your are deactivated";
				// $this->load->view('login/login', $sdata);
			}
		} else {
			$res['message'] = "Invalid User name or Password";
			//  $this->load->view('login/login', $sdata);
		}

		echo json_encode($res);
	}

	function distance($lat1, $lon1, $lat2, $lon2)
	{
		$earth = 6371000;

		$dLat = deg2rad($lat2 - $lat1);
		$dLon = deg2rad($lon2 - $lon1);

		$a = sin($dLat / 2) * sin($dLat / 2) +
			cos(deg2rad($lat1)) *
			cos(deg2rad($lat2)) *
			sin($dLon / 2) *
			sin($dLon / 2);

		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

		return $earth * $c;
	}


	public function forgotpassword()
	{
		$data['title'] = "Forgot Password";
		$this->load->view('ForgotPassword', $data);
	}

	public function logout()
	{
		$this->db->where('id', $this->session->userdata("user_activity_id"));
		$this->db->update('tbl_user_activity', ['logout_time' => date("Y-m-d H:i:s")]);

		$this->session->unset_userdata('user_activity_id');
		$this->session->unset_userdata('userId');
		$this->session->unset_userdata('User_Name');
		$this->session->unset_userdata('accountType');
		$this->session->unset_userdata('panel');
		//$this->session->unset_userdata('useremail');
		redirect("Login");
	}
}
