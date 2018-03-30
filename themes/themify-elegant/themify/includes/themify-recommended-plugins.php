<ol class="theme-list themify-recommended-plugins clearfix">
	<?php foreach( $plugins as $plugin ) : ?>
		<li class="theme-post">
			<figure class="theme-image">
				<a href="<?php echo $plugin['page']; ?>" target="_blank">
					<img src="<?php echo $plugin['image']; ?>" alt="<?php echo $plugin['name']; ?>">
				</a>
			</figure>
			<div class="theme-info">
				<div class="theme-title">
					<h3><a href="<?php echo $plugin['page']; ?>" target="_blank"><?php echo $plugin['name']; ?></a></h3>
					<a class="tag-button lightbox" target="_blank" href="<?php echo $plugin['page']; ?>"><?php echo _e( 'More info', 'themify' ); ?></a>
				</div>
				<!-- /theme-title -->
				<div class="theme-excerpt">
					<p><?php echo $plugin['desc']; ?></p>
				</div>
				<!-- /theme-excerpt -->
			</div>
			<!-- /theme-info -->	
		</li>
	<?php endforeach; ?>
</ol>