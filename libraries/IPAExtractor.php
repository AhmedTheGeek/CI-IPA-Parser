<?php

/**
Author: Ahmed Hussein  (AKA Ahmed The Geek)
Website: http://www.ahmedgeek.com/
Github Repo: https://github.com/AhmedTheGeek/CI-IPA-Parser


----------------------------------------------------------+
This library is made for CodeIgniter and tested on v2.3.1 |
And it Requires PHP v5.3+ to operate.                     |
----------------------------------------------------------+


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 * IPAExtractor class.
 */
class IPAExtractor{

	private $ipa_path, $destination, $tmp_dir, $full_app_content_path;
	public $app_version, $ios_version ,$app_name, $devices, $icon_path, $ipa_name;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $file_path
	 * @return void
	 */
	public function __construct($params){
		if(file_exists($params['file_path'])){
			
			//Set IPA Path and Destination
			$this->ipa_path      = $params['file_path'];
			$this->destination   = $params['destination'];
			//Set IPA Name
			$this->ipa_name      = end(explode('/', $this->ipa_path));
			
		}else{
			throw new Exception('Cannot Locate Provided IPA File');
		}
	}
	
	
	/**
	 * init function.
	 * 
	 * @access public
	 * @return void
	 */
	public function init(){
		return $this;
	}
	
	/**
	 * __destruct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct(){
		$this->convertToZip()->readInfoPlist()->readProInfo();
		return $this;
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
		
		return $this;
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
		
		/*
		* Check if the info.plist file do exist in the package
		*/		
		
		if(is_file($this->full_app_content_path . '/Info.plist')){
			
			/*
			* Extract info from the IPA File and convert it into array using CFPropertyList Class
			*/
			$plist = new CFPropertyList\CFPropertyList($this->full_app_content_path . '/Info.plist', CFPropertyList\CFPropertyList::FORMAT_BINARY);
			$plist = $plist->toArray();
			
			/*
			* Update the class attributes with the extracted info
			*/
						
			$this->app_name       = $plist['CFBundleName'];
			$this->icon_path      = $plist['CFBundleIconFiles'];
			$this->app_version    = $plist['CFBundleShortVersionString'];
			$this->ios_version    = $plist['DTPlatformVersion'];
		}
		
		return $this;
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
		
		/*
		* Check if embedded.mobileprovision Exists
		*/
		
		if(is_file($this->full_app_content_path . '/embedded.mobileprovision')){
			
			/*
			* Extract devices ids via mobileprovisionParser script, and output it into an array and update the devices attribue
			*/
			
			$plist = exec(FCPATH . APPPATH . 'third_party/mobileprovisionParser -f ' . FCPATH . $this->full_app_content_path . '/embedded.mobileprovision -o devices', $ids);
			$this->devices = $ids;
	
		}
		
		return $this;
	}

}

?>