	<!-- footer starts here -->
        
        <?php
            $mtime = microtime();
	    $mtime = explode(" ",$mtime);
	    $mtime = $mtime[1] + $mtime[0];
	    $endtime = $mtime;
	    $totaltime = ($endtime - $starttime);
        ?>
	<div id="footer-wrapper" class="container_16">
	
		<div id="footer-bottom">
	
			<p class="bottom-left">			
			&nbsp; &copy;2012 Copyright TVSP - DUC Anthony
			</p>	

			<p class="bottom-right" >
                                <a><?php echo number_format($totaltime,4,',','').' s.'; ?></a> |
				<a href="index.php">Home</a> |
				<a href="http://thetvdb.com/" TARGET="_blank">Tvdb</a> |
                                <a href="http://www.styleshout.com/" TARGET="_blank">Styleshout</a> 
			</p>
	
		</div>	
			
	</div>
	<!-- footer ends here -->
