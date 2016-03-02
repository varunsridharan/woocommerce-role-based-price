<?php
$slug =  sanitize_html_class($wc_rbp_plugin_data['name']);
?>

<div class="plugin-card plugin-card-<?php echo $slug; ?>">
	<div class="plugin-card-top">
		<div class="name column-name">
			<h3>
				<a class="thickbox" href="#"> <?php echo $wc_rbp_plugin_data['name']; ?> <img alt="" class="plugin-icon" src=" <?php echo $wc_rbp_plugin_data['icon']; ?>"> </a>
			</h3>
		</div>
		<div class="action-links">
			<ul class="plugin-action-buttons">
				<li>
					<a data-name="<?php echo $wc_rbp_plugin_data['name']; ?> <?php echo $wc_rbp_plugin_data['version']; ?>" aria-label="Install <?php echo $wc_rbp_plugin_data['name']; ?> <?php echo $wc_rbp_plugin_data['version']; ?> now" href="#" data-slug="<?php echo $slug;?>" class="install-now button">Install Now</a>
				</li>
				<li>
					<a data-title="<?php echo $wc_rbp_plugin_data['name']; ?> <?php echo $wc_rbp_plugin_data['version']; ?>" aria-label="More information about <?php echo $wc_rbp_plugin_data['name']; ?> <?php echo $wc_rbp_plugin_data['version']; ?>" class="thickbox" href="#">More Details</a>
				</li>
			</ul>
		</div>
		<div class="desc column-description">
			<p><?php echo $wc_rbp_plugin_data['decs']; ?></p>
			<p class="authors"> 
				<cite>By <a href="<?php echo $wc_rbp_plugin_data['author_link']; ?>">
					 <?php echo $wc_rbp_plugin_data['author']; ?></a>
				</cite>
			</p>
		</div>
	</div>
	<div class="plugin-card-bottom">
		<div class="column-updated">
			<strong>Last Updated:</strong> 
			<span title="<?php echo $wc_rbp_plugin_data['last_update']; ?>"><?php echo $wc_rbp_plugin_data['last_update']; ?></span>
		</div>
		<div class="column-downloaded"> 300,000+ Active Installs </div>
		
		<div class="column-compatibility">
			<span class="compatibility-compatible"><strong>Compatible</strong> with your version of WordPress</span> 
		</div>
	</div>
</div>