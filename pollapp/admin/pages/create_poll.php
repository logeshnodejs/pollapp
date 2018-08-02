<?php
if(!defined('IN_SCRIPT')) die("");
?>
 <div class="col-md-12">
<?php
$show_add_form=true;

$this->SetAdminHeader($this->texts["new_survey"]);

if(isset($_REQUEST["proceed_save"]))
{
	
	$listings = simplexml_load_file($this->data_file);

	$listing = $listings->addChild('poll');
	
	$arrChars = array("A","F","B","C","O","Q","W","E","R","T","Z","X","C","V","N");
							
	$random_code = $arrChars[rand(0,(sizeof($arrChars)-1))]."".rand(1000,9999)
	.$arrChars[rand(0,(sizeof($arrChars)-1))].rand(1000,9999);

	$listing->addChild('id', $random_code);
	$listing->addChild('name', stripslashes($_POST["poll_name"]));
	$listing->addChild('description', stripslashes($_POST["poll_description"]));
	$listing->addChild('possible_answers', stripslashes($_POST["possible_answers"]));
	
	if(isset($_POST["anonymous"])&&$_POST["anonymous"]=="1")
	{
		$listing->addChild("anonymous", "1");
	}
	else
	{
		$listing->addChild("anonymous", "0");	
	}
	
	$listings->asXML($this->data_file); 
	
	if(!file_exists("../data/".$random_code))
	{
		if(!mkdir("../data/".$random_code))
		{
			echo "The creation of the folder in which the results from this poll will be saved - data/".$random_code." failed! Please give write permissions to the /data folder";
		}
	}
	
	?>
	<h3><?php echo $this->texts["new_added_success"];?></h3>
	<br/>
	<a href="index.php?page=create_poll" class="underline-link"><?php echo $this->texts["add_another"];?></a>
	<?php echo $this->texts["or_message"];?>
	<a href="index.php?page=polls" class="underline-link"><?php echo $this->texts["manage_listings"];?></a>
	<br/>
	<br/>
	<br/>
	<?php
	$show_add_form=false;
}	



if($show_add_form)
{
?>
 <div class="card">
		<div class="header">
			<h4 class="title"><?php  echo $this->texts["add_survey_questions"];?></h4>
		</div>
		<div class="content">
			<form action="index.php" method="post">
			<input type="hidden" name="page" value="create_poll"/>
			<input type="hidden" name="proceed_save" value="1"/>
		
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["question"];?></label>
							<input type="text" name="poll_name" class="form-control border-input"  value="">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["survey_description"];?></label>
							<textarea type="text" id="poll_description" name="poll_description" class="form-control border-input"></textarea>
						</div>
					</div>
				</div>
				
				

	
	
			
		
			<div class="row">
				<div class="col-md-6">
					<div class="form-group12" id="answers_group">
						<label><?php echo $this->texts["possible_answers"];?></label>
						<textarea id="possible_answers" name="possible_answers" rows="7" type="text" class="form-control border-input"></textarea>
					</div>
				</div>
				
			</div>
			
			

			
				
			<div class="clearfix"></div>
			<br/>
			<br/>
			
			
			<button type="submit" class="btn btn-primary btn-fill btn-wd"><?php echo $this->texts["save_survey"];?></button>
		
			<div class="clearfix"></div>
			<br/>
			</form>
		</div>
	</div>
<?php
}
?>
</div>
					
	