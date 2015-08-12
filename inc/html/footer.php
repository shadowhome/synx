<hr />
	<footer>
		<center><small>© 2015. All Rights Reserved.</small></center>
		<span id="top-link-block" class="hidden">
		    <a href="#top" class="well well-sm">
		        <i class="glyphicon glyphicon-chevron-up"></i> Back to Top
		    </a>
		</span><!-- /top-link-block -->
	</footer>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="resource/vendor/bootstrap-sass/assets/javascripts/bootstrap.min.js"></script>

 		<script type="text/javascript">
			$('#backToTopBtn').click(function(){
				$('html,body').animate({scrollTop:0},'slow');return false;
			});
			// Only enable if the document has a long scroll bar
			// Note the window height + offset
			if ( ($(window).height() + 100) > $(document).height() ) {
			    $('#top-link-block').removeClass('hidden').affix({
			        // how far to scroll down before link "slides" into view
			        offset: {top:100}
			    });
			}
		</script>
  </body>
</html>