</div> <!--Content wrapper div close-->

<?php
	$url = $_SERVER['REQUEST_URI'];
	
	if ($url == "/" || url_contain(['index.php','post','topic','preview'])) {
		$bg = '';
	} else {
		$bg = 'bg-other-lk';
	}

?>
<footer class="<?php echo $bg ?>" role="contentinfo" id="scrollFooter">
	<div class="footer-content">
		<div class="social-links-widget more-space-between">
			<?php include '_social_links_list.php' ?>
		</div>
		<div class="copyright mt-4">
			<?php echo "{$jsonstore->copyright->sign} {$jsonstore->copyright->sitename} " . date('Y') . " {$jsonstore->copyright->delimeter} {$jsonstore->copyright->rights}" ?>
		</div>
	</div>
	<a href="#topScrollElement" class="scroll-to-top" id="scrollToTopJS" style="display: block;"><i class="fa fa-angle-up"></i></a>
</footer>

<?php echo pass_to_js() ?>
<script src="<?php echo url_for('assets/js/main.js') ?>" type="text/javascript"></script>

<?php if ($url == '/' || url_contain('index.php')): ?>
	<script type="text/javascript" src="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js"></script>
<?php endif; ?>

</body>
</html>
