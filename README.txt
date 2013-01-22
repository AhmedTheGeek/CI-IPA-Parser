Author: Ahmed Hussein  (AKA Ahmed The Geek)
Website: http://www.ahmedgeek.com/
Github Repo: https://github.com/AhmedTheGeek/CI-IPA-Parser

----------------------------------------------------------+
This library is made for CodeIgniter and tested on v2.3.1 |
And it Requires PHP v5.3+ to operate.                     |
----------------------------------------------------------+

Mobile Provision Parser by sharpland: https://github.com/sharpland/mobileprovisionParser
CFPropertyList by rodneyrehm: https://github.com/rodneyrehm/CFPropertyList


Sample Usage:

$this->load->library('IPAExtractor', array('file_path' => 'path/to/file.ipa', 'destination' => 'path/to/output/folder'));

$IPAInfo = $this->ipaextractor->init();

Requirements
1- PHP 5.3

Tested to Work on Mac System.