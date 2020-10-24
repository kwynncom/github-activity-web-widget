<?php

require_once('/opt/kwynn/kwutils.php');

class GitGetAct extends dao_generic {
    
    const db = 'repos';
    const quotaMinutes = 20;
    
    
    public function __construct()  {
	parent::__construct(self::db);
	$this->lcoll = $this->client->selectCollection(self::db, 'latest');
	
	$this->get10();
	$this->p10();
	$this->p20();
	$this->save();
	return;
    }

    private function save() {
	if (isset($this->suma)) return;
	$q = ['latest' => true];
	$a = $this->thea[0];
	$d = array_merge($a, $q);
	$d['cnt'] = count($this->thea);
	$d['asof'] = $this->asof;
	$d['asr' ] = date('r', $d['asof']);
	$this->lcoll->upsert($q, $d);
	$this->suma = $d;
    }
    
    private function sort($a, $b) { return $b['ts'] - $a['ts'];   }
    
    private function p20() { 
	if (!isset($this->thea)) return;
	usort($this->thea, [$this, 'sort']);    
	
    }
       
    private function p10() {
	
	if (isset($this->suma)) return;
	
	$fs = ['updated_at', 'html_url', 'description' ];
	
	$r = [];
	
	$j = $this->htj;
	$ws = json_decode($j, 1);
	foreach($ws as $w) {
	    $t = [];
	    foreach($fs as $f) $t[$f] = $w[$f];
	    $t['ts'] = strtotime($w['updated_at']);
	    $r[] = $t;
	}
	
	$this->thea = $r; unset($this->htj);
    }
    
    private function get10() {
	
	$testf = '/tmp/git';
	
	if (isAWS() || 1 || !file_exists($testf)) {
	    $j = $this->regGet();
	    file_put_contents($testf, $j);
	} else {
	    $j = file_get_contents($testf);
	    $this->asof = filemtime($testf);
	    $this->source = 'temp_file';
	}
	
	$this->htj = $j;
    }
    
    private function regGet() {
	$since = time() - self::quotaMinutes * 60;
	$r = $this->lcoll->findOne(['asof' => ['$gte' => $since]]);
	if ($r) $this->suma = $r;
	return;
    }
    
    private function getActual() {
	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/users/kwynncom/repos');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$h = ['User-Agent: curl/PHP'];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
	$j = curl_exec($ch);
	$this->asof = time();
	$this->source = 'curl';
	return $j;	
    }
}

if (didCLICallMe(__FILE__)) new GitGetAct();
