<?php
class Defaultdata extends CI_Model {

	private $data=array();
	private $mydata=array();
	private $footerdata=array();
	private $headerdata=array();
	public $chat_data = array();
	function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('languageID'))
		{
			$this->session->set_userdata('languageID',2);
		}
	}
    
    public function convertToHoursMins($time, $format = '%02d:%02d') {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }
    
	public function getFrontendDefaultData()
	{
		$all_segment= $this->getUrlSegments();
		$this->mydata["tot_segments"]=$all_segment;
		$this->mydata['general_settings'] = $this->grabSettingData();
		$this->data=$this->mydata;
		$this->headerdata=$this->mydata;
		$this->footerdata=$this->mydata;

		$panel = 'admin';
		if($this->session->userdata('usrtype')==1){ $panel = 'admin'; }
		elseif($this->session->userdata('usrtype')==2){ $panel = 'agent'; }
		elseif($this->session->userdata('usrtype')==3){ $panel = 'solicitor'; }
		elseif($this->session->userdata('usrtype')==5){ $panel = 'consultant'; }

		$this->data["header_scripts"]=$this->load->view($panel.'/includes/header_scripts',$this->mydata,true);
		$this->headerdata['all_langueges'] = $this->geAllLanguages();

		$this->data["unread_msg"] = $this->headerdata["unread_msg"] = array();
		if($this->session->userdata('usrid'))
		{
			$this->load->model('chatdata');
			$to_user_id = $this->session->userdata('usrid');

			$this->data['user_details'] = $this->headerdata["user_details"] = $this->userdata->grabUserData(array('id'=>$to_user_id));

			$this->data["unread_msg"] = $this->headerdata["unread_msg"] = $this->chatdata->getAllUnreadMessege($to_user_id);

		}
		
		
		$this->data["header"]=$this->load->view($panel.'/includes/header',$this->headerdata,true);
		$this->data["left_sidebar"]=$this->load->view($panel.'/includes/left_sidebar',$this->mydata,true);
		$this->data["right_sidebar"]=$this->load->view($panel.'/includes/right_sidebar',$this->mydata,true);
		$this->data["footer"]=$this->load->view($panel.'/includes/footer',$this->footerdata,true);
		$this->data["footer_scripts"]=$this->load->view($panel.'/includes/footer_scripts',$this->mydata,true);
		
		return $this->data;
	}
	
	public function geAllLanguages()
	{
		$this->db->where('status','Y');
		$this->db->order_by('weight','ASC');
		return $this->db->get(TABLE_ALLLANGUAGE)->result();
	}
	public function grabLanguage($languageID)
	{
		$this->db->where('id',$languageID);
		return $this->db->get(TABLE_ALLLANGUAGE)->row();
	}
	public function getMaxTypeId($table)
	{
		$this->db->select_max('typeID');
		$arr = $this->db->get($table)->row();
		$new_typeID = 100;
		if($arr->typeID != '')
		{
			$new_typeID = $arr->typeID + 1;
		}
		return $new_typeID;
	}
	public function is_session_active()
	{
		//session_start();
		$sess_id = $this->session->userdata('usrid');
		//$sess_usr_type=$this->session->userdata('usrtype');
		if (isset($sess_id)==true && $sess_id!="")
			return 1;
		else
			return 0;
	}
	public function CheckFilename($page_filename)
	{
		$page_filename=str_replace(" ","-",$page_filename); //blank space is converted into blank
		$special_char=array("/",".htm",".","!","@","#","$","^","&","*","(",")","=","+","|","\\","{","}",":",";","'","<",">",",",".","?","\"","%");
		$page_filename=str_replace($special_char,"",$page_filename); // dot is converted into blank
		return strtolower($page_filename);
	}
	public function getUrlSegments()
	{
		$all_segment=$this->uri->segment_array();
		if(sizeof($all_segment)==0)
		{
			$all_segment[1]=$this->router->class;
		}
		if(sizeof($all_segment)==1)
		{
			$all_segment[2]=$this->router->method;
		}
		return $all_segment;
	}
	
	public function returnPartString($string,$length)
	{
		$string = strip_tags($string);
		$s_length=strlen($string);
		if($s_length > $length)
		{
			if(strpos($string," ",$length) !== false)
			{
				$string=substr($string,0,strpos($string," ",$length));
			}
			else
			{
				$string=substr($string,0,$length);
			}
		}
		else
		{
			$string=$string;
		}
		return stripslashes($string);
	}
	public function grabSettingData(){
		$query = $this->db->get(TABLE_GENERAL_SETTINGS);
		return $query->row();
	}
	public function getAllCountry()
	{
		$this->db->order_by('countryName','asc');
		$query = $this->db->get(TABLE_COUNTRIES);
		return $query->result();
	}
    public function getAllCounties()
	{
        $query = $this->db->query("SELECT cc.*,cl.title, cl.type_id, cl.language_id  FROM com_counties cc INNER JOIN ( SELECT * FROM com_language ORDER BY title ASC) cl  ON cl.type_id = cc.language_type_id WHERE language_id = ".$this->session->userdata('languageID'));

		return $query->result();
	}
	public function grabCountry($c_cond = array())
	{
		if(count($c_cond) > 0)
		{
			$this->db->where($c_cond);
			$query = $this->db->get(TABLE_COUNTRIES);
			return $query->row();
		}
		else
		{
			return array();
		}
	}
	public function secureInput($data)
	{
		$return_data = array();
		foreach($data as $field => $inp_data)
		{
			//$return_data[$field]=$this->db->escape_str($inp_data);
			$return_data[$field] = $this->security->xss_clean(trim($inp_data));
		}
		return $return_data;
	}
	public function setLoginSession($user_data = array())
	{
		if(count($user_data) > 0)
		{
			$this->session->set_userdata('usrid',$user_data->id);
			$this->session->set_userdata('userPhone',$user_data->phone);
			$this->session->set_userdata('usremail',$user_data->emailAddress);
			$this->session->set_userdata('usrtype',$user_data->userType);
			$this->session->set_userdata('parentID',$user_data->parent_id);
			$this->session->set_userdata('usrname',$user_data->userName);
			$this->session->set_userdata('usr_name',$user_data->name);
			$this->session->set_userdata('languageID',$user_data->language);
		}
	}
	public function unsetLoginSession()
	{
		$this->session->unset_userdata('usrid');
		$this->session->unset_userdata('usremail');
		$this->session->unset_userdata('usrtype');
		$this->session->unset_userdata('parentID');
		$this->session->unset_userdata('usrname');
		$this->session->unset_userdata('usr_name');
		$this->session->unset_userdata('userPhone');
	}
	public function getGplusLoginUrl()
	{
		require_once APPPATH .'libraries/google-api-php-client-master/src/Google/autoload.php';
		$client_id = $this->config->item('client_id','googleplus');
		$client_secret = $this->config->item('client_secret','googleplus');
		$redirect_uri = $this->config->item('redirect_uri','googleplus');
		$simple_api_key = $this->config->item('api_key','googleplus');
		
		// Create Client Request to access Google API
		$client = new Google_Client();
		$client->setApplicationName("PHP Google OAuth Login Example");
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->setDeveloperKey($simple_api_key);
		$client->addScope("https://www.googleapis.com/auth/userinfo.email");
		$authUrl = $client->createAuthUrl();
		return $authUrl;
	}
	
	public function getGeneratedPassword( $length = 6 ) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
		$password = substr( str_shuffle( $chars ), 0, $length );
		return $password;
	}

	

	public function get_results($table,$where="",$order="",$limit="",$start="")
	{
		if($where)
		{
			$this->db->where($where);
		}
		if($order)
		{
			foreach($order as $key=>$val){
				$this->db->order_by($key,$val);
			}
			
		}
		if($limit)
		{
			$this->db->limit($limit,$start);
		}
		return $this->db->get($table)->result();
		
	}

	public function get_single_row($table,$where="",$select="")
	{
		if($select!="")
		{
			$this->db->select($select);
		}
		if($where)
		{
			$this->db->where($where);
		}
		return $this->db->get($table)->row();
		
	}
	public function getField($table,$where,$select)
	{
		$result = $this->db->select($select)->where($where)->get($table)->row();
		if(count($result) > 0) {
			return $result->$select;
		} else {
			return '';
		}
		
	}

     public function getTranslatedField($table,$id)
    {
        $sql = "SELECT cl.title FROM `".$table."` cc INNER JOIN com_language cl ON cc.language_type_id = cl.type_id  WHERE  language_id = ".$this->session->userdata('languageID');
        if($id){
            $sql.=" AND cc.id = ". $id; //cc.id = 1 AND
        }else{
            return '';
        }
        $query = $this->db->query($sql);

        $row = $query->row();

        if(isset($row)) {
            return $row->title;
        } else {
            return '';
        }
    }
	public function count_record($table,$where="")
	{
		if($where){
			$record = $this->db->where($where)->count_all_results($table);
			
		}else{
			$record = $this->db->count_all($table);
		}
		return $record;
	}

	public function insert($tbl,$data = array())
	{
		if(count($data) > 0){
			$this->db->insert($tbl, $data);
			 return $this->db->insert_id();
		}else{
			return 0;
		}
	}
	public function delete($tbl, $cond=array())
	{
		$res = $this->db->delete($tbl, $cond);
		if ($this->db->count_all($tbl)==0)
		{
			$this->db->truncate($tbl);
		}
		return $res;
	}
	public function update($tbl,$data,$condition)
	{
		return $this->db->update($tbl, $data, $condition);
	}

	function sendMail($to, $subject, $message, $from, $from_name, $cc=array(), $attach=array(), $bcc=array())
	{
		$this->load->library('email');

		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'smtp.eu.mailgun.org';
		$config['smtp_user'] = '';
		$config['smtp_pass'] = '';
		$config['smtp_crypto'] = 'ssl';
		$config['smtp_port'] = '465';
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';

		//$this->email->clear(TRUE);
		//var_dump($config); die;
        $this->email->initialize($config);
        $this->email->from($from, $from_name);
        $this->email->to($to);
        if(count($cc)>0)
        {
        	$this->email->cc($cc);
        }

        if(count($bcc)>0)
        {
        	$this->email->bcc($bcc);
        }

        $this->email->subject($subject);
        $this->email->message($message);
        if(count($attach)>0)
        {
        	foreach($attach as $file)
        	{
        		$this->email->attach($file);
        	}
        }
        return $this->email->send();
	}

	public function gradLanguageText($typeID)
	{
		$stat_data=array();
		$stat_data['language_id']=$this->session->userdata('languageID');
		$stat_data['type_id']=$typeID;
		
		$return_data=array();
		
		$query=$this->db->get_where(TABLE_LANGUAGE,$stat_data);
		$return_data=$query->row();
		
		return $return_data->title;
	}

	public function sendDefaultMailGun()
	{
        if(!class_exists("Mailgun"))
            $this->requireMailGun();

        $domain = MAILGUN_DOMAIN;
        $mg = Mailgun\Mailgun::create(MAILGUN_API_KEY, 'https://api.eu.mailgun.net/v3/'.$domain); // For EU servers
        $result = $mg->messages()->send($domain, [
		  'from'    => 'kontakt@'.$domain,
		  'to'      => '',
		  'subject' => 'The PHP SDK is awesome!',
		  'text'    => 'It is so simple to send a message.'
		]);
	}
    
    private function requireMailGun()
	{
		require APPPATH.'third_party/mailgun/vendor/autoload.php';
	}
}
?>
