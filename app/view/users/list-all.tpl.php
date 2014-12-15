<?php
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
    <?php
    if (isset($content)) {
        echo $content;
    }
    ?>

<?php $i = 0 ?>
<?php foreach ($users as $user) : ?>
		  <?php if ($i % 2 == 0 ) : ?>
        <div class="userUnit even">
		  <?php else : ?>        
		  <div class="userUnit odd">
		  <?php endif; $i++; ?>
<?php if($user->active === null) {$status = 'Inaktiv';} else {$status = 'Aktiv';} ?>
<?php if(isset($user->deleted)) {$status = $status . ' och i papperskorgen';} else { }?>

   <img style="float: left" src="http://www.gravatar.com/avatar/<?=md5($user->email);?>.jpg?s=60">
	&nbsp&nbspID# <?=$user->id?>: <a href='<?=$this->url->create('users/id/' . $user->id)?>'><?=$user->acronym?></a> ( <?=$status?> )<br>
	<?php if (isset($admin)) : ?>&nbsp&nbspEmail: <?=$user->email?><br><?php endif; ?>
	&nbsp&nbspNamn: <?=$user->name?><br>
	Skapad: <?=$user->created?>

		<?php if (isset($admin)) : ?>

	<p>
		<a href='<?=$this->url->create('users/delete/' . $user->id)?>'>Radera</a>&nbsp&nbsp&nbsp
		<a href='<?=$this->url->create('users/update/' . $user->id)?>'>Uppdatera</a>&nbsp&nbsp&nbsp
		<a href='<?=$this->url->create('users/softDelete/' . $user->id)?>'>Ta bort/Aterställ</a>&nbsp&nbsp&nbsp
		<a href='<?=$this->url->create('users/changeStatus/' . $user->id)?>'>Inaktivera/Aktivera</a></p>
	<hr>

		<?php endif; ?>
</div>
<?php endforeach; ?>
