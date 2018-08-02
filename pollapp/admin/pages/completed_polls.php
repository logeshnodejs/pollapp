<?php
if(!isset($_REQUEST["survey_id"]))
{
?>

<?php
$this->SetAdminHeader($this->texts["completed_surveys"]);
?>

 <div class="col-md-12">
	<div class="card">
		<div class="header">
			<h4 class="title"><?php echo $this->texts["click_see_completed"];?></h4>
			
		</div>
		<div class="content table-responsive table-full-width">
			<table class="table table-striped">
				<thead>
					<th><?php echo $this->texts["id"];?></th>
					<th><?php echo $this->texts["survey_name"];?></th>
					<th><?php echo $this->texts["poll_results"];?></th>
					<th><?php echo $this->texts["total_completed"];?></th>
					
				</thead>
				<tbody>
				
				<?php
				$surveys = simplexml_load_file($this->data_file);
				$i=0;
				foreach ($surveys->poll as $survey)
				{
				?>
					<tr>
						<td><a class="underline-link" href="index.php?page=completed_polls&survey_id=<?php echo $survey->id;?>"><?php echo $survey->id;?></a></td>
						<td>
						
							<strong><?php echo $survey->name;?></strong>
						
							<br/>
							<i style="font-size:11px"><?php echo $this->text_words($survey->description,30);?></i>
				
						
						</td>
						<td>
							<br/>
						
							<?php
							$this->show_poll_chart($survey->id);
							?>
						
						</td>
						<td>
						<?php
						$total_completed=$this->get_votes_number($survey->id);
						
						if($total_completed>0)
						{
							echo "<a class=\"underline-link\" href=\"index.php?page=completed_polls&survey_id=".$survey->id."\"><strong>".$total_completed."</strong></a>";
						}
						else
						{
							echo "0";
						}
						?>
						</td>
					</tr>
				<?php
				}
				?>
				
				</tbody>
			</table>

		</div>
	</div>
</div>

<?php
}
else
{
	$id=$_REQUEST["survey_id"];
	$this->check_id($id);
	
	
	
	$xml = simplexml_load_file($this->data_file);
	$nodes = $xml->xpath('//polls/poll/id[.="'.$id.'"]/parent::*');
	$survey = $nodes[0];
	
	$survey_folder="../".$this->data_folder."/".$id;
	
	
	if(isset($_REQUEST["del"]))
	{
		unlink($survey_folder."/".$_REQUEST["del"].".xml");
	}
	
	echo "<div class=\"col-md-12\"><h3>".$this->texts["survey"].": <strong>".$survey->name."</strong></h3></div><div class=\"clearfix\"></div><br/>";

	if(!file_exists($survey_folder))
	{
		
		echo "<br/><i>".$this->texts["no_results"]."</i>";
	}
	else
	{
		$answers_file="../data/".$id."/".md5($id.$this->salt)."_details.xml";
			
		$answers_xml = simplexml_load_file($answers_file);

		$i=0;
		foreach($answers_xml->result as $answer)
		{
		
			?>
			 <div class="col-md-12">
				<div class="card">
					
					
					<div class="header">
						<h5 class="title">
							<?php  if(isset($answer->vote_date)) echo date("F j, Y, g:i a",$answer->vote_date);?> &nbsp; 
						
							<?php 
								if($answer->name!="") echo " &nbsp; - &nbsp; ".$answer->name;
								if($answer->email!="") echo " (".$answer->email.") ";
								echo " ".$answer->phone;
								echo "IP: ".$answer->ip;
							?>
						</h5>
						<div class="results-data">
						<?php
						echo $this->texts["vote"].": <strong>".$answer->data."</strong><br/>";
						
						
						?>
						</div>
					</div>
					
					<br/>
				</div>
			</div>	
			
			
			<?php
		
	
			 
		}
	}
 
}
?>