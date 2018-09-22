	</div><!-- End role["main"] -->
	<div class="footer row">
		<div class="footer__call-to-action columns small-12">
			<p> <b>Benötigen Sie Hilfe</b> bei der Umsetzung Ihres Projektes oder wollen Sie sich <b>unverbindlich beraten lassen?</b> </p>
		</div>
		<div class="footer__info columns small-12 row">
			<div class="footer__info__bail columns small-12 medium-4">
				<h4>Schreiben Sie uns</h4>
				<p>info@concept360.ch</p>
			</div>
			<div class="footer__info__telefon columns small-12 medium-4">
				<h4>Rufen Sie uns an</h4>
				<p>+41 44 545 36 40</p>
			</div>
			<div class="footer__info__adresse columns small-12 medium-4">
				<h4>Kommen Sie vorbei</h4>
				<p>Regensbergstrasse 126, CH-8050 Zürich</p>
			</div>
		</div>
		<div class="footer__contact-form columns small-12">
			<?php echo do_shortcode( '[contact-form-7 id="676" title="Contact Form"]' ); ?>
		</div>
		<div class="footer__copyright columns small-12">
			©2018 CONCEPT360
		</div>
	</div>
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your thbe, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	 wp_footer();
?>
</body>
</html>
