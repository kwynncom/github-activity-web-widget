<?php

require_once('/opt/kwynn/kwutils.php');
require_once('getActual.php');

class GitGetAct extends dao_generic_3 {
    
    const dbname = 'repos';
	
	const uaf =   'pushed_at';
	const gfs =  [self::uaf, 'html_url', /* 'description' , 'id', 'node_id', 'created_at' */];
	
    public function __construct(string $act = '')  {
		parent::__construct(self::dbname);
		self::creTabs('repos');
		if (didCLICallMe(__FILE__)) $this->pull();
		return;
    }
	
	private function pull() {
		$this->regGet();
		$this->save();		
	}
	
	public static function putOne(string $s) {
		$a = json_decode($s, true); 	kwas($a, 'bad format webhook - 0142');
		$o = new self();
		$o->putOneI($a['repository']);
		
	}
	
	private function save() { foreach($this->rawa as $r) $this->putOneI($r);	}
	
	public function putOneI(array $r) { 
		
		$t = [];
		foreach(self::gfs as $f) {
			// $t[self::uaf . '_U'] = strtotime($w[self::uaf]);
			if ($f === self::uaf) $this->popTime($r[$f], $t);
			else $t[$f] = $r[$f];
		}

		$t['_id'] = basename($r['html_url']); unset($r);

		
		
		$dbr = $this->rcoll->upsert(['_id' => $t['_id']], $t);
		$s = 'modified count: ' . $dbr->getModifiedCount() . " $t[_id] \n";
		file_put_contents('/tmp/gwh', $s, FILE_APPEND);
		if (iscli()) echo($s);
	}
	
	private function popTime(int | string $t, array &$a) {
		if (is_integer($t)) {
			$a[self::uaf . '_U'] = $t;
			$a[self::uaf . '_r' ] = date('r', $t);
			return;
		}
		
		$a[self::uaf . '_U'] = strtotime($t);
		$a[self::uaf . '_r'] = $t;
	}

    private function regGet() {
		$this->rawa = github_api_user_repo_get::get();
    }
	
}

if (didCLICallMe(__FILE__)) new GitGetAct();
