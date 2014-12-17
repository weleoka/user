<?php
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		}
?>
<div class="commentAll">
    <h4>
        <?php  if (count($users) == 0) : ?>
        Inga användare.
        <?php elseif (count($users) == 1) : ?>
        1 användare.
        <?php else : ?>
        <?php echo count($users); ?>
        användare.
        <?php endif; ?>
    </h4>

<?php $i = 0; foreach ($users as $user) : ?>
		  		<?php if ($i % 2 == 0 ) : ?>
        			<div class="commentUnit even">
		  		<?php else : ?>
		  			<div class="commentUnit odd">
				<?php endif; $i++; ?>

            <div class="commentBox">
					<?php if($user->active === null) {$status = 'Inaktiv';} else {$status = 'Aktiv';} ?>
					<?php if(isset($user->deleted)) {$status = $status . ' och i papperskorgen';} else { }?>

               <div class="gravatar">
  						<img src="http://www.gravatar.com/avatar/<?=md5($user->email);?>.jpg?s=60">
               </div>
               <div class="commentData">
						ID# <?=$user->id?>:
						<a href='<?=$this->url->create('users/id/' . $user->id)?>'><?=$user->acronym?></a> ( <?=$status?> )<br>
						<?php if (isset($admin)) : ?>Email: <?=$user->email?><br><?php endif; ?>
						Namn: <?=$user->name?><br>
						Skapad: <?=$user->created?>
					</div>
					<p class="commentContent">
					</p>
					<?php if (isset($admin)) : ?>
	            	<div class="commentButtonsDiv">
							<a href='<?=$this->url->create('users/delete/' . $user->id)?>'>Radera</a>&nbsp&nbsp&nbsp
							<a href='<?=$this->url->create('users/update/' . $user->id)?>'>Uppdatera</a>&nbsp&nbsp&nbsp
							<a href='<?=$this->url->create('users/softDelete/' . $user->id)?>'>Ta bort/Aterställ</a>&nbsp&nbsp&nbsp
							<a href='<?=$this->url->create('users/changeStatus/' . $user->id)?>'>Inaktivera/Aktivera</a>
						</div>
					<?php endif; ?>
				</div>	
			</div>
<?php endforeach; ?>
</div>