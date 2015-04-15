<?php
/* Datamodel and base functionality for a button 

*/
 
class maxButton
{
	protected $id = 0; 
	protected $name = '';  
	protected $status = ''; 
	protected $description = ''; 
	
	protected $button_loaded = false;

	protected $data = array(); 
	protected $blocks; //= array('basic', 'text' , 'border', 'color', 'gradient', 'container', 'advanced', 'responsive'); // Blocks

	protected $wpdb = ''; 
	
	protected $button_css = array('normal' => array() ,':hover' => array() ,':visited' => array(), "responsive" => array()); 
	protected $button_js = array(); 
	
	// output conditions
	protected $load_css = 'footer';  // [ footer, inline, external, element ] 
	protected $load_js  = 'footer'; 
	
	protected $cssParser = ''; 
	protected $parsed_css = ''; 
	/* Class constructor 
	   
	   Get als loads the various blocks of which a button is built up. Blocks can be added and removed using the mb-init-blocks filter
	*/
	function __construct()
	{

		global $wpdb; 
		$this->wpdb = $wpdb; 
		
		// the parser
		 $this->cssParser =  new maxCSSParser();

		
		// get all files from blocks map 
		
		// get all blocks via apply filters, do init. Init should not load anything big. 
		
		require_once("block.php"); 
		
		$block_paths = apply_filters('mb-block-paths',  array(maxButtons::get_plugin_path() . "blocks/") );
		 
		global $blockClass; // load requires only once

		if ($blockClass == '' || count($blockClass) == 0)
		{ 
		
			$newBlocks = array();
			
			foreach($block_paths as $block_path)
			{
				$dir_iterator = new RecursiveDirectoryIterator($block_path, FilesystemIterator::SKIP_DOTS);
				$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
		
				foreach ($iterator as $fileinfo)
				{
					$block = $fileinfo->getFilename();

					if (file_exists($block_path . $block))
					{
						 require_once( $block_path . $block);
					}
				}

			}
				ksort($blockOrder);
				foreach($blockOrder as $prio => $blockArray)
				{
					foreach($blockArray as $block)
					{
						if (isset($blockClass[$block]))
							$newBlocks[$block] = $blockClass[$block]; 
					
					}
				}
				$blockClass = $newBlocks;
				$this->loadBlockClasses($blockClass); 
		} 
		
		$this->blocks = array_keys($blockClass);

	}
	
	/* Makes overriding block features possible by subclass
	
	*/
	private function loadBlockClasses($classes)
	{
		// set blocks to the 'block name' that survived. 
		 
		
		foreach($classes as $block => $class)
		{
			$block = new $class(); 
 
		}
 
	}
	
	/* Simple function to retrieve loaded blocks */ 
	public function getDefinedBlocks() 
	{
		return $this->blocks;
	}
	
	/*  Get Data from Database and set variables
	
		You can pass either id or name to this function
	
		@return Boolean Returns false when no data was found using either ID or name
	*/
	function set($id = 0, $name = '')
	{
		
		global $wpdb;
		$this->clear();
		// check to see if the value passed is NOT numeric. If it is, use title, else assume numeric
		if($id == 0 && $name != '') {
			$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . maxButtonsUtils::get_buttons_table_name() . " WHERE name = '%s' and status ='publish'", trim($name) ), ARRAY_A);
			 
		} else {
			$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . maxButtonsUtils::get_buttons_table_name() . " WHERE id = %d and status ='publish'", $id), ARRAY_A);
		}
		
 
		
		if (count($row) == 0) 
		{
			return false; 		
		} 
	
		return $this->setupData($row);
		
	}
	/* Clear button settings
	*
	*  This function prevent that generated values from previous set actions will still be present.
	*/	
	function clear() 
	{
		unset($this->data);
		unset($this->button_css);
		$this->button_css = '';
		$this->data = array();
	}
		
	function setupData($data)
	{

		foreach($this->blocks as $block)
		{
			if (array_key_exists($block, $data))  // strangely isset doesn't work
			{
				$this->data[$block] = unserialize($data[$block]);
			}
			else 
			{
				// else block does not exist in this dataset - ignoring. 
				//exit("Fatal: Something wrong with provider on $block ");
			} 
		}
 
		$this->id = $data["id"];
		$this->data["id"] = $this->id; // needed for certain blocks, to be button aware. 
		$this->name = $data["name"]; 
		$this->status = $data["status"];
		$this->description = $this->data["basic"]["description"]; 
		
		do_action('mb-data-load', $this->data);
		return true;
		 
	}
	
	function get( ) 
	{
		return $this->data;
	 
	}
	
	function getID() 
	{
		return $this->id; 
	}
	function getName() 
	{
		return $this->name;
	}
	function getDescription()
	{
		return $this->description;
	}
	
	function getStatus()
	{
		return $this->status; 
	}
	
	function getParsedCSS() 
	{
		return $this->parsed_css; 
	}
	
	function getCSSArray()
	{

		return $this->button_css;
	}
	
	/* Get multiple buttons 
	
		Used for overview pages, retrieve buttons on basis of passed arguments. 
		
		@return array Array of found buttons with argument
	*/
	function getButtons($args = array())
	{
		$defaults = array(
			"status" => "publish", 
			"orderby" => "", 
			"order" => "asc"
		);
		$args = wp_parse_args($args, $defaults); 
		
		$sql = "SELECT id FROM " . maxButtonsUtils::get_buttons_table_name() . " WHERE status = '%s'"; 
		if ($args["orderby"] != '')
			$sql .=  " ORDER BY "  . $args["orderby"] . " " . $args["order"]; 
	 
		$sql = $this->wpdb->prepare($sql, $args["status"], ARRAY_A); 
		$buttons = $this->wpdb->get_results($sql, ARRAY_A);
		return $buttons;
	}

	/* Parse CSS from all the elements
	
	*/
	function parse_css($mode = "normal")
	{
		$css = $this->button_css; 
		$css = apply_filters('mb-css-blocks', $css, $mode); 

		$this->button_css = $css;

		$css = $this->cssParser->parse($this->button_css);
 
		$this->parsed_css = $css; 		
	}
	
	function parse_js($mode = "normal") 
	{
		$js = $this->button_js; 
		$js = 	apply_filters('mb-js-blocks', $js, $mode);
		$this->button_js = $js; 
	}
	
	function parse_button($mode = 'normal')
	{
		$domObj = new simple_html_dom();
		$domObj->load("<a  class=\"maxbutton-" . $this->id . " maxbutton\"></a>"); 
 
		$domObj = apply_filters('mb-parse-button', $domObj, $mode); 
 
		$domObj->load($domObj->save());
	
		$this->cssParser->loadDom($domObj);
		return $domObj; 
	}

	/* Display all data and html to allow users to edit button settings */ 
	public function admin_fields() 
	{
		do_action('mb-admin-fields' ); 
 
	}
	
 	/* Display the button */
	public function display($args = array() )
	{	
		$defaults = array(
			"preview" => false,
			"preview_part" => "normal",
			"echo" => true, 
			"load_css" => "footer", // control how css is loaded. 
		);
		
		$args = wp_parse_args($args, $defaults); 

	 	$this->load_css = $args["load_css"]; 
 
		if ($this->id == 0) // if button doesn't exists don't display unless in preview
		{
			if (! $args["preview"]) 
				return;
 
			$data = apply_filters("mb-save-fields", array(), array() ); // load defaults
			$data["id"] = 0;
			do_action('mb-data-load', $data);
		}
		
		$mode = (isset($args["preview"]) && $args["preview"] == true) ? "preview" : "normal"; 		
	
 
		//$css = array(); 
		//$js = array(); 
		$buttonAttrs = array(); // the actual buttons and it's components. 
		
		// create button
		$domObj = $this->parse_button($mode); 
 		
		$this->parse_css($mode); 
		$this->parse_js($mode); 
		


		if ($args["preview"] == true)  // mark it preview
		{
			$domObj->find('a',0)->class .= ' maxbutton-preview';
		}
		
		if ($args["preview"] == true && $args["preview_part"] != 'full')
		{
 
 	 		if ($args["preview_part"] != 'normal')
 	 		{
				$domObj->find('a',0)->class .= ' hover'; 	
				$domObj = $this->cssParser->outputInline($domObj,'hover');

			}
			else
			{
				$domObj->find('a',0)->class .= ' normal'; 
				$domObj = $this->cssParser->outputInline($domObj);
			}

		}
		elseif ($this->load_css == 'footer') 
		{
			$css = $this->display_css(false, true); 
			do_action('mb-footer-css',$this->id, $css); 
			
		} elseif ($this->load_css == 'inline') 
		{
			$this->display_css();
		}
		elseif ($this->load_css == 'element') // not possible to load both normal and hover to an element. 
		{
				$domObj->find('a',0)->class .= ' normal'; 
				$domObj = $this->cssParser->outputInline($domObj); 
				//$this->get_element_css($domObj, 'normal'); 
		}
			

 		$output = $domObj->save();
		
		$output = apply_filters('mb-before-button-output', $output); 
		if ($args["echo"])
			echo $output; 
		else
			return $output; 
		
	}	
	/* Function used to map field id's to display for Frontend Javascript
	
		This function bundles all defined fields into a json encoded variable. This is used for the frontend javascript functions in the 
		administrator area like colorpickers and real-time updating of the button preview
	*/
	public function display_field_map()
	{
		$map = array(); 
		$map = apply_filters("mb-field-map",$map); 
		

		echo "<script language='javascript'>"; 	
				echo "var buttonFieldMap = '" . json_encode($map) . "';" ;
		echo "</script>";
	
	}
	
	/* Write parsed CSS to output. 
 	   @param echo Default true. When true, outputs directly, otherwise returns output string 
 	   @param style_tag Default true. When true, outputs a html <style> tags around the output.
 	*/
	public function display_css($echo = true, $style_tag = true)
	{
		$output = ''; 
		
		if ($style_tag)
			$output .=  "<style type='text/css'>";
		
			$output .= $this->parsed_css;
			
		if ($style_tag)
			$output .= "</style>";
		
 
		if ($echo) echo $output; 
		else return $output; 
		
	}
	/* Function for display CSS on an element
		
		This function allows for inline styling on an element. The CSS should be parsed. Used for inline output of styling in the button 
		preview. 
	*/
	/*private function get_element_css($domObj, $preview_part = 'normal')
	{
 
 		$base = ".maxbutton-" . $this->id; 
 
 		$domObj->load($domObj->save());
		
		foreach($this->button_css as $part => $css)
		{
			
			$dompart = $base . $part; 
			if ($part == 'normal') $dompart = $base; 
			if (strpos($part,':hover') !== false) $dompart = "a.hover" . str_replace(':hover','',$part);
			if (strpos($part,':visited') !== false) continue; 
			
			$element = $domObj->find($dompart, 0);
			if ( is_object($element)) echo ""; 
			else continue; 
			
			$style = ''; 
			
 			foreach ($css as $key => $val) 
 			{
 				$style .= $key . ":" . $val . ";";
 			}
 			$element->style .= $style;
 			
		}
 
		return $domObj;
	} */

	/* Makes a copy of the current buttons. 
	
	   The button to be copied -must- be loaded and set	
	*/
	function copy() 
	{		
		$this->id = 0; 
		$data = $this->data;
		$data["name"] = $this->name;
		
		return $this->update($data);		
	}
	/*  Change the publication status of the button.
	
	*/
	function setStatus($status = "publish") 
	{
		$data = $this->data; 
		$data["status"] = $status; 
		return $this->update($data); 
			
	}	
	/* Remove the button from database */ 
	public function delete($id) 
	{
		$this->wpdb->query($this->wpdb->prepare("DELETE FROM " . maxButtonsUtils::get_buttons_table_name() . " WHERE id = %d", $id));
	}
		
	/* Save changes to the button 
		
		Updates or saves the button. Existing buttons must load their data and be set -first- or lose all not-passed data. 
		
	   @param post Post data in field - value format (flat $_POST array)
	   @param boolean savedb if false do not save to database 
	*/
	public function save($post, $savedb = true)
	{

		$data = apply_filters('mb-save-fields',$this->data, $post); 
		if (! $savedb ) return $data; 
		return $this->update($data); // save to db. 
	
	}
	
	public function update($data) 
	{	 
		$return = false;  
		
		$fields = array(); 
		foreach($this->blocks as $block)
		{
			$blockData = $data[$block]; 
			$fields[$block] = serialize($blockData);
		}	
 		if (isset($data["name"])) {  // other fields. 
 			$fields["name"] = $data["name"]; 
 		}
 		if (isset($data["status"])) {
 			$fields["status"] = $data["status"]; 
 		}

		$where = array('id' => $this->id );
		if ($this->id > 0) 
		{
			$where = array('id' => $this->id);
			$where_format = array('%d');
			$this->wpdb->update(maxButtonsUtils::get_buttons_table_name(), $fields, $where, null, $where_format);
			$return = true;
		}
		else
		{
			$this->wpdb->insert(maxButtonsUtils::get_buttons_table_name(), $fields);
			$id = $this->wpdb->insert_id;
 			$return = $id; 
		
		}
 
 		return $return;
	}
	

	
	public function shortcode($atts)
	{  		 			
		extract(shortcode_atts(array(
				'id' => '',
				'name' => '',
				'text' => '',
				'url' => '',
				'window' => '',
				'nofollow' => '',
			//	'externalcss' => '',
			//	'externalcsspreview' => '',		// Only used in maxbuttons-button-css.php
			//	'ignorecontainer' => '',		// Internal use only on button list pages and the TinyMCE dialog
				'exclude' => ''
			), $atts));	
			
		$button_id = $id; 
		$button_name = $name;
 
		if ($button_id > 0) 
			$result = $this->set($button_id); 
		elseif ($button_name != '') 
			$result = $this->set(0, $button_name); 
		else return; // no button

		if (! $result) 
			return; // shortcode doesn't exist
			
		//if (isset($button)) {
			// If we're not in the admin and the button is in the trash, just return nothing
			if (!is_admin() && $this->status == 'trash') {
				return '';
			}
		// Check to handle excludes
		if ("{$exclude}" != '') {
			global $post;
			
			// Don't render the button if excluded from the current post/page
			$exclude = explode(',', "{$exclude}");
			if (in_array($post->ID, $exclude)) {
				return '';
			}
		}
		
		// Override shortcode options comparing to default button data.
		$overrides = false; 
		if ($text != '') 
		{ 
			$this->data["text"]["text"] = $text; 
			$overrides = true;
		}  
		if ($url != '') 
		{
			$this->data["basic"]["url"]  = $url; 
			$overrides = true;
		}
		if ($window != '' && $window =='new') 
		{
			$this->data["basic"]["new_window"] = 1;  
			$overrides = true;
		}
		if ($nofollow != '' && $nofollow == 'true') 
		{
			$this->data["basic"]["nofollow"] = 1; 
			$overrides = true;
		}
		
		if ($overrides)
		{
			do_action('mb-data-load', $this->data);
		}
		// if there are no reasons not to display; display
		$output = $this->display(array("echo" => false));
	 
		return $output;
		
	}

}
?>
