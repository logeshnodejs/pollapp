<?php
if(!defined('IN_SCRIPT')) die("");

$id=$_REQUEST["id"];

$this->check_id($id);

$show_survey_form=true;

$xml = simplexml_load_file($this->data_file);

$nodes = $xml->xpath('//polls/poll/id[.="'.$id.'"]/parent::*');
$survey = $nodes[0];

?>

<div class="block-wrap">
	

	<?php
	if(isset($_REQUEST["proceed_submit"]))
	{
		$survey_result_file="data/".$survey->id."/".md5($survey->id.$this->salt)."_details.xml";
		if(!file_exists($survey_result_file))
		{
			file_put_contents($survey_result_file, "<results></results>");
		}
			
		$survey_results = simplexml_load_file($survey_result_file);
		
		$already_voted=false;
		if($this->settings["website"]["only_one_per_ip"]=="1")
		{
			$user_vote = $xml->xpath('//results/result/ip[.="'.$_SERVER["REMOTE_ADDR"].'"]/parent::*');
			
			if(isset($user_vote[0]))
			{
				?>
				<h4 class="custom-color"><?php echo $this->texts["already_voted"];?></h4>
				<br/>
				<?php
				$show_survey_form=false;
			}
		}
	
		
			
			if(!file_exists("data/".$survey->id))
			{
				if(!mkdir("data/".$survey->id))
				{
				
				}
			}
			
			
			
			if(!isset($survey_results))
			{
				$survey_results = simplexml_load_file($survey_result_file);
			}
			$survey_result = $survey_results->addChild('result');
			$survey_result->addChild('name', (isset($_POST["name"])?$this->filter_data($_POST["name"]):"") );
			$survey_result->addChild('email', (isset($_POST["email"])?$this->filter_data($_POST["email"]):"")  );
			$survey_result->addChild('phone', (isset($_POST["phone"])?$this->filter_data($_POST["phone"]):"") );
			$survey_result->addChild('ip', $_SERVER["REMOTE_ADDR"]);
			$survey_result->addChild('vote_date', time());
			
			$survey_result->addChild('data', $this->filter_data(stripslashes(trim($_POST["answer"]))));
			

			$survey_results->asXML($survey_result_file); 
			
			
			$answers_file="data/".$survey->id."/".md5($survey->id.$this->salt)."_answers.xml";
			if(!file_exists($answers_file))
			{
				file_put_contents($answers_file, "<answers></answers>");
			}
			
			$answers_xml = simplexml_load_file($answers_file);

		
			$answers = $answers_xml->xpath('//answers/answer/option[.="'.stripslashes(trim($_POST["answer"])).'"]/parent::*');
			
			if(isset($answers[0]))
			{
				$answers[0]->value=intval($answers[0]->value)+1;
			}
			else
			{
				$new_answer=$answers_xml->addChild('answer');
				$new_answer->addChild('option', $this->filter_data(stripslashes(trim($_POST["answer"]))));
				$new_answer->addChild('value', "1");
			}
			
			$answers_xml->asXml($answers_file);
			
			?>
			<br/>
			<h4 class="custom-color"><?php echo $this->texts["survey_thank_you"];?></h4>
			<br/>
			
			<?php
			if($this->settings["website"]["show_results"]=="1")
			{
				echo "<i>".$this->texts["poll_results"]."</i><hr style=\"margin-top:10px;margin-bottom:10px\"/>";
				$this->show_poll_chart($survey->id);
			}
			
			if($this->settings["website"]["send_notifications"]=="1")
			{
				/*
				mail
				(
					$this->settings["website"]["admin_email"],
					$this->texts["new_completed_survey"]." - ".$survey->name,
					$survey_email
				);
				*/
			}
			
			$show_survey_form=false;
		
	}
	else
	{
	?>
		<h4 class="custom-color"><strong><?php echo $survey->name;?></strong></h4>
		<i><?php echo $survey->description;?></i>
	<?php
	}
	

	if($show_survey_form)
	{
	?>
		<form action="index.php" method="post"   enctype="multipart/form-data">
		<input type="hidden" name="page" value="poll"/>
		<input type="hidden" name="proceed_submit" value="1"/>
		<input type="hidden" name="id" value="<?php echo $id;?>"/>
		
	
		
		<div class="clearfix"></div>
		
		
		<input type="hidden" name="survey_questions" id="survey_questions" value="<?php  echo $survey->questions;?>"/>	
		
	
		<div class="survey-question custom-color"><?php echo $survey->question;?></div>
		
		<div class="answers-wrap">
			<?php
			$possible_answers=explode("\n",$survey->possible_answers);
			foreach($possible_answers as $value)
			{
				echo '<input type="radio" required value="'.$value.'" name="answer" '.(isset($_POST["answer"])&&trim($_POST["answer"])==trim($value)?"checked":"").' class=""/> '.$value.'<br/>';
			}
			?>
		</div>
		<div class="clearfix"></div>		

				
		<button type="submit" class="btn btn-md custom-back-color"><?php echo $this->texts["submit"];?></button>
	
		<div class="clearfix"></div>
	
		</form>
		
<?php
}
?>
		
</div>
<?php
$this->Title($survey->name);
$this->MetaDescription($survey->description);
?>