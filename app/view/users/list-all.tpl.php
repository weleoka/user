<?php 
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		} if (isset($feedback)) {
				echo "<h5>" . $feedback . "</h5>";
			//	unset($feedback);
		}  if (isset($_SESSION['form-output'])) {
        		$output = $_SESSION['form-output'];
				echo "<h5>" . $output . "</h5>";        		
        		unset($_SESSION['form-output']);
      } 
?>

<?php foreach ($users as $user) : ?>
<?php if($user->active === null) {$status = 'Inaktiv';} else {$status = 'Aktiv';} ?>
<?php if(isset($user->deleted)) {$status = $status . ' och i papperskorgen';} else { }?>
<p>
	ID# <?=$user->id?>: <a href='<?=$this->url->create('users/id/' . $user->id)?>'><?=$user->acronym?></a> ( <?=$status?> )<br>
	Email: <?=$user->email?><br>
	Namn: <?=$user->name?><br>
	Status: <?=$status?>	
</p>

<p>
<a href='<?=$this->url->create('users/delete/' . $user->id)?>'>Radera</a>&nbsp&nbsp&nbsp
<a href='<?=$this->url->create('users/update/' . $user->id)?>'>Uppdatera</a>&nbsp&nbsp&nbsp
<a href='<?=$this->url->create('users/softDelete/' . $user->id)?>'>Ta bort/Aterställ</a>&nbsp&nbsp&nbsp
<a href='<?=$this->url->create('users/changeStatus/' . $user->id)?>'>Inaktivera/Aktivera</a></p>
<hr>
<?php endforeach; ?>
 
 
