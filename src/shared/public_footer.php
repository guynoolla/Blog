<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package activello
 */
?>
				</div><!-- close .*-inner (main-content or sidebar, depending if sidebar is used) -->
			</div><!-- close .row -->
		</div><!-- close .container -->
	</div><!-- close .site-content -->

	<div id="footer-area">
		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="site-info container">
				<div class="row">
					<P>Social icons</P>
					<div class="copyright col-md-12">
						<P>Copyright</P>
						<P>Footer info</P>
					</div>
				</div>
			</div><!-- .site-info -->
			<button class="scroll-to-top"><i class="fa fa-angle-up"></i></button><!-- .scroll-to-top -->
		</footer><!-- #colophon -->
	</div>
</div><!-- #page -->

<?php echo pass_to_js() ?>
<script src="<?php echo url_for('/assets/scripts/main.js') ?>"></script>

</body>
</html>
