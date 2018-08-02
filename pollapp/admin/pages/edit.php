<?php
if(!defined('IN_SCRIPT')) die("");

$id=intval($_REQUEST["id"]);

$this->ms_i($id);

$show_add_form=true;

$xml = simplexml_load_file($this->data_file);

if(isset($_POST["proceed_save"]))
{
	$xml->poll[$id]->description=stripslashes($_POST["survey_description"]);
	$xml->poll[$id]->name=stripslashes($_POST["survey_name"]);
	
	if(isset($_POST["anonymous"]))
	{
		$xml->poll[$id]->anonymous="1";
	}
	else
	{
		$xml->poll[$id]->anonymous="0";	
	}
		
	$xml->poll[$id]->possible_answers=stripslashes($_POST["possible_answers"]);
	
	$xml->asXML($this->data_file); 
	echo "<h3>".$this->texts["modifications_saved"]."</h3><br/>";
}	

if($show_add_form)
{
?>
 <div class="card">
 
		
		<a class="btn btn-default pull-right" href="index.php?page=polls" style="margin-top:12px;margin-right:12px;"><i class="ti-angle-left"></i> <?php echo $this->texts["go_back"];?></a>
		<div class="clearfix"></div>

		<div class="header">
			<h4 class="title"><?php  echo $this->texts["edit_survey"];?></h4>
		</div>
		
		<div class="content">
			
			<form action="index.php" method="post"   enctype="multipart/form-data">
			<input type="hidden" name="page" value="edit"/>
			<input type="hidden" name="proceed_save" value="1"/>
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
			
		
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["question"];?></label>
							<input type="text" name="survey_name" class="form-control border-input"  value="<?php echo $xml->poll[$id]->name;?>">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["survey_description"];?></label>
							<textarea type="text" id="survey_description" name="survey_description" class="form-control border-input"><?php echo $xml->poll[$id]->description;?></textarea>
						</div>
					</div>
				</div>
				
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["possible_answers"];?></label>
							<textarea type="text" rows="7" id="possible_answers" name="possible_answers" class="form-control border-input"><?php echo $xml->poll[$id]->possible_answers;?></textarea>
						</div>
					</div>
				</div>
				
				
							
			
			
			<div class="clearfix"></div>
			
			
				
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

					
	