<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class demo_captcha extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('captcha','url'));
		$this->load->database();
	}
	function gbr()
	{
		$vals = array(
		'img_path' => './captcha/',
		'img_url' => base_url().'captcha/',
		'font_path' => './system/fonts/impact.ttf',
		'img_width' => '200',
		'img_height' => 60,
		'expiration' => 90
		);
	
		$cap = create_captcha($vals);
		
		$data = array(
			'captcha_time' => $cap['time'],
			'ip_address' => $this->input->ip_address(),
			'word' => $cap['word']
			);
		$expiration = time()-90;
		$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
		$query = $this->db->insert_string('captcha', $data);
		$this->db->query($query);
		echo $cap['image'];
	}

	function index()
	{
		?>
			<script src="<?php echo base_url(); ?>asset/js/jquery.js"></script>
			<script>
			$(function() {
				$('#loading').ajaxStart(function(){
					$(this).fadeIn();
				}).ajaxStop(function(){
					$(this).fadeOut();
				});
			
				$('a').click(function() {
					var url = $(this).attr('href');
					$('#gbr').load(url);
					return false;
				});
			});
			function firstCaptcha()
			{
				$('#gbr').load('<?php echo base_url(); ?>index.php/demo_captcha/gbr');
			}
			</script>
			<style>
			body{
				font-size:12px;
				font-family:Arial;
				margin:0px auto;
			}
			#content{
				width:300px;
				margin:0px auto;
				padding:10px;
			}
			a{
				text-decoration:none;
				color:#FF0000;
				font-weight:bold;
			}
			a:hover{
				text-decoration:underline;
				color:#FF9900;
				font-weight:bold;
			}
			#loading{
				position:static;
				color:#FFFFFF;
				font-weight:bold;
				margin:0px auto;
				width:300px;
				padding:10px;
				background-color:#FF0000;
				display:none;
			}
			</style>
			<title>Demo Captcha Pada Web Berbasis CodeIgniter</title>
			<body onload="firstCaptcha()">
		<?php
		echo '<div id="loading">Memuat Captcha</div><div id="content"><div id="gbr"></div>
		<a href="'.base_url().'index.php/demo_captcha/gbr">Refresh Captcha</a>
		<form method="post" action="'.base_url().'index.php/demo_captcha/validasi">
		<input type="text" name="captcha" value="" /><input type="submit" value="Cek Captcha">
		</form></div>';
		?>
		</body>
		<?php
		
	}
	function validasi()
	{
		$expiration = time()-90;
		$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
		
		$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();
		
		if ($row->count == 0)
		{
			echo "Kode Captcha yang anda masukkan tidak Valid...!!!";
		}
		else
		{
			echo "Selamat, Kode Captcha Benar...!!!";
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */