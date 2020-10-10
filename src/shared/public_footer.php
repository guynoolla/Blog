</div> <!--Content wrapper div close-->

<?php
	$url = $_SERVER['REQUEST_URI'];
	
	if (url_contain(['/pasword/', '/email/'])) $bg = 'bg-other-lk';
	else $bg = '';

?>
<footer class="<?php echo $bg ?>" role="contentinfo" id="scrollFooter">
	<div class="footer-content">
		<div class="social-links-widget more-space-between">
			<?php include '_social_links_list.php' ?>
		</div>
		<div class="copyright mt-4"><?php echo $jsonstore->copyright ?></div>
	</div>
	<a href="#page-top" class="scroll-to-top" id="scrollToTopJS" style="display:block;width:2.5rem;height:2.5rem;">
		<!--<i class="fa fa-angle-up"></i>-->
		<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-icon svg-inline--fa fa-angle-up fa-w-10 fa-3x"><path fill="currentColor" d="M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z" class=""></path></svg>
	</a>
</footer>

<?php echo pass_to_js() ?>
<script src="<?php echo url_for('assets/js/main.js') ?>" type="text/javascript"></script>

<?php if (isset($carousel_posts)): ?>
	<script type="text/javascript" src="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js"></script>
<?php endif; ?>

</body>
</html>
