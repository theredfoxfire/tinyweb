<?php

if(!defined('PCAFW')){
	echo"This file can only be called vie the main index.php, and not directly";
	exit();
}

class template{
	private $page;
	
	public function __construct(){
		include (APP_PATH.'/PCARegistry/objects/page.class.php');
		$this->page = new Page();
	}
	
	public function addTemplateBit(){
		if (strpos($bit,'skins/') === false){
			$bit = 'skins/'.PCARegistry::getSetting('skin').'/templates/'.$bit;
		}
		this->page->addTemplateBit($tag, $bit);
	}
	
	private function replaceBits(){
		$bits = $this->page->getBits();
		foreach($bits as $tag => $template){
			$templateContent = file_get_contents($bit);
			$newConnection = str_replace('{'.$tag.'}', $templateContent, $this->page->getContent());
			$this->page->setContent($newContent);
		}
	}
	
	private function replaceTags(){
		$tags = $this->page->getTags();
		
		foreach($tags as $tag => $data){
			if (is_array($data)){
				if($data[0] == 'SQL'){
					$this->replaceTags($tags, $data[1]);
				}elseif($data[0] == 'DATA'){
					$this->replaceDataTags($tag, $data[1]);
				}
			} else{
				$newContent = str_replace('{'.$tag.'}', $data, $this->page->getContent());
				
				$this->page->setContent($newContent);
			}
		}
	}
	
	private function replaceDBTags($tag, $cacheId){
		$block = '';
		$blockOld = $this->page->getBlock($tag);
		
		while($tags = PCARegistry::getObject('db')->resultsFromCache($cacheId)){
			$blockNew = $blockOld;
			foreach($tags as $ntag => $data){
				$blockNew = str_replace("{".$ntag."}", $data, $blockNew);
			}
			$block .= $blockNew;
		}
		$pageContent = $this->page->getContent();
		$newContent = str_replace('<!-- START'.$tag.'-->'. $blockOld.'<!--END'.$tag.'-->', $block, $pageContent);
		
		$this->page->setContent($newContent);
	}
	
	private function replaceDataTags($tag, $cacheId){
		$block = $this->page->getBlock($tag);
		$blockOld = $block;
		while ($tags = PCARegistry::getObject('db')->dataFromCache($cacheId)){
			foreach($tags as $tag => $data){
				$blockNew = $blockOld;
				$blockNew = str_replace("{".$tag."}", $data, $blockNew);
			}
			$block .= $blockNew;
		}
		$pageContent = $this->page->getContent();
		$newContent = str_replace($blockOld, $block, $pageContent);
		$this->page->setContent($newContent);
	}
	
	public function getPage(){
		return $this->page;
	}
	
	public function buildFromTemplates(){
		$bits = func_get_args();
		$content = "";
		foreach($bits as $bit){
			if(strpos($bit, 'skins/') === false){
				$bit = 'skins/'.PCARegistry::getSetting('skin'). '/template/'.$bit;
			}
			if(file_exist($bit) == true){
				$content .= file_get_contents($bit);
			}
		}
		$this->page->setContent($content);
	}
	
	public function dataToTags($data, $prefix){
		foreach($data as $key => $content){
			$this->page->addTag($key.$prefix, $content);
		}
	}
	
	public function parseTitle(){
		$newContent = str_replace('<title>', '<title>'.$page->getTitle(), $this->page->getContent());
		$this->page->setContent($newContent);
	}
	
	public function parseOuput(){
		$this->replaceBits();
		$this->replaceTags();
		$this->parseTitle();
	}
}
