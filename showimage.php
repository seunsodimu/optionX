<html>
    <body>
        <?php if(isset($_GET['img'])){ 
            $img = "assets/files/".  urldecode($_GET['img']);
            ?>
        <img src="<?php echo $img; ?>"/><?php echo $img; ?>
        <?php } ?>
    </body>
</html>