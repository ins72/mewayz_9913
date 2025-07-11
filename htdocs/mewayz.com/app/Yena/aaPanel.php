<?php

namespace App\Yena;

class aaPanel {
	private $key = "";
  	private $host = "";
	public function __construct(){
		$this->key = config('app.aapanel_api');
		$this->host = config('app.aapanel_host');
	}
	
	public function GetLogs(){
		//拼接URL地址
		$url = $this->host.'/data?action=getData';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['table'] = 'logs';
		$p_data['limit'] = 10;
		$p_data['tojs'] = 'test';
		
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	
	public function getData(){
		$url = $this->host.'/data?action=getData';
		
		$data = $this->GetKeyData();
		$data['p'] = 1;
		$data['limit'] = 10;
		$data['table'] = 'test';
		
		$result = $this->HttpPostCookie($url,$data);
		
		$data = json_decode($result,true);
      	return $data;
	}
	
	public function getSites(){
		$url = $this->host.'/data?action=getData';
		
		$data = $this->GetKeyData();
		$data['p'] = 1;
		$data['limit'] = 10;
		$data['table'] = 'sites';
		$data['search'] = config('app.aapanel_website');
		$data['order'] = '';
		$data['type'] = -1;
		
		$result = $this->HttpPostCookie($url,$data);
		
		$data = json_decode($result,true);
      	return $data;
	}
	
	public function getSite(){
		$res = null;

		$getSites = $this->getSites();
		foreach (ao($getSites, 'data') as $item) {
			if(ao($item, 'rname') == config('app.aapanel_website')) $res = $item;
		}

      	return $res;
	}
	
	public function AddDomain($domain){
		$url = $this->host.'/site?action=AddDomain';

		$getSite = $this->getSite();
		if(!$getSite) return;
		
		$data = [
			...$this->GetKeyData(),
			'id' => ao($getSite, 'id'),
			'webname' => config('app.aapanel_website'),
			'domain' => $domain,
			'port' => '80',
		];
		
		$result = $this->HttpPostCookie($url, $data);
		$data = json_decode($result,true);
      	return $data;
	}
	
	public function RemoveDomain($domain){
		$url = $this->host.'/site?action=DelDomain';

		$getSite = $this->getSite();
		if(!$getSite) return;
		
		$data = [
			...$this->GetKeyData(),
			'id' => ao($getSite, 'id'),
			'webname' => config('app.aapanel_website'),
			'domain' => $domain,
			'port' => '80',
		];
		
		$result = $this->HttpPostCookie($url, $data);
		$data = json_decode($result,true);
      	return $data;
	}
	
	public function cert($domain){
		$url = $this->host.'/acme?action=apply_cert_api';

		$getSite = $this->getSite();
		if(!$getSite) return;
		
		$data = [
			...$this->GetKeyData(),
			'id' => ao($getSite, 'id'),
			'auth_type' => 'http',
			'auth_to' => '1',
			'auto_wildcard' => '0',
			'domains' => json_encode([
				'den.tinahatzky.de'
			]),
		];
		
		$result = $this->HttpPostCookie($url, $data);
		$data = json_decode($result,true);

		dd($data);
      	return $data;
	}
	
  	private function GetKeyData(){
  		$now_time = time();
    	$p_data = array(
			'request_token'	=>	md5($now_time.''.md5($this->key)),
			'request_time'	=>	$now_time
		);
    	return $p_data;    
    }
  	
  
  	/**
     * 发起POST请求
     * @param String $url 目标网填，带http://
     * @param Array|String $data 欲提交的数据
     * @return string
     */
    private function HttpPostCookie($url, $data,$timeout = 60)
    {
        $cookie_file='./'.md5($this->host).'.cookie';
        if(!file_exists($cookie_file)){
            $fp = fopen($cookie_file,'w+');
            fclose($fp);
        }
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
	
	private function HttpPostFormData($url, $data, $timeout = 60)
	{
		$cookie_file = './' . md5($this->host) . '.cookie';
		
		if (!file_exists($cookie_file)) {
			file_put_contents($cookie_file, '');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Convert data to URL-encoded query string
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}
}
