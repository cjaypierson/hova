<?php 

/* Helper class for uniform elements in admin pages */ 

add_action('mb-display-logo', array('maxAdmin','logo')); 
add_action('mb-display-tabs', array('maxAdmin','tab_menu')); 
add_action('mb-display-ads', array('maxAdmin', 'display_ads')); 

class maxAdmin 
{
	static $tabs = null;
					
		
	static function logo()
	{
	?> 
			<?php _e('Brought to you by', 'maxbuttons') ?>
			<a href="http://maxfoundry.com/products/?ref=mbfree" target="_blank"><img src="<?php echo maxButtons::get_plugin_url() ?>/images/max-foundry.png" alt="Max Foundry" /></a>
			<?php printf(__('Upgrade to MaxButtons Pro today! %sClick Here%s', 'maxbuttons'), '<a href="http://www.maxbuttons.com/pricing/?utm_source=wordpress&utm_medium=mbrepo&utm_content=button-list-upgrade&utm_campaign=plugin">', '</a>' ) ?>
	<?php
	}
	
	static function tab_items_init()
	{
			self::$tabs = array(
						"list" => array("name" =>  __('Buttons', 'maxbuttons'), 
										 "link" => "page=maxbuttons-controller&action=list",
										 "active" => "maxbuttons-controller", ), 
										 
						"pro" => array( "name" => __('Upgrade to Pro', 'maxbuttons'),
										 "link" => "page=maxbuttons-pro",
										 "active" => "maxbuttons-pro",
										 ),
						"settings" => array("name" => __('Settings', 'maxbuttons'),
										 "link" => "page=maxbuttons-settings",
										 "active" => "maxbuttons-settings",
										 "userlevel" => 'manage_options'  ), 
						"support" => array("name" => __('Support', 'maxbuttons'), 
										 "link" => "page=maxbuttons-support",
										 "active" => "maxbuttons-support",
										 "userlevel" => 'manage_options'
										 )
			); 
	}
	
	static function tab_menu()
	{
		 self::tab_items_init(); 
	?>
			<h2 class="tabs">
				<span class="spacer"></span>
		<?php foreach (self::$tabs as $tab => $tabdata) { 
			if (isset($tabdata["userlevel"]) && ! current_user_can($tabdata["userlevel"]))
				continue; 

			$link = admin_url() . "admin.php?" . $tabdata["link"]; 
			$name = $tabdata["name"];
			$active = ''; 
			if ($tabdata["active"] == $_GET["page"])
				$active = "nav-tab-active";
				
				echo "<a class='nav-tab $active' href='$link'>$name</a>"; 

		}
		echo "</h2>";	
	}
	
	static function display_ads()
	{ ?>
        <div class="ads">
            <h3><?php _e('MaxButtons Pro only $19', 'maxbuttons'); ?></h3>
            <p><?php _e('Do so much more with MB Pro.  Get 2 free buttons packs when you buy.  Just use MBFREE at checkout.', 'maxbuttons'); ?></p>
            <p><strong><?php _e('Some extra features for going Pro:', 'maxbuttons'); ?></strong></p>
            <ul>
                <li><?php _e('Great Support', 'maxbuttons'); ?></li>
                <li><?php _e('Pre-Made Button Packs', 'maxbuttons'); ?></li>
                <li><?php _e('Two Lines of Editable Text', 'maxbuttons'); ?></li>
                <li><?php _e('Add An Icon To Your Buttons', 'maxbuttons'); ?></li>
                <li><?php _e('Google Web Fonts', 'maxbuttons'); ?></li>
                <li><?php _e('Many more benefits!', 'maxbuttons'); ?></li>
            </ul>
            <a class="button-primary" href="http://www.maxbuttons.com/pricing/?utm_source=wordpress&utm_medium=mbrepo&utm_content=button-list-sidebar-19&utm_campaign=plugin"><?php _e('Get MaxButtons Pro Now!', 'maxbuttons'); ?></a>
        </div>
        
        <div class="ads">
            <h3><?php _e('Get MaxGalleria', 'maxbuttons'); ?></h3>
            <p><?php _e('Download our free WordPress Gallery plugin MaxGalleria!  Add-ons for Albums, Videos, and Image Sliders.', 'maxbuttons'); ?></p>
            <a class="button-primary" href="https://wordpress.org/plugins/maxgalleria/"><?php _e('Get MaxGalleria Now!', 'maxbuttons'); ?></a>
        </div>
        
        <div class="ads">
            <h3><i class="fa fa-cogs"></i> <?php _e('Font Awesome Support', 'maxbuttons'); ?></h3>
            <p><?php _e('With MaxButtons Pro you have access to all 439 Font Awesome icons, ready to add to your buttons.', 'maxbuttons'); ?></p>
            <p><?php _e('Never upload another icon again, just choose an icon and go about your normal button-making business.', 'maxbuttons'); ?></p>
            <a class="button-primary" href="http://www.maxbuttons.com/pricing/?utm_source=wordpress&utm_medium=mbrepo&utm_content=button-list-sidebar-99&utm_campaign=plugin"><?php _e('Use Font Awesome!', 'maxbuttons'); ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
        <?php
	}




} // class
?>
