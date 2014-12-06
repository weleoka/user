<h3><?= $user->name ?></h3>
	<p>
		<a href="<?= $this->url->create('user/update/' . $user->id); ?>">Uppdatera användare</a><br>
	</p>
	<p>Acronym: <?= $user->acronym ?></p>
  
	<p>Email: <?= $user->email ?></p>
	<p>Status: <?php if($user->active === null) {echo 'Inaktiv';} else {echo 'Aktiv';} ?></p>
	<p>       
		<a href="<?= $this->url->create('user/activate/' . $user->id); ?>">Aktivera användare</a>
	</p>
	<p> 
		<a href="<?= $this->url->create('user/deactivate/' . $user->id); ?>">Deaktivera användare</a>
	</p> 
     
	<p>Status: <?php if($user->deleted != null) {echo 'Borttagen';} else {echo 'Inte borttagen';} ?></p>
	<p>       
		<a href="<?= $this->url->create('user/softdelete/' . $user->id); ?>">Ta bort användare</a>
	</p>
	<p> 
		<a href="<?= $this->url->create('user/softundo/' . $user->id); ?>">Ångra ta bort användare</a>
	</p> 
	<p>
		<a href="<?= $this->url->create('user/delete/' . $user->id); ?>">Ta bort permanent</a><br>
	</p>
  