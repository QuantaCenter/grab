<?php
class Course {
	private $username;
	private $name;
	private $cookie;
	private $content;

	public function __construct($username){
		$this->cookie = dirname(__FILE__).'/cookies/'.$username.'.txt';
	}

	/**
	 * 从返回的内容中提取出cookie
	 * @param String $responseHeader
	 */
	public function parseHost($url){
		$parttern = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
		$url = trim($url);
		preg_match($parttern, $url, $match);
		return $match[4];
	}

	/**
	 * 验证登录,获取登录状态
	 * @param String $url
	 * @param Array $field
	 */
	public function login($url,$field){	
		$param = '';
		foreach ($field as $key => $value){
			$param .= $key."=".urlencode($value)."&";
		}
		$param = substr($param, 0,-1);
		$host = $this->parseHost($url);
		$origin = 'http://'.$host;
		$this->username = $field['username'];
		
		$header = array(
			'POST /pkmslogin.form HTTP/1.1',
			'Host: '.$host,
			'Connection: keep-alive',
			'Content-Length: '.strlen($param),
			'Cache-Control: max-age=0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Origin: '.$origin,
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.12 Safari/537.31',
			'Content-Type: application/x-www-form-urlencoded',
			'Accept-Encoding: gzip,deflate,sdch',
			'Accept-Language: zh-CN,zh;q=0.8',
			'Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3'
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		$content = curl_exec($ch);
		
		curl_close($ch);

		$pattern = "/Your login was successful========./";

		if(preg_match($pattern, $content)){
			return $this->getName();
		}
		return false;
	}

	/**
	 * 获取用户姓名
	 */
	private function getName(){
		$url = "http://jw.gdufs.edu.cn/xs_main.aspx?xh=".$this->username;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
	
		ob_start();
		curl_exec($ch);
		$content = ob_get_contents();
		ob_end_clean();
		
		curl_close($ch);

		$pattern = "/<span id=\"xhxm\">\d*(.*)<\/span>/";
		if(preg_match($pattern, $content, $match)){
			$result = trim($match[1]);
			$this->name = substr($result, 0, strlen($result)-4);
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * 返回登录后获取的用户名及学号
	 */
	public function loginResult(){
		$res = Array();
		$res['username'] = $this->username;
		$res['name'] = $this->name;
		return $res;
	}

	/**
	 * 返回课程列表
	 * @param String $url
	 */
	public function showCourse($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
	
		ob_start();
		curl_exec($ch);
		$content = ob_get_contents();
		ob_end_clean();
		
		curl_close($ch);

		$content = str_replace(array("\r","\n"), "", $content);
		$pattern = '#<input type=\"hidden\" name=\"(.*?)\" value=\"(.*?)\"#';
		$input = "";
		if (preg_match_all($pattern, $content, $matches)){
			$arr = array(
					'__EVENTTARGET'=>$matches[2][0],
					'__EVENTARGUMENT'=>$matches[2][1],
					'__VIEWSTATE'=>$matches[2][2]
					);
		}
		foreach ($arr as $key => $value) {
			$input .= $key."=>".$value."&";
		}
		$input = substr($input, 0, -1);
		$pattern = "/<table.*?>(.*?)<\/table>/";
		if(preg_match_all($pattern, $content, $match)){
			$res = array();
			$res['list'] = preg_replace("/<a.*?>/", "", $match[0][0]);
			$res['my'] = preg_replace("/<a.*?>/", "", $match[0][1]);
			$res['my'] = str_replace("submit", "button", $res['my']);
			$res['input'] = $input;
			return $res;
		}
		else{
			return 0;
		}
	}

	/**
	 * 提交表单
	 * @param String $url
	 * @param Array $form
	 */
	function submitForm($url, $form){
		$param = '';
		foreach ($form as $key => $value){
			$param .= "$key=".urlencode($value)."&";
		}
		$param = substr($param, 0,-1);
		$host = $this->parseHost($url);
		$origin = 'http://'.$host;
		$post = split("/",$url);
		
		$header = array(
			'POST /'.$post[count($post)-1].' HTTP/1.1',
			'Host: '.$host,
			'Connection: keep-alive',
			'Content-Length: '.strlen($param),
			'Cache-Control: max-age=0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Origin: '.$origin,
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.12 Safari/537.31',
			'Content-Type: application/x-www-form-urlencoded',
			'Referer: '.$url,
			'Accept-Encoding: gzip,deflate,sdch',
			'Accept-Language: zh-CN,zh;q=0.8',
			'Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3',
			
		);
		
// 		var_dump($header);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		// 抓取URL并把它传递给浏览器
		$content = curl_exec($ch);
		
		curl_close($ch);

		// var_dump($content);
		return $content;
	}
}
?>