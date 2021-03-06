<?php
# Copyright 2018 Menne Kamminga <kamminga DOT m AT gmail DOT com>. All rights reserved.
# Use of this source code is governed by a BSD-style
# license that can be found in the LICENSE file.

defined('__MAINAPP__') or die('nope');

$tdCount = 0;

function addElement($tag,DOMElement $fromElement,$text='',$attr = array()) {
	$newElement = $fromElement->ownerDocument->createElement($tag);
	if($text) {
		$tnode = $fromElement->ownerDocument->createTextNode($text);
		$newElement->appendChild($tnode);
	}
	foreach($attr as $k=>$v) {
		$newElement->setAttribute($k,$v);
	}
	$fromElement->appendChild($newElement);
	return $newElement;
}

function createTable($node,$id,$attributes,$data,$donewrow,$membershipfilterforattr) {
	global $tdCount;

	/*//global $lang;*/
	$table = addElement('table',$node,'',array('id'=>$id,'class'=>'sorted'));
	$thead = addElement('thead',$table);
	$tbody = addElement('tbody',$table);

	$trhead = addElement('tr',$thead);
	foreach($attributes as $att=>$att_tr) {
		addElement('th',$trhead,getLang($att_tr),array('name'=>$att));
	}

	if($donewrow) {
		$tr = addElement('tr',$thead,'',array('id'=>$id."_newrow",'class'=>'newrow','__dn'=>$id.'_new'));
		$td = null;
		$tt = 0;
		foreach($attributes as $att=>$att_tr) {
			$td = addElement('td',$tr,'',array('id'=>$id.'_'.$att."_new"));
			$func = "displayfilter_".$att;
			if(in_array($att,$membershipfilterforattr)) {
				$func= "displayfilter_membership";
			} else {
				$ediv = addElement('div',$td,"",array('class'=>'cellerror'));	
								addElement('span',$ediv,"",array('class'=>'cellerrortext'));
			}
					
			if(function_exists($func)) {
				$func($id.'_'."new","",$td,$att,false,$attributes);
			} else {
				displayfilter_default($id.'_'."new","",$td,$att,false,$attributes);
			}
			++$tt;
		}
	}
	
	foreach($data as $dn=>$usr) {
		$tr = addElement('tr',$tbody,'',array('__dn'=>$dn));
		foreach($attributes as $att=>$att_tr) {
			$td = addElement('td',$tr,'',array('id'=>'td_'.$tdCount));
			++$tdCount;
			$func = "displayfilter_".$att;
			if(in_array($att,$membershipfilterforattr)) {
				$func= "displayfilter_membership";
			} else {
				$ediv = addElement('div',$td,"",array('class'=>'cellerror'));	
								addElement('span',$ediv,"",array('class'=>'cellerrortext'));	
			}
			
			if(function_exists($func)) {
				$func($dn,$usr[$att],$td,$att,true,$attributes);
			} else {
				displayfilter_default($dn,$usr[$att],$td,$att,true,$attributes);
			}
			
		}
		
	}
	return $table;
}

function removeNode(&$node) {
    $pnode = $node->parentNode;
    removeChildren($node);
	if($pnode) {
		$pnode->removeChild($node);
	}
}

function removeChildren(&$node) {
    while ($node->firstChild) {
        while ($node->firstChild->firstChild) {
            removeChildren($node->firstChild);
        }

        $node->removeChild($node->firstChild);
    }
}

function replaceDocumentMarkers($document,$additionalMarkers = array()) {
	global $config;
	$cont = $document->getElementsByTagName('body')[0];
	
	$removeElements = array();
	
	$elements = $document->getElementsByTagName("enable_if");
	foreach ($elements as $replaceme) {
		$parent = $replaceme->parentNode;
		//$parent->removeChild($replaceme);
		$i = $replaceme->getAttribute('config');
		if(isset($config[$i]) && $config[$i]) {
			foreach($replaceme->childNodes as $child) {
				$parent->insertBefore($child->cloneNode(true),$replaceme);
			}
			$removeElements[] = $replaceme;
		} else {
			$removeElements[] = $replaceme;
		}
	}

	$elements = $document->getElementsByTagName("disable_if");
	foreach ($elements as $replaceme) {
		$parent = $replaceme->parentNode;
		//$parent->removeChild($replaceme);
		$i = $replaceme->getAttribute('config');
		if(isset($config[$i]) && $config[$i]) {
			$removeElements[] = $replaceme;
		} else {
			foreach($replaceme->childNodes as $child) {
				$parent->insertBefore($child->cloneNode(true),$replaceme);
			}
			$removeElements[] = $replaceme;
		}
	}

	foreach($removeElements as $e) { removeNode($e); }
	
	
	$elements = $document->getElementsByTagName("meta");
	foreach ($elements as $replaceme) {
		if(!$replaceme->hasAttribute('replaceme')) {
			continue;
		}
		$parent = $replaceme->parentNode;
		$i = $replaceme->getAttribute('replaceme');
		
		//print_r($i);
		switch($i) {
			case 'headerscripts':
				addElement('link',$parent,'',array('rel'=>'stylesheet','type'=>'text/css','href'=>'?resource=styles.css'));
				addElement('script',$parent,'',array('src'=>'?resource=main.js'));

				
				break;
			case 'headertitle':
				addElement('title',$parent,$config['page_title']);		
				break;
			case 'title':
				addElement('span',$parent,$config['page_title'],array('class'=>'subtitle'));
				break;
			case 'usernameinput':
				addElement('input',$parent,'',array(
					'name'=>'username',
					'type'=>'text',
					'value'=>'',
				));
				break;
			case 'passwordinput':
				addElement('input',$parent,'',array(
					'name'=>'password',
					'type'=>'password',
					'value'=>'',
				));
				break;
			case 'tokeninput':
				addElement('input',$parent,'',array(
					'name'=>'token',
					'type'=>'password',
					'value'=>'',
				));			
				break;
			case 'loginbutton':
				addElement('input',$parent,'',array(
					'type'=>'submit',
					'value'=>getLang('btn:login'),
					'class'=>'btn btn-primary',
				));		
				break;
			case 'version':
				addElement('span',$parent,$config['version']);
				break;
			case 'usersbutton':
				addElement('a',$parent,getLang('btn:users'),array('class'=>'sorted_bt','name'=>'users'));
				break;
			case 'groupsbutton':
				addElement('a',$parent,getLang('btn:groups'),array('class'=>'sorted_bt','name'=>'groups'));
				break;
			case 'rightsbutton':
				addElement('a',$parent,getLang('btn:rights'),array('class'=>'sorted_bt','name'=>'rights'));
				break;
			case 'refreshbutton':
				$a = addElement('a',$parent,'',array('id'=>'refresh_bt'));
				addElement('span',$a,getLang('btn:refresh'));
				break;
			case 'auditbutton':
				addElement('a',$parent,getLang('btn:audit'),array('class'=>'sorted_bt','name'=>'audit'));
				break;
			case 'auditinput':
				addElement('input',$parent,'',array('type'=>'text','id'=>'audit_date','value'=>date('Ym')));
				break;
			case 'searchinput':
				addElement('input',$parent,'',array('type'=>'text','id'=>'search','placeholder'=>getLang('btn:search').'...'));
				break;
			case 'error':
				addElement('div',$parent,isset($_SESSION['error'])?$_SESSION['error']:'',array('id'=>'errordiv'));
				break;
			case 'logo':
				addElement('img',$parent,'',array('id'=>'logo_img','src'=>$config['logo']));
				break;
			default:
				if(isset($additionalMarkers[$i])) {
					$parent->insertBefore($additionalMarkers[$i],$replaceme);
				} else {
					
					$tnode = $replaceme->ownerDocument->createTextNode(getLang($i));
					$parent->insertBefore($tnode,$replaceme);
				}
				break;
		}
		$removeElements[] = $replaceme;
	}
	
	foreach($removeElements as $e) { removeNode($e); }
	
	if($config['debug'] && isset($_SESSION['debug'])) {
		foreach($_SESSION['debug'] as $msg) {
			addElement('div',$cont,$msg,array('class'=>'debug'));
		}
	}
	
	
}

function loadDocumentTemplate($document,$template) {
	if(!$document->loadHTMl(file_get_contents($template),LIBXML_NOWARNING|LIBXML_NOERROR))  {
    // handle errors here
		//print_r(libxml_get_errors());

		die("failed to load template: ".$template);
	}
}
