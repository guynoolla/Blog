	</div><!--container-xl-->
</div><!--Page Admin--> 

<footer class="footer" role="contentinfo" id="scrollTestContact">
	<div class="footer-content">
		<div class="copyright mt-0">
			<?php echo "{$jsonstore->copyright->sign} {$jsonstore->copyright->sitename} " . date('Y') . " {$jsonstore->copyright->delimeter} {$jsonstore->copyright->rights}" ?>
		</div>
	</div>
	<a href="#page-top" class="scroll-to-top" id="scrollToTopJS" style="display:block;width:2.5rem;height:2.5rem;">
		<!--<i class="fa fa-angle-up"></i>-->
		<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-icon svg-inline--fa fa-angle-up fa-w-10 fa-3x"><path fill="currentColor" d="M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z" class=""></path></svg>
	</a>
</footer>

  <?php echo pass_to_js() ?>
	<script src="<?php echo url_for('/assets/js/main.js') ?>"></script>

</body>
</html>