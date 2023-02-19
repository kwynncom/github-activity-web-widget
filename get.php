<?php

require_once('/opt/kwynn/kwutils.php');
require_once('getActual.php');

class GitGetAct extends dao_generic_3 {
    
    const dbname = 'repos';
    const quotaMinutes = 15;
	
	const uaf =   'pushed_at';
	const gfs =  [self::uaf, 'html_url', /* 'description' , 'id', 'node_id', 'created_at' */];

   
    public function __construct()  {
		parent::__construct(self::dbname);
		self::creTabs('repos');

		$this->regGet();
		$this->p10();
		$this->save();
		return;
    }
	
	private function save() { foreach($this->thea as $r) $this->rcoll->upsert(['_id' => $r['_id']], $r); }

    private function p10() {


		$r = [];
		foreach($this->rawa as $w) {
			$t = [];
			foreach(self::gfs as $f) $t[$f] = $w[$f];
			$t[self::uaf . '_U'] = strtotime($w[self::uaf]);
			$t['_id'] = basename($w['html_url']);
			$r[] = $t;
		}

		$this->thea = $r;
    }
    
    private function regGet() {
		$this->rawa = github_api_user_repo_get::get();
    }
}

if (didCLICallMe(__FILE__)) new GitGetAct();
