<article class="article1">
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

    <?php if (isset($byline)) : ?>
        <footer class="byline">
            <img id='me' src='<?=$this->url->asset("img/me.jpg")?>' alt='kawe14'/>
            <?= $byline ?>
        </footer>
    <?php endif; ?>
</article> 
