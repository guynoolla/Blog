	</div><!--container-xl-->
</div><!--Page Admin--> 

<footer class="footer" role="contentinfo" id="scrollTestContact">
	<div class="footer-content">
		<div class="copyright mt-0">
			<?php echo "{$jsonstore->copyright->sign} {$jsonstore->copyright->sitename} " . date('Y') . " {$jsonstore->copyright->delimeter} {$jsonstore->copyright->rights}" ?>
		</div>
	</div>
	<a href="#page-top" class="scroll-to-top" id="scrollToTopJS" style="display: block;"><i class="fa fa-angle-up"></i></a>
</footer>

  <?php echo pass_to_js() ?>
	<script src="<?php echo url_for('/assets/js/main.js') ?>"></script>

</body>
</html>