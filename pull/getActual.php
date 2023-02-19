<?php

require_once('/opt/kwynn/kwutils.php');

class github_api_user_repo_get {
    
    const url = 'https://api.github.com/users/kwynncom/repos';
    const iterLimit = 12; // This limits the number of repos, but I want it here for now to prevent infinite loops
    
    public static function get() {
		$o = new self();
		return $o->getI();
    }
    
    private function __construct() { $this->get10();  }
    
    public function getI() { return $this->rawa; }
    
    private function get10() {

		$i = 0;
		$this->rawa = [];
		$url = self::url;
		do {
			$htr = $this->getActualActual($url);
			$nu = $this->p10($htr);
			if (!$nu) return;
			$url = $nu;
		} while ($i++ < self::iterLimit);
    }
    
    private function p10($rin) {
		$hba = explode("\r\n\r\n", $rin);
		$ta = json_decode($hba[1], 1);
		if ($ta) $this->rawa = array_merge($this->rawa, $ta);
		$h = $hba[0];
		// link: <https://api.github.com/user/14192685/repos?page=2>; rel="next", <https://api.github.com/user/14192685/repos?page=2>; rel="last"
		$mr = preg_match('/\blink\: <([^>]+)>; rel="next", <([^>]+)>; rel="last"/', $h, $ma);
		if (!$mr) return false;
		return $ma[1];
		
    }
    
    private function getActualActual($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$h = ['User-Agent: curl/PHP'];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$htr = curl_exec($ch);

		file_put_contents('/tmp/git', $htr);

		return $htr;	
    }
}

if (didCLICallMe(__FILE__)) github_api_user_repo_get::get();
