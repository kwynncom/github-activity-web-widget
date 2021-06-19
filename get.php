<?php

require_once('/opt/kwynn/kwutils.php');
require_once('getActual.php');

class GitGetAct extends dao_generic {
    
    const db = 'repos';
    const quotaMinutes = 15;
    
    public function __construct()  {
	parent::__construct(self::db);
	$this->lcoll = $this->client->selectCollection(self::db, 'latest');
	$this->scoll = $this->client->selectCollection(self::db, 'snapshot');
	
	$this->regGet();
	$this->p10();
	$this->p20();
	$this->save();
	return;
    }

    private function save() {
	if (isset($this->suma)) return;
	$q = ['latest' => true];
	$a = $this->thea[0];
	$d = [];
	$d['cnt'] = count($this->thea);
	$d['asof'] = $this->asof;
	$d['asr' ] = date('r', $d['asof']);
	$d['source'] = $this->source;
	$la = array_merge($a, $q, $d);
	$this->lcoll->upsert($q, $la);
	$sa = array_merge($d, ['rs' => $this->thea]);
	$this->scoll->insertOne($sa);
	$this->suma = $d;
    }
    
    private function sort($a, $b) { return $b['ts'] - $a['ts'];   }
    
    private function p20() { 
	if (!isset($this->thea)) return;
	usort($this->thea, [$this, 'sort']);    
    }
       
    private function p10() {
	
	if (isset($this->suma)) return;
	
	$fs = ['updated_at', 'html_url', 'description', 'id', 'node_id', 'created_at' ];
	
	$r = [];
	foreach($this->rawa as $w) {
	    $t = [];
	    foreach($fs as $f) $t[$f] = $w[$f];
	    $t['ts'] = strtotime($w['updated_at']);
	    $r[] = $t;
	}
	
	$this->thea = $r; unset($this->htj);
    }
    
    private function regGet() {
	$since = time() - self::quotaMinutes * 60;
	$r = $this->lcoll->findOne(['asof' => ['$gte' => $since]]);
	if ($r) { 
	    $this->suma = $r; 	
	    $this->source = 'dbcache';
	    return; 
	}
	
	$this->source = 'curl';	
	$this->rawa = github_api_user_repo_get::get();
	$this->asof = time();
    }
}

if (didCLICallMe(__FILE__)) new GitGetAct();
