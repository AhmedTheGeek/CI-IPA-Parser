<?php

class IPAExtractor{
	
	public $ipa_path, $app_name, $udids, $icon_path, $destination, $ipa_name, $tmp_dir, $full_app_content_path;
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $file_path
	 * @return void
	 */
	public function __construct($params){
		if(file_exists($params['file_path'])){
			$this->ipa_path = $params['file_path'];
			$this->destination = $params['destination'];
			//Set IPA Name
			$this->ipa_name = end(explode('/', $this->ipa_path));
		}else{
			throw new Exception('Cannot Locate Provided IPA File');
		}
	}
	
	/**
	 * convertToZip function.
	 * 
	 * @access public
	 * @return void
	 */
	public function convertToZip(){
		if($this->ipa_path != null){
			$zip = new ZipArchive();
			if(is_dir($this->destination)){
				$rename = rename($this->ipa_path, $this->ipa_path.'.zip');
				if($rename){
					$this->tmp_dir = time();
					//Creat tmp folder
					mkdir($this->destination . $this->tmp_dir);
					$zip = new ZipArchive();
					$zip->open($this->ipa_path.'.zip');
					$zip->extractTo(APPPATH . 'third_party/apps/'. $this->tmp_dir);
					//Extract .app file path
					$extracted_dir = opendir(APPPATH . 'third_party/apps/'. $this->tmp_dir . '/Payload');
					while($file = readdir($extracted_dir)){
						$this->full_app_content_path = APPPATH . 'third_party/apps/'. $this->tmp_dir . '/Payload/' . $file;
					}
					return $this;
				}else{
					throw new Exception('Not Allowed To Convert IPA to ZIP File');
				}
			}else{
				throw new Exception('Cannot Locate Provided Destination Directory');
			}
		}
	}
	
	
	/**
	 * readProperties function.
	 * 
	 * @access public
	 * @return void
	 */
	public function readInfoPlist(){
		/**
		 * Require CFPropertyList
		 */
		require_once(__DIR__.'/CFPropertyList.php');
		$plist = new CFPropertyList\CFPropertyList($this->full_app_content_path . '/Info.plist', CFPropertyList\CFPropertyList::FORMAT_BINARY );
		
		$this->app_name = $plist->CFBundleName;
		$this->icon_path = $plist->CFBundleIconFiles;
	}
	
	
	/**
	 * readProInfo function.
	 * 
	 * @access public
	 * @return void
	 */
	public function readProInfo(){
		/**
		 * Require CFPropertyList
		 */
		require_once(__DIR__.'/CFPropertyList.php');
		$plist = system(FCPATH . APPPATH . 'third_party/mobileprovisionParser -f ' . FCPATH . $this->full_app_content_path . '/embedded.mobileprovision -o devices');
		echo "<pre>";
		var_dump($plist);
		echo "</pre>";
	}
			
}
	
?>