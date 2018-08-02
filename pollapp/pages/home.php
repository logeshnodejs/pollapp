	<?php
	$surveys = simplexml_load_file($this->data_file);
	$survey_counter=0;
	foreach ($surveys->poll as $survey)
	{
	?>
		<div class="block-wrap">
			<h4><a class="custom-color" href="<?php echo $this->survey_link($survey->id,$survey->name);?>"><?php echo $survey->name;?></a></h4>
			<p><?php echo $survey->description;?></p>
			<a class="btn btn-default btn-md custom-back-color" href="<?php echo $this->survey_link($survey->id);?>"><?php echo $this->texts["start_survey"];?></a>
		</div>
	<?php
		$survey_counter++;
	}
	
	if($survey_counter==0)
	{
		echo "<br/><h2>".$this->texts["no_surveys_found"]."</h2>";
	}
	?>



<?php
$this->Title($this->texts["surveys"]);
$this->MetaDescription("");
?>