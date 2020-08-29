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
			<ul class="menu-social-items" class="social-menu">
				<li class="menu-item menu-item-type-custom">
					<a href="https://www.facebook.com/colorlib">
						<i class="social_icon fa fa-facebook"><span>Facebook</span></i>
					</a>
				</li>
				<li class="menu-item menu-item-type-custom">
					<a href="https://twitter.com/colorlib">
						<i class="social_icon fa fa-twitter"><span>Twitter</span></i>
					</a>
				</li>
				<li class="menu-item menu-item-type-custom">
					<a href="https://www.youtube.com/channel/UCOaovjLNXdIch2vLFsw_uew">
					<i class="social_icon fa fa-youtube"><span>youtube</span></i></a>
				</li>
				<li class="menu-item menu-item-type-custom">
					<a href="https://plus.google.com/100289203607749737039">
						<i class="social_icon fa fa-google-plus"><span>Google+</span></i>
					</a>
				</li>
				<li class="menu-item menu-item-type-custom">
					<a href="https://instagram.com">
						<i class="social_icon fa fa-instagram"><span>Instagram</span></i>
					</a>
				</li>
				<li class="menu-item menu-item-type-custom">
					<a href="https://github.com/puikinsh/">
						<i class="social_icon fa fa-github"><span>Github</span></i>
					</a>
				</li>
			</ul>
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
