<?php
if(!isset($_COOKIE["AuthUser"])) 
{
?>
	
	<a target="_blank" href="http://www.netartmedia.net/en_Contact.html" class="top-right-link"><img src="images/contact.png"/>
	<?php echo $this->texts["have_questions"];?>
	</a>
<?php
}
else
{
?>

	<li class="dropdown">
	  <a href="#">
			
			<p class="notification" id="top_notification">
			
			</p>
			
	  </a>
	  
</li>


<li>
	<a href="logout.php">
		<i class="ti-shift-right"></i>
		<p><?php echo $this->texts["logout"];?></p>
	</a>
</li>


<?php
}
?>
