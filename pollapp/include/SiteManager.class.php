<?php
error_reporting(0);
class SiteManager
{
	public $lang="en";
	public $page="home";
	public $salt = "PS9DI26";
	public $data_file = "";
	public $data_path = "";
	public $data_folder = "data";
	public $arrPages = array();
	public $domain = "";
	public $multi_language = false;
	public $is_admin = false;

	
	public $default_page_name="en_Home";
	
	
	function __construct()
	{
		
	}
	
	function InitData()
	{
		$this->data_file = $this->data_path."data/polls_".md5($this->salt).".xml";
		
		if(!file_exists($this->data_file))
		{
			if(!copy($this->data_path."data/polls.xml",$this->data_file))
			{
				die($this->data_file."-System Error: The /data folder isn't writable, please make sure it exists and there are write permissions for it.");
			}
		}	
	}
	
	/// The website title and meta description and keywords,
	/// which can be used for SEO purposes
	public $Title = true;
	public $Description = true;
	public $Keywords = true;
	
	/// The current language version on the website
	public $Language = true;
	
	/// The html code of the website template
	public $TemplateHTML = "";
	
	/// The site paramets
	public $settings = array();
	
	/// Texts and words shown on the website
	public $texts = array();
	
	
	function SetLanguage($lang)
	{
		$this->lang= substr(preg_replace("/[^a-z]/i", "", $lang), 0, 2); 
	}
	
	function SetDataFile($data_file)
	{
		$this->data_file= $data_file;
	}
		
	function SetDatabase(Database $db)
	{
		$this->db = $db;
	
	}
	
	function SetPage($page)
	{
	
		$this->page=$page;
	}
	
	function LoadSettings()
	{
		if(file_exists("config.php"))
		{
			$this->settings = parse_ini_file("config.php",true);
		}
		else
		if(file_exists("../config.php"))
		{
			$this->settings = parse_ini_file("../config.php",true);
		}
		else
		{
			die("The configuration file doesn't exist!");
		}
		
		if(file_exists("include/texts_".$this->lang.".php"))
		{
			$this->texts = parse_ini_file("include/texts_".$this->lang.".php",true);
		}
		else
		if(file_exists("../include/texts_".$this->lang.".php"))
		{
			$this->texts = parse_ini_file("../include/texts_".$this->lang.".php",true);
		}
		else
		{
			die("The language file include/texts_".$this->lang.".php doesn't exist!");
		}
		
		
		
	}
	
	function LoadTemplate()
	{
		global $_REQUEST,$DBprefix;
		
		if(file_exists("template.htm"))
		{
			$templateArray=array();
			
			$templateArray["html"] = file_get_contents('template.htm');
		
		}
		
		else
		{
			die("Error: The template file template.htm doesn't exist.");
		}
		
		$this->TemplateHTML = stripslashes($templateArray["html"]);
		
		if(!$this->is_admin)
		{
			$custom_css="";
			if($this->settings["website"]["accent_color"]!="" && $this->settings["website"]["accent_color"]!="Default")
			{
				$custom_css.='
					.block-wrap {border-left-color: '.$this->settings["website"]["accent_color"].' !important}
					.block-wrap h3, .custom-color, a {color: '.$this->settings["website"]["accent_color"].' !important}
					.custom-back-color{background-color:  '.$this->settings["website"]["accent_color"].' !important;color: #ffffff !important}';
			}
			
			if($this->settings["website"]["survey_font"]!="" && $this->settings["website"]["survey_font"]!="Default")
			{
				$custom_css.='
					* {font-family:'.$this->settings["website"]["survey_font"].'}';
			}
			
			if($this->settings["website"]["survey_font_size"]!="" && $this->settings["website"]["survey_font_size"]!="Default")
			{
				$custom_css.='
					p,h3,h4, .btn, .block-wrap, .survey-question, .survey-field {font-size:'.$this->settings["website"]["survey_font_size"].'px !important}';
			}
			
			
			$this->TemplateHTML = 
			str_replace
			(
				'</head>',
				'</head>
				<style>
				'.$custom_css.'</style>',
				$this->TemplateHTML
			);
				
			
		}
		
		
		$pattern = "/{(\w+)}/i";
		preg_match_all($pattern, $this->TemplateHTML, $items_found);
		foreach($items_found[1] as $item_found)
		{
			
			if(isset($this->texts[$item_found]))
			{
				$this->TemplateHTML=str_replace("{".$item_found."}",$this->texts[$item_found],$this->TemplateHTML);
			}
		}
		
		
		$arrTags=array();
		
		array_push($arrTags, array("top_right_menu","top_right_menu.php"));
		array_push($arrTags, array("search_form","search_form.php"));
		array_push($arrTags, array("admin_menu","admin_menu.php"));
		
		if(is_array($arrTags))
		{
			foreach($arrTags as $arrTag)
			{
				$tag_pos = strpos($this->TemplateHTML,"<site ".$arrTag[0]."/>");
			
				if($tag_pos !== false)
				{
					if(trim($arrTag[1]) != "none" && trim($arrTag[0]) != "" && trim($arrTag[1]) != "")
					{
						$HTML="";
						ob_start();
						include("include/".$arrTag[1]);
						
						if($HTML=="")
						{
							$HTML = ob_get_contents();
						}
						ob_end_clean();
						$this->TemplateHTML = str_replace("<site ".$arrTag[0]."/>",$HTML,$this->TemplateHTML);
					}
				}
			}
		}

	}
	
	function Render()
	{
		
		if($this->page!="")
		{
			$HTML="";
			ob_start();
			
			if(file_exists("pages/".$this->page.".php"))
			{
				include("pages/".$this->page.".php");
			
			}
			$HTML = ob_get_contents();
			
			$this->TemplateHTML=str_replace("<site content/>",$HTML,$this->TemplateHTML);
			
			ob_end_clean();
		}
		
		echo $this->TemplateHTML;
	}

	
	function check_word($input)
	{
		if(!preg_match("/^[a-zA-Z0-9_]+$/i", $input)) die("");
	}
	
	function check_extended_word($input)
	{
		if(!preg_match("/^[a-zA-Z0-9_\-. @]+$/i", $input)) die("");
	} 
	
	function check_integer($input)
	{
		if(!is_numeric($input)) die("");
	} 
	
	function ms_ia($input)
	{
		foreach($input as $inp) if(!is_numeric($inp)) die("");
	}
	
	function ms_i($input)
	{
		if(!is_numeric($input)) die("");
	} 
	
	function check_id($input)
	{
		if(!preg_match('/[A-Za-z0-9]/', $input))
		{
		  die("");
		}
	} 
	
	function ForceLogin()
	{
		die("<script>document.location.href='login.php';</script>");
	}
	
	
	function sanitize($input)
	{
		$strip_chars = array("~", "`", "!","#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]",
                 "}", "\\", "|", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                  "<", ">", "/", "?");
		$output = trim(str_replace($strip_chars, " ", strip_tags($input)));
		$output = preg_replace('/\s+/', ' ',$output);
		$output = preg_replace('/\-+/', '-',$output);
		return $output;
	}
	
	function filter_data($data)
	{
		return stripslashes(strip_tags($data));
	}
	
	
	function str_rot($s, $n = 13) {
    static $letters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
    $n = (int)$n % 26;
    if (!$n) return $s;
    if ($n < 0) $n += 26;
    if ($n == 13) return str_rot13($s);
    $rep = substr($letters, $n * 2) . substr($letters, 0, $n * 2);
    return strtr($s, $letters, $rep);
}

	

	function write_ini_file($file, array $options)
	{
		$tmp = '; <?php exit;?>';
		$tmp.="\n\n";
		foreach($options as $section => $values){
			$tmp .= "[$section]\n";
			foreach($values as $key => $val){
				if(is_array($val)){
					foreach($val as $k =>$v){
						$tmp .= "{$key}[$k] = \"$v\"\n";
					}
				}
				else
					$tmp .= "$key = \"$val\"\n";
			}
			$tmp .= "\n";
		}
		file_put_contents($file, $tmp);
		unset($tmp);
	}

	
	function load_login_slides($product="")
	{
		
		$url = "http://www.netartmedia.net/get_slides.php?p=adlister";		
						
		 $opts = array('http' =>
		  array(

			'timeout' => 3
		  )
		);
							   
		$context  = stream_context_create($opts);
		  
		  $data = file_get_contents($url, false, $context);
		  if(!$data)
		  {
			return false;
		  }
		  return simplexml_load_string($data);
	}
	
	
	function parse_csv($file, $delimiter=',') 
	{
		$field_names=array();
		$res=array();
		
		if (($handle = fopen($file, "r")) !== FALSE) 
		{ 
			$i = 0; 
			while (($lineArray = fgetcsv($handle, 4000, $delimiter)) !== FALSE) 
			{ 
				
				if($i==0)
				{
					for ($j=0; $j<count($lineArray); $j++) 
					{ 
						$field_names[$j] = $lineArray[$j]; 
					}
				}
				else
				{
					for ($j=0; $j<count($lineArray); $j++) 
					{ 
						if(isset($field_names[$j]))
						{
							$data2DArray[$i-1][$field_names[$j]] = $lineArray[$j]; 
						}
					}
				}				
				$i++; 
			} 
			fclose($handle); 
		} 
			
		
		return $data2DArray; 
		
	} 
	
	function format_str($strTitle)
	{
		$strSEPage = ""; 
		$strTitle=strtolower(trim($strTitle));
		$arrSigns = array("~", "!","\t", "@","1","2","3","4","5","6","7","8","9","0", "#", "$", "%", "^", "&", "*", "(", ")", "+", "-", ",",".","/", "?", ":","<",">","[","]","{","}","|"); 
		
		$strTitle = str_replace($arrSigns, "", $strTitle); 
		
		$pattern = '/[^\w ]+/';
		$replacement = '';
		$strTitle = preg_replace($pattern, $replacement, $strTitle);

		$arrWords = explode(" ",$strTitle);
		$iWCounter = 1; 
		
		foreach($arrWords as $strWord) 
		{ 
			if($strWord == "") { continue; }  
			
			if($iWCounter == 4) { break; }  
			if($iWCounter != 1) { $strSEPage .= "-"; }
			$strSEPage .= $strWord;  
			
			$iWCounter++; 
		} 
		
		return $strSEPage;
		
	}
	
	function text_words($string, $wordsreturned)
	{
		$string=trim($string);
		$string=str_replace("\n","",$string);
		$string=str_replace("\t"," ",$string);
		
		$string=str_replace("\r","",$string);
		$string=str_replace("  "," ",$string);
		 $retval = $string;    
		$array = explode(" ", $string);
	  
		if (count($array)<=$wordsreturned)
		{
			$retval = $string;
		}
		else
		{
			array_splice($array, $wordsreturned);
			$retval = implode(" ", $array)." ...";
		}
		return $retval;
	}
	
	function Title($website_title)
	{
		$this->TemplateHTML = 
		str_replace
		(
			"<site title/>",
			strip_tags(stripslashes($website_title)),
			$this->TemplateHTML
		);
	}
	
	function MetaDescription($meta_description)
	{
		$this->TemplateHTML = 
		str_replace
		(
			"<site description/>",
			strip_tags(stripslashes($meta_description)),
			$this->TemplateHTML
		);
	}
	
	function SetAdminHeader($header_text)
	{
		$this->Title($header_text);

		echo '<script>document.getElementById("page_header").innerHTML="'.$header_text.'";</script>';
	}
	
	function survey_link($survey_id,$survey_name="")
	{
		if($this->settings["website"]["seo_urls"]=="0")
		{
			return "index.php?page=poll&id=".$survey_id;
		}
		else
		{
			return "poll-".$this->format_str($survey_name)."-".$survey_id.".html";
		}
	}
	
	
	
	function create_chart
	(
		$arr_names,
		$arr_values
	)
	{
		$max_value=max($arr_values);
		
		
		echo ' <table  cellspacing="0" cellpadding="0" width="500">';
		
		for($i=0;$i<sizeof($arr_names);$i++)
		{
			$p_value=round((floatval($arr_values[$i])/floatval($max_value))*80);
			
			echo '<tr>
				<td style="border-color:#f3f3f3;padding:4px"  width="150">'.$arr_names[$i].'</td>
				<td style="border-color:#f3f3f3;padding:4px"><img src="images/bar.png" alt="" width="'.$p_value.'%" height="16" /> '.$arr_values[$i].'</td>
			</tr>';
		}
     
 
		echo '</table>';
		
	}
	
	function show_poll_chart($poll_id)
	{
		$answers_file="data/".$poll_id."/".md5($poll_id.$this->salt)."_answers.xml";
		
		if(file_exists($answers_file))
		{
			$answers = simplexml_load_file($answers_file);
		}
		else
		{
			
			$answers = simplexml_load_file("../".$answers_file);
		}
		
		$arr_names=array();
		$arr_values=array();
		
		foreach ($answers->answer as $answer)
		{
		
			array_push($arr_names,"".$answer->option);
			array_push($arr_values,"".$answer->value);
		}
			
		$this->create_chart($arr_names,$arr_values);
	}
						
	function get_votes_number($poll_id)
	{
		$answers_file="data/".$poll_id."/".md5($poll_id.$this->salt)."_answers.xml";
		
		if(file_exists($answers_file))
		{
			$answers = simplexml_load_file($answers_file);
		}
		else
		if(file_exists("../".$answers_file))
		{
			$answers = simplexml_load_file("../".$answers_file);
		}
		else
		{
			return 0;
		}
		
		$total_votes=0;
		
		foreach ($answers->answer as $answer)
		{
			$total_votes+=$answer->value;
		}
			
		return $total_votes;
	}
	
	
	function count_all($path) 
	{ 
		
		$total_count = 0;
		$dir = opendir($path);

		if (!$dir) return -1;
		while ($file = readdir($dir))
		{
			
			if ($file == '.' || $file == '..') continue;
											
			if(is_dir($path  . "/". $file))
			{
				$total_count += $this->get_votes_number($file);
			}
					
		}

		closedir($dir);
		return $total_count;
	}
	
	
	
}	
?>