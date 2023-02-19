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
		$secr = $h['X-Hub-Signature-256'];
		$this->logf('2');
		kwas(hash_hmac('sha256', $s, $this->osec) === $secr, 'bad data web hook 0203');
		$this->logf('3');
		$this->logf('hmac pass');
	}
	
	private function logf(string $s) {
		if (iscli()) return;
		file_put_contents('/tmp/whrfg', $s . "\n", FILE_APPEND);
	}
}

new rcvGitWebHook();
