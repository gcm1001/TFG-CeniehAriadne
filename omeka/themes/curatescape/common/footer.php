	
	</div><!--end wrap-->
	
	<footer class="main container">
		<nav id="footer-sponsor">
		    <a href="https://cenieh.es/"><figure><img src="<?php echo mh_cenieh_logo(); ?>"/> </figure></a></nav>
		<nav id="footer-sponsor">
		    <a href="https://ariadne-infrastructure.eu/"><figure><img src="<?php echo mh_ariadne_logo(); ?>"/> </figure></nav>
		<nav id="footer-sponsor">
		    <a href="https://www.ubu.es/"><figure><img src="<?php echo mh_ubu_logo(); ?>"/> </figure></nav>
		 
		<div class="default">
			<?php echo mh_footer_find_us();?>
			<div id="copyright"><?php echo mh_license();?></div> 
		</div>
		
		<?php echo mh_footer_cta();?>
		
		<div class="custom"><?php echo get_theme_option('custom_footer_html');?></div>
	
		<?php echo fire_plugin_hook('public_footer', array('view'=>$this)); ?>	
		<?php echo mh_google_analytics();?>	
			
	</footer>
</div> <!-- end page-content -->

<div hidden class="hidden">
	<!-- Mmenu Markup -->
	<?php echo mh_simple_search('sidebar-search',array('id'=>'sidebar-search-form'),__('Search - Drawer'));?>
	<nav aria-label="<?php echo __('Drawer Navigation');?>" id="offscreen-menu">
		<?php echo mh_global_nav(true);?>
	</nav>
</div>
	
</body>
</html>
