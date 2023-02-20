<?php

require_once(__DIR__ . '/pull/get.php');

class rcvGitWebHook {
	
	const secf = '/var/kwynn/gitwebhook_secret';
	
	public function __construct() {
		$this->init10();
		$this->do10();
	}
	
	private function init10() {
		$this->loadSecret();
	}
	
	private function loadSecret() {
		$t = trim(file_get_contents(self::secf));
		$this->osec = $t;
	}
	
	private function do10() {
		$s = file_get_contents('php://input');
		// $this->logf($s);
		$h = getallheaders();
		$this->logf('1');
		$secr = kwifs($h, 'X-Hub-Signature-256');
		if ($secr) $secr = substr($secr, 7); // sha256=blahblahblah
		$this->logf('2');
		$secf = $this->osec;
		$vars = get_defined_vars(); unset($vars['s']);
		$this->logf(json_encode($vars));
		$this->logf($s);
		kwas(hash_hmac('sha256', $s, $secf) === $secr, 'bad data web hook 0203');
		$this->logf('3');
		$this->logf('hmac pass');
		GitGet::putOne($s);
		$this->logf('after DB put');		
	}
	
	private function logf(string $s) {
		
		static $here;

		if (iscli()) return;
		
		if (!$here) { 
			file_put_contents('/tmp/whrfg', '');
			$here = true;
		}
		

		file_put_contents('/tmp/whrfg', $s . "\n", FILE_APPEND);
	}
}

new rcvGitWebHook();
