<?php
$blockClass["responsive"] = "responsiveBlock"; 
$blockOrder[20][] = "responsive"; 

class responsiveBlock extends maxBlock 
{
	protected $blockname = 'responsive'; 
	protected $fields = array("options" => '',
							  "auto_responsive" => array("default" => 1),
							  "media_query" => array("default" => array()
							  				), 
							  
							  
							  
							  ); 
	// multifields for manual conversion // define +/- the same as normal fields. 
	// to find solution in parsing stuff back to button.
	protected $multi_fields = array("mq_button_width" => array("default" => 0, 
															   "css" => "width", 
															   "csspseudo" => "responsive", 
															   
															),
									"mq_button_width_unit" => array("default" => 'px', 
																	"css" => "width_unit",
																	"csspseudo" => "responsive",
															), 
									"mq_container_width" => array("default" => 0, 
																"css" => "width",
																"csspart" => "mb-container",
																"csspseudo" => "responsive",
															),
									"mq_container_float" => array("default" => "", 
																"css" => "float", 
																"csspart" => "mb-container",
																"csspseudo" => "responsive",
														),
									"mq_container_width_unit" => array("default" => "px",
																	"css" => "width_unit",
																	"csspart" => "mb-container", 
															), 
									"mq_custom_minwidth" => array("default" => 0, 
															  "css" => "custom_minwidth"), 
									"mq_custom_maxwidth" => array("default" => 0, 
															  "css" => "custom_maxwidth"),
									); 
	
	
	public function parse_css($css, $mode = 'normal')
	{
		if ($mode != 'normal') 
			return $css;
			
		$data = $this->data[$this->blockname];
		
		if (isset($data["auto_responsive"]) && $data["auto_responsive"] == 1)
		{	// generate auto_rules for responsive.
			$css["maxbutton"]["responsive"]["phone"]["width"] = "90%"; 
			$css["mb-container"]["responsive"]["phone"]["width"] = "90%"; 
 			$css["mb-container"]["responsive"]["phone"]["float"] = "none"; 
 
		}
		
		if (! isset($data["media_query"]))
 			return $css;
 			
		foreach($data["media_query"] as $query => $fields )
		{
			foreach($fields as $field => $value)
			{
				$csspart = (isset($this->multi_fields[$field]["csspart"])) ? $this->multi_fields[$field]["csspart"] : 'maxbutton' ; 
				$css_stat = $this->multi_fields[$field]["css"]; 

				//$value = (isset($data[$field])) ? $data[$field] : null; 
				//if (is_null($value)) continue; 
				
				if ($value == '' || $value == '0') 
				{  }
				elseif ($query != 'custom' && ($field == 'mq_custom_maxwidth' || $field == "mq_custom_minwidth")) 
				{ } // skip custom fields on noncustom query
				else
				{
					$css[$csspart]["responsive"][$query][$css_stat] = $value; 
				}
				
			}
		}

		return $css;
	}
	

	public function save_fields($data, $post)
	{
		//echo "<PRE>"; print_R($post); echo "</PRE>"; 
		//$data = parent::save_fields($data, $post);
		
		$queries = (isset($post["media_query"])) ? $post["media_query"] : null; 
		$media_queries = array(); 
		
		if (is_null($queries))
			return $data; 
	
		foreach($queries as $i => $query)
		{
			if ($query != '')
			{
				$media_queries[$query] = array(); 
			
				// collect the other fields. 
				foreach($this->multi_fields as $field => $values)
				{
					echo "$field :: $i <br />"; 
					if (isset($post[$field][$i])) 
					{
						$media_queries[$query][$field] = $post[$field][$i]; 
					}
				}
			}
		}
		
		$data[$this->blockname]["media_query"] = $media_queries;
		
		$data[$this->blockname]["auto_responsive"] = (isset($post["auto_responsive"])) ? $post["auto_responsive"] : 0; 
		
		return $data;
		
	}

	
	public function admin_fields()
	{
		$data = $this->data[$this->blockname];
		
		$media_names =  maxButtonsUtils::get_media_query(1); // nicenames
		$media_desc = maxButtonsUtils::get_media_query(3);
		$units = array("px" => __("px","maxbuttons"),
					   "%" => __("%","maxbuttons")
					  );
		$container_floats = array(
							"" => "",
							"none" => __("None","maxbuttons"), 
							"left" => __("Left","maxbuttons"), 
							"right" => __("Right","maxbuttons"),
						);
	
		foreach($this->fields as $field => $options)
		{		
 	 	    $default = (isset($options["default"])) ? $options["default"] : ''; 
			$$field = (isset($data[$field])) ? $data[$field] : $default;
			${$field  . "_default"} = $default; 
		}
		

?>
			<div class="option-container">
				<div class="title"><?php _e('Responsive Settings', 'maxbuttons') ?></div>
				<div class="inside">
				
					<div class="option-design"> 
						<div class="label"><?php _e("Auto Responsive", 'maxbuttons') ?> <?php _e("(Experimental)","maxbuttons") ?></div>
						<div class="input"> 
							<input type='checkbox' name='auto_responsive' value='1' <?php checked(1, $auto_responsive); ?> >
						</div>
											<div class="clear"></div>
					</div>
 
					
				<div class='option-design media_queries_options'>
					<?php
					
					foreach($media_query as $item => $fields) 
					{
						
						?>
						<div class='media_query'> 
							<span class='removebutton'><img src="<?php echo maxButtons::get_plugin_url() ?>/assets/icons/remove.png"></span>
							
							<input type="hidden" name="media_query[]" value="<?php echo $item ?>"> 
							<label class='title'><?php echo $media_names[$item] ?></label>
							<p class='description'><?php echo $media_desc[$item] ?></p>							
							<?php 
								if ($item == "custom") { $custom_class = ''; } else { $custom_class = 'hidden'; }
								
							?>
							<div class="custom" <?php echo $custom_class ?> > 
								<div class="label"><?php _e("Min width","maxbuttons") ?></div>								
								<div class="input"><input type="text" class="tiny" name="mq_custom_minwidth[]" value="<?php echo $fields["mq_custom_minwidth"] ?>" />px</div> 
								

								<div class="input max"><input type="text" class="tiny" name="mq_custom_maxwidth[]" value="<?php echo $fields["mq_custom_maxwidth"] ?>" />px</div> 
								<div class="label max"> <?php _e("Max width","maxbuttons"); ?></div>
								
							</div>	
						
							<div class='label'><?php _e("Button width", 'maxbuttons') ?></div>
							
							<div class='input'><input type='text' name="mq_button_width[]" value="<?php echo $fields["mq_button_width"] ?>" class='tiny'> <?php echo maxButtonsUtils::selectify("mq_button_width_unit[]",$units,$fields["mq_button_width_unit"]) ?></div>
							
							<div class='label'><?php _e("Container width", 'maxbuttons'); ?></div>
							
							<div class='input'><input type='text' name="mq_container_width[]" value="<?php echo $fields["mq_container_width"] ?>" class='tiny'> <?php echo maxButtonsUtils::selectify("mq_container_width_unit[]",$units,$fields["mq_container_width_unit"]); ?>
							</div>
							
							<div class='label'><?php _e("Container float", "maxbuttons"); ?></div>
							<div class="input"><?php echo maxButtonsUtils::selectify("mq_container_float[]",$container_floats, $fields["mq_container_float"]) ?></div>
								
						</div>
					<div class="clear"></div>
						<?php
					}	
					
					
					?>
					<div class="new_query_space"></div>
				
					<div class="option-design new-query">
						<div class="label"><?php _e('New Query', 'maxbuttons') ?></div>
						
 
						<div class="input">
							<?php echo maxButtonsUtils::selectify("new_query",$media_names,'' ); ?>
							<a class="button add_media_query"><?php _e("Add","maxbuttons") ?></a>
						</div>
						
 
						<div class="clear"></div>
					</div>
				 </div>		
			</div> <!-- inside --> 
		
			<div class='media_option_prot'>
				<div class='media_query'> 
							<span class='removebutton'><img src="<?php echo maxButtons::get_plugin_url() ?>/assets/icons/remove.png"></span>
							<input type="hidden" name="media_query[]" value=""> 
							<label class='title'></label>
							<p class='description'>Description here</p>
							
							<div class="custom"> 
								<div class="label"><?php _e("Min width","maxbuttons") ?></div>								
								<div class="input"><input type="text" class="tiny" name="mq_custom_minwidth[]" value="0" />px</div> 
								

								<div class="input max"><input type="text" class="tiny" name="mq_custom_maxwidth[]" value="0" />px</div> 
								<div class="label max"> <?php _e("Max width","maxbuttons"); ?></div>
								
							</div>	
														
							<div class='label'><?php _e("Button width", "maxbuttons") ?></div>
							<div class='input'><input type='text' name="mq_button_width[]" value="0" class='tiny'> <?php echo maxButtonsUtils::selectify("mq_button_width_unit[]",$units,""); ?></div>
							<div class='label'><?php _e("Container width", "maxbuttons"); ?></div>
							<div class='input'>
							<input type='text' name="mq_container_width[]" value="0" class='tiny'> <?php echo maxButtonsUtils::selectify("mq_container_width_unit[]",$units,""); ?>
							</div>

							<div class='label'><?php _e("Container float", "maxbuttons"); ?></div>
							<div class="input"><?php echo maxButtonsUtils::selectify("mq_container_float[]",$container_floats, "") ?></div>
														
				`</div>
				<div class="clear"></div>
			</div>
			<div id="media_desc">
			<?php foreach($media_desc as $key => $desc)
			{
				echo "<span id='$key'>$desc</span>";
			
			}
			?>
			</div>
			

		</div> <!-- container --> 
		
				

			
<?php	
	}

} // class

?>
