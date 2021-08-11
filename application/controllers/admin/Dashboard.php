<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once ('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Dashboard extends CI_Controller
{

  public $data;

  public function __construct()
  {
    parent::__construct();
    $this->data['theme'] = 'admin';
    $this->data['model'] = 'dashboard';
    $this->load->model('dashboard_model', 'dashboard');
    $this->load->model('user_login_model', 'user');
    $this->load->model('common_model', 'common_model');
    $this->data['base_url'] = base_url();
    $this->load->helper('user_timezone');
    $this->data['user_role'] = $this->session->userdata('role');
    $this->load->model('Api_model', 'api');
  }

  public function index()
  {
    $this->data['page'] = 'index';
    $this->data['payment'] = $this->dashboard->get_payments_info();
    $this->load->vars($this->data);
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function admin_notification($value = '')
  {
    $this->data['page'] = 'admin_notification';
    $this->data['admin_notification'] = $this->db->where('n.receiver', $this->session->userdata('chat_token'))->where('n.status', 1)->from('notification_table as n')->join('providers as p ', 'p.token=n.sender', 'left')->select('n.notification_id,n.message,n.created_at,p.name,p.profile_img,n.utc_date_time')->get()->result_array();
    $notification_update = $this->db->where('receiver', $this->session->userdata('chat_token'))->update('notification_table', ['status' => 0]);
    $this->data['admin_id'] = $this->session->userdata('admin_id');
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function send_notification($user_token = '')
  {
    $this->data['page'] = 'send_notification';
    $getData = $this->db->where('n.sender', $this->session->userdata('chat_token'))->where('n.receiver', $user_token)->from('notification_table as n')
    // ->join('providers as p ','p.token=n.receiver', 'left')
    // ->join('users as u ','u.token=n.receiver', 'left')
    ->select('n.notification_id,n.message,n.created_at,n.utc_date_time')->get();
    if (!$getData)
    {
      $this->data['notifications'] = [];
    }
    else
    {
      $this->data['notifications'] = $getData->result_array();
    }
    $this->data['admin_id'] = $this->session->userdata('admin_id');
    $this->data['user_token'] = $user_token;
    $this->data['user_info'] = (array)$this->api->get_token_info($user_token);
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function send_notification_message()
  {
    extract($_POST);
    $token = $this->session->userdata('chat_token');
    $this->send_push_notification($token, $this->session->userdata('id') , $to_token, 1, $content);
    echo json_encode(["success" => 1]);
    exit;
  }

  public function send_push_notification($token, $provider_id, $receiver_token, $type, $msg = '')
  {
    if ($type == 1)
    {
      $device_tokens = $this->api->get_device_info_multiple($provider_id, 1);
    }
    /*insert notification*/
    $msg = "From " . ucfirst($this->session->userdata('name')) . " : " . strtolower($msg);
    $this->api->insert_notification($token, $receiver_token, $msg);

    $title = 'Subscription';

    if (!empty($device_tokens))
    {
      foreach ($device_tokens as $key => $device)
      {
        if (!empty($device['device_type']) && !empty($device['device_id']))
        {
          if (strtolower($device['device_type']) == 'android')
          {
            $notify_structure = array(
              'title' => $title,
              'message' => $msg,
              'image' => 'test22',
              'action' => 'test222',
              'action_destination' => 'test222',
            );
            sendFCMMessage($notify_structure, $device['device_id']);
          }

          if (strtolower($device['device_type'] == 'ios'))
          {
            $notify_structure = array(
              'alert' => $msg,
              'sound' => 'default',
              'badge' => 0,
            );
            sendApnsMessage($notify_structure, $device['device_id']);
          }
        }
      }
    }
    // else{
    //   $this->token_error();
    // }
    
  }

  /***
   *
   *
   **/

  public function withdraw_subscription()
  {
    $notification_id = $_GET['notification_id'];
    $flag = $_GET['flag'];
    if ($flag == true)
    {
      $receiver_token = $this->db->where('user_id', 1)->from('administrators')->select('token')->get()->row_array() ['token'];
      $msg = $this->db->where('notification_id', $notification_id)->from('notification_table')->select('message')->get()->row_array() ['message'];
    }

    else
    {
      $receiver_token = $this->db->where('notification_id', $notification_id)->from('notification_table')->select('sender')->get()->row_array() ['sender'];
      $msg = "your subscription withdrawin request did't accepted!";

    }

    $token = $this->session->userdata('chat_token');
    $this->send_push_notification($token, $this->session->userdata('id') , $receiver_token, 1, $msg);
    echo json_encode('successfully received');
    exit;
  }

  public function map_list()
  {
    $this->data['page'] = 'map_list';
    $this->data['map'] = $this->dashboard->get_payments_info();
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function service_map_list()
  {

    $this->db->select('tab_2.name,tab_1.service_latitude,tab_1.service_longitude,tab_1.service_title')->from('services tab_1');
    $val = $this->db->join('providers tab_2', 'tab_2.id=tab_1.user_id', 'LEFT')->get()->result_array();

    if (!empty($val))
    {

      $result_json = [];

      foreach ($val as $key => $value)
      {
        $temp = $temp2 = [];
        $temp2[] = $value["service_latitude"];
        $temp2[] = $value["service_longitude"];

        $temp['latLng'] = $temp2;
        $temp['name'] = $value['name'];

        $result_json[] = $temp;

      }

    }

    $data = json_encode($result_json);
    print ($data);
  }

  public function users($value = '')
  {
    $this->common_model->checkAdminUserPermission(13);
    $this->data['page'] = 'users';
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function user_details($value = '')
  {
    $this->common_model->checkAdminUserPermission(13);
    $this->data['page'] = 'user_details';
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function users_list($value = '')
  {
    $this->common_model->checkAdminUserPermission(13);
    extract($_POST);

    if ($this->input->post('form_submit'))
    {
      $this->data['page'] = 'users';
      $username = $this->input->post('username');
      $email = $this->input->post('email');
      $from = $this->input->post('from');
      $to = $this->input->post('to');
      $this->data['lists'] = $this->dashboard->user_filter($username, $email, $from, $to);
      $this->load->vars($this->data);
      $this->load->view($this->data['theme'] . '/template');
    }
    else
    {
      $lists = $this->dashboard->users_list();
      $data = array();
      $no = $_POST['start'];
      foreach ($lists as $template)
      {
        $no++;
        $row = array();
        $row[] = $no;
        $profile_img = $template->profile_img;
        if (empty($profile_img))
        {
          $profile_img = 'assets/img/user.jpg';
        }
        $row[] = '<h2 class="table-avatar"><a href="#" class="avatar avatar-sm mr-2"> <img class="avatar-img rounded-circle" alt="" src="' . $profile_img . '"></a>
                    <a href="' . base_url() . 'user-details/' . $template->id . '">' . str_replace('-', ' ', $template->name) . '</a></h2>';

        $row[] = $template->email;
        $row[] = $template->mobileno;
        $created_date = '-';
        if (isset($template->last_login))
        {
          if (!empty($template->last_login) && $template->last_login != "0000-00-00 00:00:00")
          {
            $date_time = $template->last_login;
            $date_time = ($date_time);
            $created_date = date("d M Y", strtotime($date_time));
          }
        }
        $created_at = '-';
        if (isset($template->created_at))
        {
          if (!empty($template->created_at) && $template->created_at != "0000-00-00 00:00:00")
          {
            $date_time = $template->created_at;
            $date_time = ($date_time);
            $created_at = date("d M Y", strtotime($date_time));
          }
        }
        $row[] = $created_at;
        $row[] = $created_date;

        if ($template->status == 1)
        {
          $val = 'checked';
        }
        else
        {
          $val = '';
        }

        if ($template->type == 1)
        {
          $row[] = '';
        }
        else
        {
          $row[] = ' <div class="status-toggle"><input id="status_' . $template->id . '" class="check change_Status_user1" data-id="' . $template->id . '" type="checkbox" ' . $val . '><label for="status_' . $template->id . '" class="checktoggle">checkbox</label></div>';
        }

        $row[] = ' <a href="' . base_url() . 'send-notification/' . $template->token . '" class="btn btn-sm bg-success-light mr-2">
            <i class="fa fa-edit mr-1"></i> Send Message
          </a>';
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->dashboard->users_list_all() ,
        "recordsFiltered" => $this->dashboard->users_list_filtered() ,
        "data" => $data,
      );
      echo json_encode($output);
    }

  }

  public function change_rating()
  {
    $id = $this->input->post('id');
    $status = $this->input->post('status');

    $this->db->where('id', $id);
    $this->db->update('rating_type', array(
      'status' => $status
    ));
  }

  public function change_subcategory()
  {
    $id = $this->input->post('id');
    $status = $this->input->post('status');

    $this->db->where('id', $id);
    $this->db->update('subcategories', array(
      'status' => $status
    ));
  }

  public function change_category()
  {
    $id = $this->input->post('id');
    $status = $this->input->post('status');

    $this->db->where('id', $id);
    $this->db->update('categories', array(
      'status' => $status
    ));
  }

  public function change_Status()
  {
    $id = $this->input->post('id');
    $status = $this->input->post('status');

    $this->db->where('id', $id);
    $this->db->update('users', array(
      'status' => $status
    ));
  }

  /*change delete_users */
  public function delete_users()
  {
    $id = $this->input->post('user_id');
    $status = $this->input->post('status');
    $table_data['status'] = $status;
    $this->db->where('id', $id);
    if ($this->db->update('users', $table_data))
    {
      echo "success";
    }
    else
    {
      echo "error";
    }

  }

  /*change delete_users */
  public function delete_provider()
  {
    $id = $this->input->post('provider_id');
    $status = $this->input->post('status');
    $table_data['status'] = $status;
    $this->db->where('id', $id);
    if ($this->db->update('providers', $table_data))
    {
      echo "success";
    }
    else
    {
      echo "error";
    }

  }
  // Job View
  public function job_view($value = '')
  {
    $this->common_model->checkAdminUserPermission(1);
    $this->data['page'] = 'jobview';
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  //paramesh code
  public function adminusers($value = '')
  {
    $this->common_model->checkAdminUserPermission(1);
    $this->data['page'] = 'adminusers';
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function adminuser_details($value = '')
  {
    $this->common_model->checkAdminUserPermission(1);
    $this->data['page'] = 'adminuser_details';
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }

  public function adminusers_list($value = '')
  {
    $this->common_model->checkAdminUserPermission(1);
    extract($_POST);

    if ($this->input->post('form_submit'))
    {
      $this->session->set_userdata('user_filter', $this->input->post());
      $this->data['page'] = 'adminusers';
      $username = $this->input->post('username');
      $this->data['lists'] = $this->dashboard->adminuser_filter($username);
      $this->load->vars($this->data);
      $this->load->view($this->data['theme'] . '/template');

    }
    else
    {
      $this->session->unset_userdata('user_filter');
      $lists = $this->dashboard->adminusers_list();

      $data = array();
      $no = $_POST['start'];
      foreach ($lists as $template)
      {
        $no++;
        $row = array();
        $row[] = $no;
        $profile_img = $template->profile_img;
        if (empty($profile_img))
        {
          $profile_img = 'assets/img/user.jpg';
        }
        $row[] = '<h2 class="table-avatar"><a href="#" class="avatar avatar-sm mr-2"> <img class="avatar-img rounded-circle" alt="" src="' . $profile_img . '"></a>
      <a href="' . base_url() . 'adminuser-details/' . $template->user_id . '">' . str_replace('-', ' ', $template->full_name) . '</a></h2>';

        $row[] = $template->username;
        $row[] = $template->email;
        $base_url = base_url() . "adminusers/edit/" . $template->user_id;
        if ($template->user_id != 1)
        {
          $row[] = "<a href='" . $base_url . "'' class='btn btn-sm bg-success-light mr-2'>
    <i class='fa fa-edit mr-1'></i> Edit
    </a>
    <a class='btn btn-sm bg-info-light delete_show' data-id='" . $template->user_id . "'><i class='fa fa-trash' ></i> Delete</a>";
        }
        else
        {
          $row[] = "";

        }

        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->dashboard->adminusers_list_all() ,
        "recordsFiltered" => $this->dashboard->adminusers_list_filtered() ,
        "data" => $data,
      );
      echo json_encode($output);
    }
  }

  public function edit_adminusers($id = NULL)
  {
    $this->common_model->checkAdminUserPermission(1);
    if (!empty($id))
    {
      $this->data['user'] = $this->dashboard->get_adminuser_details($id);
      $this->data['title'] = "Edit Admin User";
    }
    else
    {
      $this->data['user'] = array();
      $this->data['title'] = "Add Admin User";
    }

    $this->data['page'] = "edit_adminuser";
    $this->load->vars($this->data);
    $this->load->view($this->data['theme'] . '/template');
  }
  public function update_adminuser()
  {
    $this->common_model->checkAdminUserPermission(1);
    $params = $this->input->post();
    //echo json_encode($params);exit;
    $user_id = '';
    $uploaded_file_name = '';

    if (isset($_FILES) && isset($_FILES['profile_img']['name']) && !empty($_FILES['profile_img']['name']))
    {
      $uploaded_file_name = $_FILES['profile_img']['name'];
      $uploaded_file_name_arr = explode('.', $uploaded_file_name);
      $filename = isset($uploaded_file_name_arr[0]) ? $uploaded_file_name_arr[0] : '';
      $this->load->library('common');
      $upload_sts = $this->common->global_file_upload('uploads/profile_img/', 'profile_img', time() . $filename);
      if (isset($upload_sts['success']) && $upload_sts['success'] == 'y')
      {
        $uploaded_file_name = $upload_sts['data']['file_name'];
      }
    }
    if (!empty($uploaded_file_name))
    {
      $params['profile_img'] = "uploads/profile_img/" . $uploaded_file_name;
    }
    $params['role'] = 1;
    $accesscheck = $params['accesscheck'];
    if (!empty($params['id']))
    {
      $user_id = $params['id'];
      unset($params['id']);
      unset($params['accesscheck']);
      unset($params['selectall1']);
      $result = $this->db->where('user_id', $user_id)->update('administrators', $params);
    }
    else
    {
      $params['password'] = md5($params['password']); //echo json_encode($params['password']);exit;
      unset($params['id']);
      unset($params['accesscheck']);
      unset($params['selectall1']);
      $result = $this->db->insert('administrators', $params);
      $user_id = $this->db->insert_id();
      $token = $this->user->getToken(14, $user_id);
      $this->db->where('user_id', $user_id);
      $this->db->update('administrators', array(
        'token' => $token
      ));
    }
    $module_result = $this->db->where('status', 1)->select('id')->get('admin_modules')->result_array();
    foreach ($module_result as $module)
    {
      $adminparams['admin_id'] = $user_id;
      $adminparams['module_id'] = $module['id'];
      $access_result = $this->db->where('admin_id', $user_id)->where('module_id', $module['id'])->select('id')->get('admin_access')->result_array();
      if (in_array($module['id'], $accesscheck))
      {
        $adminparams['access'] = 1;
      }
      else
      {
        $adminparams['access'] = 0;
      }
      if (!empty($access_result))
      {
        $result = $this->db->where('id', $access_result[0]['id'])->update('admin_access', $adminparams);
      }
      else
      {
        $result = $this->db->insert('admin_access', $adminparams);
      }

    }
    if ($result == true)
    {
      if (empty($user_id))
      {
        echo json_encode(['status' => true, 'msg' => "Admin Userdetails Added SuccesFullly..."]);
      }
      else
      {
        echo json_encode(['status' => true, 'msg' => "Admin Userdetails Updated SuccesFullly..."]);
      }
    }
    else
    {
      echo json_encode(['status' => false, 'msg' => "Someting Went in Server end..."]);
    }
  }

  public function check_adminuser_name()
  {
    $name = $this->input->post('name');
    $id = $this->input->post('id');
    if (!empty($id))
    {
      $this->db->select('*');
      $this->db->where('username', $name);
      $this->db->where('user_id !=', $id);
      $this->db->from('administrators');
      $result = $this->db->get()->num_rows();
    }
    else
    {
      $this->db->select('*');
      $this->db->where('username', $name);
      $this->db->from('administrators');
      $result = $this->db->get()->num_rows();
    }
    if ($result > 0)
    {
      $isAvailable = false;
    }
    else
    {
      $isAvailable = true;
    }
    echo json_encode(array(
      'valid' => $isAvailable
    ));
  }

  public function check_adminuser_email()
  {
    $email = $this->input->post('email');
    $id = $this->input->post('id');
    if (!empty($id))
    {
      $this->db->select('*');
      $this->db->where('email', $email);
      $this->db->where('user_id !=', $id);
      $this->db->from('administrators');
      $result = $this->db->get()->num_rows();
    }
    else
    {
      $this->db->select('*');
      $this->db->where('email', $email);
      $this->db->from('administrators');
      $result = $this->db->get()->num_rows();
    }
    if ($result > 0)
    {
      $isAvailable = false;
    }
    else
    {
      $isAvailable = true;
    }
    echo json_encode(array(
      'valid' => $isAvailable
    ));
  }

  public function adminuser_delete()
  {
    $this->common_model->checkAdminUserPermission(1);
    $params = $this->input->post();
    if (!empty($params['id']))
    {
      $result = $this->db->where('user_id', $params['id'])->delete('administrators');
      if ($result == true)
      {
        echo json_encode(['status' => true, 'msg' => "Admin User Deleted SuccessFully..."]);
      }
      else
      {
        echo json_encode(['status' => false, 'msg' => "Something Went in server end..."]);
      }
    }
  }

  //Export Excel
  public function adminusers_export()
  {
    $this->common_model->checkAdminUserPermission(1);
    $style = array(
      'borders' => array(
        'allborders' => array(
          'style' => Border::BORDER_MEDIUM,
          'color' => array(
            'argb' => '006200'
          ) ,
        ) ,
      ) ,
      'fill' => array(
        'type' => Fill::FILL_SOLID,
        'color' => array(
          'rgb' => '006200'
        )
      ) ,
      'font' => array(
        'bold' => true
      )
    );
    $fileName = 'users.xlsx';
    $service_filter = $this->session->userdata('user_filter');

    if ($service_filter['form_submit'] == "submit")
    {

      $username = $service_filter['username'];

      $list = $this->dashboard->get_adminusers_filter($username);

    }
    else
    {
      $list = $this->dashboard->get_adminusers_list();
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'User Name');
    $sheet->setCellValue('C1', 'Full Name');

    $sheet->getStyle('A1:H1')->applyFromArray($style);
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);

    $rows = 2;

    foreach ($list as $val)
    {
      $sheet->setCellValue('A' . $rows, $val['user_id']);
      $sheet->setCellValue('B' . $rows, $val['username']);
      $sheet->setCellValue('C' . $rows, $val['full_name']);
      $rows++;
    }
    $writer = new Xlsx($spreadsheet);
    $writer->save("uploads/service_excel/" . $fileName);
    header("Content-Type: application/vnd.ms-excel");
    redirect(base_url() . "/uploads/service_excel/" . $fileName);
  }
  
}
