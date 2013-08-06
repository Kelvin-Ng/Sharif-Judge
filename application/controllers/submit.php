<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file submit.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Submit extends CI_Controller{
	var $data; //data sent to view
	var $username;
	var $user_level;
	var $assignment;
	var $assignment_root;
	var $problems;
	var $problem;//submitted problem id
	var $file_type; //type of submitted file
	var $ext; //uploaded file extension
	var $file_name; //uploaded file name without extension

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->load->library('upload');
		$this->load->model('queue_model');
		$this->username = $this->session->userdata('username');
		$this->user_level = $this->user_model->get_user_level($this->username);
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->assignment_root = $this->settings_model->get_setting('assignments_root');
		$this->problems = $this->assignment_model->all_problems($this->assignment['id']);
	}


	public function _check_filetype($str){
		if ($str=="0")
			return FALSE;
		if (in_array($str,array('c','cpp','java','zip')))
			return TRUE;
		return FALSE;
	}


	public function index(){
		$this->data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'problems' => $this->problems,
			'title'=>'Submit',
			'style'=>'main.css',
			'in_queue'=>FALSE,
			'upload_state'=>''
		);
		$this->form_validation->set_message('greater_than',"Select a %s.");
		$this->form_validation->set_message('_check_filetype',"Select a valid %s.");
		$this->form_validation->set_rules('problem','problem','required|integer|greater_than[0]');
		$this->form_validation->set_rules('filetype','file type','required|alpha_numeric|callback__check_filetype');

		if ($this->form_validation->run()){
			$this->_upload();
		}

		$this->load->view('templates/header',$this->data);
		$this->load->view('pages/submit',$this->data);
		$this->load->view('templates/footer');
	}

	private function _upload(){
		$now = shj_now();
		foreach($this->problems as $item)
			if ($item['id']==$this->input->post('problem')){
				$this->problem = $item;
				break;
			}
		$this->file_type = $this->input->post('filetype');
		$this->ext = substr(strrchr($_FILES['userfile']['name'],'.'),1); // uploaded file extension
		$this->file_name = basename($_FILES['userfile']['name'], ".{$this->ext}"); // uploaded file name without extension
		if ( $this->queue_model->in_queue($this->username,$this->assignment['id'],$this->problem['id']) )
			die('<p>You have submitted for this problem already. Your last submission is still in queue.</p>');
		if ($this->user_model->get_user_level($this->username)==0 && !$this->assignment['open'])
			die('<p>Selected assignment has been closed.</p>');
		if ($now < strtotime($this->assignment['start_time']))
			die('<p>Selected assignment has not started.</p>');
		if ($now > strtotime($this->assignment['finish_time'])+$this->assignment['extra_time'])
			die('<p>Selected assignment has finished.</p>');
		if ( !$this->assignment_model->is_participant($this->assignment['participants'],$this->username) )
			die('<p>You are not registered for submitting.</p>');
		$filetypes = explode(",",$this->problem['allowed_file_types']);
		foreach ($filetypes as &$filetype){
			$filetype = trim($filetype);
		}
		if ($_FILES['userfile']['error']==4)
			die('<p>No file chosen.</p>');
		if (!in_array($this->file_type,$filetypes))
			die('<p>This file type is not allowed for this problem.</p>');
		if ($this->file_type !== $this->ext)
			die('<p>This file type does not match your selected file type.</p>');
		if ( preg_match('/[^\x20-\x7f]/', $_FILES['userfile']['name']))
			die('<p>Invalid characters in file name.</p>');

		$user_dir=rtrim($this->assignment_root,'/')."/assignment_".$this->assignment['id']."/p".$this->problem['id'].'/'.$this->username;
		if(!file_exists($user_dir))
			mkdir($user_dir,0700);

		$config['upload_path'] = $user_dir;
		$config['allowed_types'] = '*';
		$config['max_size']	= $this->settings_model->get_setting('file_size_limit');
		$config['file_name'] = $this->file_name."-".($this->assignment['total_submits']+1).".".$this->ext;
		$config['max_file_name']=20;
		$config['remove_spaces']=TRUE;
		$this->upload->initialize($config);

		if($this->upload->do_upload('userfile')){
			$result = $this->upload->data();
			$this->load->model('submit_model');

			$submit_info = array(
				'submit_id' => $this->assignment_model->add_total_submits($this->assignment['id']),
				'username' => $this->username,
				'assignment' => $this->assignment['id'],
				'problem' => $this->problem['id'],
				'file_name' => $result['raw_name'],
				'main_file_name' =>$this->file_name,
				'file_type' => ltrim($result['file_ext'],'.')
			);
			if($this->problem['judge']){
				$this->queue_model->add_to_queue($submit_info);
				exec("php ".rtrim($this->settings_model->get_setting('tester_path'),'/')."/queue_process.php >/dev/null 2>/dev/null &");
			}else{
				$this->submit_model->add_upload_only($submit_info);
			}

			$this->data['upload_state']='ok';
		}
		else
			$this->data['upload_state']='error';
	}

}