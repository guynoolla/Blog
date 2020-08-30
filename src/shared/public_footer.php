</div> <!--Content wrapper div close-->

<?php
	$bg = '';
	$url = $_SERVER['REQUEST_URI'];
	if ($url != '/' && !url_contain('post')) {
		$bg = 'bg-light-lk';
	}

?>
<footer class="<?php echo $bg ?>" role="contentinfo" id="scrollTestContact">
	<div class="footer-content">
		<div class="social-links-widget more-space-between">
			<?php include '_social_links_list.php' ?>
		</div>
		<div class="copyright mt-4">
			Light Kite. Theme by <a href="https://colorlib.com/" target="_blank">Gainulla</a> web developer
		</div>
	</div>
	<a href="#topScrollElement" class="scroll-to-top" id="scrollToTopJS" style="display: block;"><i class="fa fa-angle-up"></i></a>
</footer>

<script src="<?php echo url_for('assets/js/main.js') ?>" type="text/javascript"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js"></script>
</body>
</html>
