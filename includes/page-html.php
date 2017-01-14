<div class="wrap welcome-wrap">
    <h1>Welcome To <?php echo $this->plugin_name;?>   <?php echo $this->version; ?></h1>
    <div class="about-text plugin_welcome_text">
        Thanks for installing! <?php echo $this->plugin_name;?>  <?php echo $this->version; ?> is more powerful, stable and secure than ever before. We hope you enjoy using it. </div>



    <div class="plugin_welcome_actions">
     		<?php $this->get_downloads(); ?>
		   <a  href="https://twitter.com/share" 
			   class="twitter-share-button" 
			   data-url="<?php echo $this->wp_plugin_url; ?>" 
			   data-text="<?php echo $this->tweet_text; ?>" 
			   data-via="<?php echo $this->twitter_user; ?>" 
			   data-hashtags="<?php echo $this->twitter_hash;?>">Tweet</a>
  

		   <a href="https://twitter.com/<?php echo $this->twitter_user; ?>" 
			  class="twitter-follow-button" 
			  data-show-count="false" 
			  data-show-screen-name="false">Follow @<?php echo $this->twitter_user; ?></a>
		
		   <a href="https://twitter.com/intent/tweet?button_hashtag=<?php echo $this->twitter_hash;?>" 
			  class="twitter-hashtag-button" 
			  data-related="<?php echo $this->twitter_user; ?>" 
			  data-url="<?php echo $this->wp_plugin_url; ?>">Tweet #<?php echo $this->twitter_hash;?></a>
		   

		   <div class="fb-like" 
				data-href="<?php echo $this->wp_plugin_url; ?>" 
				data-layout="standard" 
				data-action="recommend" 
				data-show-faces="true" 
				data-share="true"></div> 
         
        
        
        <iframe src="https://ghbtns.com/github-btn.html?user=<?php echo $this->gitub_user;?>&repo=<?php echo $this->github_repo; ?>&type=star&count=true" 
				frameborder="0" scrolling="0" width="60px" height="20px"></iframe>
        
        <iframe src="https://ghbtns.com/github-btn.html?user=<?php echo $this->gitub_user;?>&repo=<?php echo $this->github_repo; ?>&type=watch&count=true&v=2" 
				frameborder="0" scrolling="0" width="70px" height="20px"></iframe>
        
        <iframe src="https://ghbtns.com/github-btn.html?user=<?php echo $this->gitub_user;?>&repo=<?php echo $this->github_repo; ?>&type=fork&count=true" 
				frameborder="0" scrolling="0" width="70px" height="20px"></iframe>
        
        <iframe src="https://ghbtns.com/github-btn.html?user=<?php echo $this->gitub_user;?>&type=follow&count=true" 
				frameborder="0" scrolling="0" width="170px" height="20px"></iframe>
 

    </div>


    <div class="content_container">
        <div class="changelog"> 
			<?php 
				$this->get_decs(); 
			?>
        </div>
        
        
        
        
        
        
        
        
		<div class="singnUpFORM">
			<div id="poststuff">
				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox">
						<div class="handlediv" title="Click to toggle"> <br /> </div>
						<h3 class="hndle"><span><?php _e('Subscribe to our mailing list',WC_RBP_TXT); ?></span></h3> 
						<div class="option">
							<div id="mc_embed_signup">
								<form action="http://varunsridharan.us11.list-manage.com/subscribe/post?u=438373310b9f3b6302526f737&amp;id=71f582aed1" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
									<div id="mc_embed_signup_scroll"> 

										<div id="mce-responses" class="clear">
											<div class="response" id="mce-error-response" style="display:none"></div>
											<div class="response" id="mce-success-response" style="display:none"></div>
										</div>
										
										<div class="mc-field-group">
											<label for="mce-EMAIL">
												<?php _e('Email Address',WC_RBP_TXT); ?>  <span class="asterisk">*</span>
											</label>
											<input type="email" value="<?php echo get_option('admin_email'); ?>" name="EMAIL" 
											class="required email" id="mce-EMAIL">
										</div>
										
										<div class="mc-field-group">
											<label for="mce-FNAME">
												<?php _e('First Name',WC_RBP_TXT); ?> <span class="asterisk">*</span>
											</label>
											<input type="text" value="" name="FNAME" class="required" id="mce-FNAME">
										</div>
										
										<div class="mc-field-group">
											<label for="mce-LNAME"><?php _e('Last Name',WC_RBP_TXT); ?> </label>
											<input type="text" value="" name="LNAME" class="" id="mce-LNAME">
										</div>
										
										<div class="mc-field-group">
											<label for="mce-WEBSITE">
												<?php _e('Website',WC_RBP_TXT); ?> <span class="asterisk">*</span>
											</label>
											
											<input type="url" value="<?php echo get_site_url(); ?>" name="WEBSITE" class="required url" 
												   id="mce-WEBSITE">
										</div>
										
										<div class="mc-field-group">
											<label for="mce-COUNTRY">
												<?php _e('Country',WC_RBP_TXT); ?> <span class="asterisk">*</span>
											</label>
											<input type="text" value="" name="COUNTRY" class="required" id="mce-COUNTRY">
											<input type="hidden" value="<?php echo $this->plugin_name; ?>" name="PLUGINNAME" class="" id="mce-PLUGINNAME">
										</div>
									
									<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
										<div style="position: absolute; left: -5000px;">
											<input type="text" name="b_438373310b9f3b6302526f737_71f582aed1" tabindex="-1" value="">
										</div>
										<div class="clear">
											<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        
        
        
       <div class="changelog full_width">
       	<?php $this->get_change_log(); ?>
       </div>
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
    </div>

</div>


<style>

.welcome-wrap {
    float: left;
    font-size: 15px;
    margin: 0;
    padding-right: 20px;
    position: relative;
    width: 98%;
}
.welcome-wrap h1 {
	color: #32373c;
	font-size: 2.8em;
	font-weight: 400;
	line-height: 1.2em;
	margin: 0.2em 0 0 0;
	padding: 0;
}

.welcome-wrap .about-text {
	color: #777;
	margin: 1em 0px 1em 0;
	min-height: 60px;
}
	
.welcome-wrap .plugin_welcome_text {
	font-size: 19px;
	font-weight: normal;
	line-height: 1.6em;
	margin-bottom: 1em !important;
}

.welcome-wrap .content_container {
    float: left;
    margin-bottom: 40px;
    width: 100%;
}
	
.changelog {
    background: #fff none repeat scroll 0 0;
    float: left;
    margin: 20px 2% 20px 0;
    padding: 10px 20px;
    width: 46%;
}
	
	
.changelog h1 { 
	margin: 0;  
}
	
.singnUpFORM{
	    display: inline-block;
    margin-left: 2%;
    margin-top: 10px;
    width: 46%;
}
#postbox-container-1 {
	float: left;
	margin-right: 0 !important; 
}

.postbox {
	position: relative;
	min-width: 255px;
	border: 1px solid #e5e5e5;
	-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
	background: #fff;
}	
	
div#poststuff { 
min-width: 15%; 
}
	
	
.changelog.full_width {
    display: inline-block;
    margin-right: 0 !important;
    width: auto;
}
	
	
.plugin_welcome_actions .downloads_count {
    display: inline-block;
    margin: 0 10px 0 0;
    vertical-align: top;
}
	
</style>
<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
	    #mc_embed_signup form {
        padding-top: 0!important;
        padding-left: 0;
    }
    
    #mc_embed_signup div.mce_inline_error {
        background-color: #ea7274;
    }
    
    #mc_embed_signup .mc-field-group input {
        margin: 0 auto !important;
    }	
	
    .option:nth-child(2n+1) {
        background: #fcfcfc none repeat scroll 0 0;
    }
    
    .option {
        border-width: 1px 0;
        padding: 6px 10px 8px;
    }
    
    .option p.description {
        line-height: 1.2em;
    }
    
    .option p {
        margin: 0;
        padding: 0;
    }
</style>


<link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
<script>
    ! function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + '://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'twitter-wjs');
</script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=477093012452061";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Place this tag right after the last button or just before your close body tag. -->
<script async defer id="github-bjs" src="https://buttons.github.io/buttons.js"></script>
<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>

<script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='WEBSITE';ftypes[3]='url';fnames[4]='COUNTRY';ftypes[4]='text';fnames[5]='PLUGINNAME';ftypes[5]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
