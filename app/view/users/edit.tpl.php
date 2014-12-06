<article class="article1">
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
