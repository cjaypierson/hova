(function($){
	$(document).ready(function(){
		var DEBUG=$(document).wpiDebug();		
		var TOOLS=$(document).wpiTools();
		var CSS=$(document).wpiCss();
		var UI=$(document).wpiUI();
		var CREATEUI=$(document).wpiCreateUI();
		DEBUG.setState("Script");
		
		var $wpi_export_tabs_count=0;
		var $wpi_db_theme_count=0;
		var $wpi_db_icons_count=0;
		var $wpi_db_icons_arr=[];
		
		var box=$("#wpi_designer_button_box");
		var preview_button=$("#wpi_preview .wpi_designer_button");	
		var restore=box.find("#wpi_restore");
		var _export=box.find("#wpi_export");
		
		
		var styles=box.find("#wpi_styles");
		var icons=box.find("#wpi_icons");
		var colors=$("#wpi_colors");
		
		var icon_text=box.find("#wpi_icon_text");
		var only_text=box.find("#wpi_only_text");
		var only_icon=box.find("#wpi_only_icon");

		//styles page
		var sty_el=$( ".wpi_des_but_sty" );
		var sty_ids=["shape","padding","shadow","text_shadow", "border_sides", "glow_size", "glow_color", "texture", "text_size", "font", "font_weight", "border_width", "border_style", "display", "text_color", "background_color", "border_color", "text_color_h", "background_color_h", "border_color_h", "text_color_a", "background_color_a", "border_color_a"];
		var sty=get_elements(sty_ids);
		var $sty_defaults=get_defaults(sty_ids);
		
		
		//buttons page
		var but_el=$( ".wpi_des_but" );
		var but_ids=["icon","text","style_id"];
		var but=get_elements(but_ids);
		
		var but_map={
			defaults:{},
			init:function(){
				this.defaults=get_defaults(but_ids);
				this.prepare_preview();	
				export_fn(this.defaults);				
				but['icon'].change({fn:this},function(event){ event.data.fn.icon();});
				but['style_id'].change({fn:this},function(event){ event.data.fn.style_id();});
				but['text'].keyup({fn:this},function(event){ event.data.fn.text();});
			},
			prepare_preview:function(){
				this.icon();	
				this.style_id();	
				this.text();	
			},
			apply_preset:function(style){
				var id=$(style).find(".wpi_id").text();
				var no_text_class="";
				but['style_id'].val(id);
				var icon_class="wpi_icon wpi_icon_"+but['icon'].val();
				var classes=$(style).attr("class");	
				if(but['text'].val()==""){no_text_class="wpi_no_text";}
				preview_button.attr("class",classes+" "+icon_class+" "+no_text_class);
			},
			icon:function(){
				remove_class("icon");
				preview_button.addClass("wpi_icon wpi_icon_"+but['icon'].val());				
			},
			text:function(){
				preview_button_text(but['text'].val());
			},
			style_id:function(){
				var no_text_class="";
				var icon_class="wpi_icon wpi_icon_"+but['icon'].val();
				var element="#wpi_db_sty_"+but['style_id'].val();
				var classes=$(element).attr("class");	
				if(but['text'].val()==""){no_text_class="wpi_no_text";}
				preview_button.attr("class",classes+" "+icon_class+" "+no_text_class);				
			},	
		};	
		
		//slides page
		var sli_el=$( ".wpi_des_but_sli" );
		var sli_ids=["slide_heading","slide_heading_2","slide_heading_3", "slide_heading_font", "slide_heading_size", "slide_heading_font_weight", "slide_heading_line_height", "slide_heading_letter_spacing", "slide_heading_color", "slide_heading_margin_top","slide_heading_margin_left", "slide_heading_margin_right",  "slide_heading_padding", "slide_heading_border_width", "slide_heading_border_color","slide_heading_background_color", "slide_heading_shadow_distance", "slide_text", "slide_text_font", "slide_text_size", "slide_text_font_weight", "slide_text_color", "slide_text_margin_top","slide_text_margin_left","slide_text_margin_right", "button_text","button_margin_top", "button_margin_bottom","slide_footer_text","slide_footer_padding","slide_footer_text_size", "background_color", "background_image",  "background_custom_image", "background_repeat_image", "background_image_opacity","background_image_blur",  "icon", "style_id", "frame_height"];
		var sli=get_elements(sli_ids);		
		var sli_div_ids=["wpi_slide","wpi_slide_heading","wpi_slide_text","wpi_slide_image","wpi_slide_button","wpi_slide_footer"];
		var sli_div=get_elements(sli_div_ids);
		
		var sli_map={			
			defaults:{},
			input_map:{},
			classes:{},
			init:function(){
				this.defaults=get_defaults(sli_ids);
				this.set_css();
				this.initialPreviewSet=0;
				this.headingTicker=sli_div["wpi_slide_heading"].wpiTicker({store:true});
				this.prepare_preview();	
				this.map();					
				export_fn(this.defaults);
				sli['slide_heading'].keyup({fn:this},function(event){ event.data.fn.heading();});
				sli['slide_heading_2'].keyup({fn:this},function(event){ event.data.fn.heading_2();});
				sli['slide_heading_3'].keyup({fn:this},function(event){ event.data.fn.heading_3();});
				sli['slide_heading_font'].change({fn:this},function(event){	event.data.fn.heading_font();});	
				sli['slide_heading_size'].change({fn:this},function(event){	event.data.fn.heading_size();});
				sli['slide_heading_font_weight'].change({fn:this},function(event){	event.data.fn.heading_font_weight();});
				sli['slide_heading_line_height'].change({fn:this},function(event){	event.data.fn.heading_line_height();});
				sli['slide_heading_letter_spacing'].change({fn:this},function(event){	event.data.fn.heading_letter_spacing();});
				sli['slide_heading_color'].change({fn:this},function(event){ event.data.fn.heading_color();});
				sli['slide_heading_background_color'].change({fn:this},function(event){ event.data.fn.heading_background_color();});
				sli['slide_heading_margin_top'].change({fn:this},function(event){ event.data.fn.heading_margin_top();});
				sli['slide_heading_margin_left'].change({fn:this},function(event){ event.data.fn.heading_margin_left();});
				sli['slide_heading_margin_right'].change({fn:this},function(event){ event.data.fn.heading_margin_right();});
				sli['slide_heading_padding'].change({fn:this},function(event){ event.data.fn.heading_padding();});
				sli['slide_heading_border_width'].change({fn:this},function(event){ event.data.fn.heading_border_width();});
				sli['slide_heading_border_color'].keyup({fn:this},function(event){ event.data.fn.heading_border_color();});
				sli['slide_heading_shadow_distance'].change({fn:this},function(event){ event.data.fn.heading_shadow_distance();});
				sli['slide_text'].keyup({fn:this},function(event){ event.data.fn.text();});
				sli['slide_text_font'].change({fn:this},function(event){ event.data.fn.text_font();});	
				sli['slide_text_size'].change({fn:this},function(event){ event.data.fn.text_size();});
				sli['slide_text_font_weight'].change({fn:this},function(event){ event.data.fn.text_font_weight();});
				sli['slide_text_color'].change({fn:this},function(event){ event.data.fn.text_color();});
				sli['slide_text_margin_top'].change({fn:this},function(event){ event.data.fn.text_margin_top();});
				sli['slide_text_margin_left'].change({fn:this},function(event){ event.data.fn.text_margin_left();});
				sli['slide_text_margin_right'].change({fn:this},function(event){ event.data.fn.text_margin_right();});
				sli['icon'].change({fn:this},function(event){ event.data.fn.icon();});	
				sli['style_id'].change({fn:this},function(event){ event.data.fn.style_id();});
				sli['button_text'].keyup({fn:this},function(event){	event.data.fn.button_text();});	
				sli['button_margin_top'].change({fn:this},function(event){ event.data.fn.button_margin_top();});
				sli['button_margin_bottom'].change({fn:this},function(event){ event.data.fn.button_margin_bottom();});
				sli['slide_footer_text'].change({fn:this},function(event){ event.data.fn.footer_text();});
				sli['slide_footer_text_size'].change({fn:this},function(event){ event.data.fn.footer_text_size();});
				sli['slide_footer_padding'].change({fn:this},function(event){ event.data.fn.footer_padding();});
				sli['background_color'].change({fn:this},function(event){ event.data.fn.background_color();});
				sli['background_image'].change({fn:this},function(event){ event.data.fn.background_image();});
				sli['background_custom_image'].change({fn:this},function(event){ event.data.fn.background_image();});
				sli['background_repeat_image'].change({fn:this},function(event){ event.data.fn.background_repeat_image();});
				sli['background_image_opacity'].change({fn:this},function(event){ event.data.fn.background_image_opacity();});
				sli['background_image_blur'].change({fn:this},function(event){ event.data.fn.background_image_blur();});
				sli['frame_height'].change({fn:this},function(event){ event.data.fn.frame_height();});
				
				var input_map=this.input_map;
				var self=this;				
				function prepare_inputs(args){
					self.prepare_inputs(args);					
				}
				function a2b_data(){					
					return self.a2b_data();
				}
				sli_div["wpi_slide"].wpiProperties({input_map: input_map, callback : prepare_inputs, a2b_callback : a2b_data }); //global function
				
				//$(document).wpiTicker();
			},
			set_css:function(){
				this.classes={};
				var self=this;
				$.each(sli_div_ids,function(key,val){
					self.classes[val]={};
					DEBUG.setState(key+":"+val);					
					self.classes[val]['element']=sli_div[val];
					self.classes[val]['styles']={};
				});
			},
			build_css:function(args){
				this.check_css(args);
				var element=args['element'];
				var style=args['style'];
				var value=args['val'];
				this.classes[element]['styles'][style]=value;
				CSS.buildCss(this.classes[element]);				
			},
			check_css:function(args){				
				/*if(args['style']=="text-shadow-distance"){
					this.classes[args['element']]['styles']['text-shadow-x']=args['val'];
					this.classes[args['element']]['styles']['text-shadow-y']=args['val'];
					this.classes[args['element']]['styles']['text-shadow-blur']=args['val'];
				};	*/				
			},
			prepare_preview:function(){
				this.update_headingTicker();
				this.heading();
				this.heading_2();
				this.heading_3();
				this.heading_font();
				this.heading_size();
				this.heading_font_weight();
				this.heading_line_height();
				this.heading_letter_spacing();
				this.heading_color();
				this.heading_background_color();
				this.heading_margin_top();
				this.heading_margin_left();
				this.heading_margin_right();
				this.heading_padding();
				this.heading_border_width();
				this.heading_border_color();
				this.heading_shadow_distance();
				this.text();
				this.text_font();
				this.text_size();
				this.text_font_weight();
				this.text_color();
				this.text_margin_top();
				this.text_margin_left();
				this.text_margin_right();
				this.icon();
				this.style_id();
				this.button_text();
				this.button_margin_top();				
				this.button_margin_bottom();
				this.footer_text();
				this.footer_padding();
				this.footer_text_size();
				this.background_image();
				this.background_repeat_image();
				this.background_image_opacity();
				this.background_image_blur();
				this.background_color();
				this.frame_height();				
				if(this.initialPreviewSet==0){
					this.initialPreviewSet=1;
					this.headingTicker.init();
					var parentPadding=sli['slide_heading_padding'].val();
					var animate="stop";
					if(sli['slide_heading_2'].val()!=""|| sli['slide_heading_3'].val()!=""){
						animate="play";
					}
					this.headingTicker.update({"animate":animate, "parentPadding": parentPadding});
				}
			},
			map:function(){	
				this.input_map={
					'wpi_slide_heading' : {
						'text': {							
							el: sli['slide_heading'], val:"",
						},
						'font-family': {
							el: sli['slide_heading_font'], val:"",
						},
						'color':{
							el: sli['slide_heading_color'], val:"",
						},
						'shadow' : {
							el: sli['slide_heading_shadow_distance'], val:"",
						},
						'font-size' : {
							el: sli['slide_heading_size'], val:"",
						},
						'font-weight' : {
							el: sli['slide_heading_font_weight'], val:"",
						},
						'letter-spacing' : {
							el: sli['slide_heading_letter_spacing'], val:"",
						},	
						'margin-top' : {
							el: sli['slide_heading_margin_top'], val:"",
						},
						'margin-left' : {
							el: sli['slide_heading_margin_left'], val:"",
						},
						'margin-right' : {
							el: sli['slide_heading_margin_right'], val:"",
						},
						'padding' : {
							el: sli['slide_heading_padding'], val:"",
						},
						'border-color' : {
							el: sli['slide_heading_border_color'], val:"",
						},
						'border-width' : {
							el: sli['slide_heading_border_width'], val:"",
						},
						'shadow-distance' : {
							el: sli['slide_heading_shadow_distance'], val:"",
						},
					},
					'wpi_slide_text' : {
						'text' : {
							el: sli['slide_text'], val:"",
						},
						'font-family' : {
							el: sli['slide_text_font'], val:"",
						},
						'font-size' : {
							el: sli['slide_text_size'], val:"",
						},
						'font-weight' : {
							el: sli['slide_text_font_weight'], val:"",
						},
						'margin-top' : {
							el: sli['slide_text_margin_top'], val:"",
						},	
						'margin-left' : {
							el: sli['slide_text_margin_left'], val:"",
						},
						'margin-right' : {
							el: sli['slide_text_margin_right'], val:"",
						},
						'color' : {
							el: sli['slide_text_color'], val:"",
						},
					},
					'wpi_slide_button' : {
						'text': {							
							el: sli['button_text'], val:"",
						},
						'margin-top' : {
							el: sli['button_margin_top'], val:"",
						},
						'margin-bottom' : {
							el: sli['button_margin_bottom'], val:"",
						},
					},
					'wpi_slide_footer' : {			
						'font-size' : {
							el: sli['slide_footer_text_size'], val:"",
						},
						'padding' : {
							el: sli['slide_footer_padding'], val:"",
						},
					},
				};					
			},
			test:function(){
				alert(1);
			},
			prepare_inputs:function(args){				
				var id=$(args['element']).attr("id");
				DEBUG.setState("external"+args['element'].data(args['property']));
				var property=args['property'];
				var value=args['value'];
				this.input_map[id][property]['el'].val(value);
				this.input_map[id][property]['el'].trigger("change");
				this.prepare_preview();	
			},	
			a2b_data:function(){	
				var self=this;
				$.each(self.input_map, function(key,val){
					$.each(val, function(k,v){
						self.input_map[key][k]['val'] = self.input_map[key][k]['el'].val();
					});						
				});				
				return this.input_map;
			},	
			update_headingTicker:function(){
				if(this.initialPreviewSet!=0){	
					var parentPadding=sli['slide_heading_padding'].val();
					var animate="stop";
					if(sli['slide_heading_2'].val()!=""|| sli['slide_heading_3'].val()!=""){
						animate="play";
					}
					this.headingTicker.update({"animate":animate, "parentPadding": parentPadding});
				}				
			},
			heading:function(){
				sli_div['wpi_slide_heading'].find(".wpi_heading_1").children().html(sli['slide_heading'].val());
				this.update_headingTicker();
			},
			heading_2:function(){
				sli_div['wpi_slide_heading'].find(".wpi_heading_2").children().html(sli['slide_heading_2'].val());	
				this.update_headingTicker();
			},
			heading_3:function(){
				sli_div['wpi_slide_heading'].find(".wpi_heading_3").children().html(sli['slide_heading_3'].val());	
				this.update_headingTicker();				
			},			
			heading_font:function(){
				sli_div['wpi_slide_heading'].css({"font-family":sli['slide_heading_font'].val()});
				this.update_headingTicker();
			},
			heading_size:function(){
				sli_div['wpi_slide_heading'].css({"font-size":sli['slide_heading_size'].val()});
				this.update_headingTicker();
			},
			heading_font_weight:function(){
				sli_div['wpi_slide_heading'].css({"font-weight":sli['slide_heading_font_weight'].val()});	
				this.update_headingTicker();
			},
			heading_line_height:function(){
				sli_div['wpi_slide_heading'].css({"line-height":sli['slide_heading_line_height'].val()});
				this.update_headingTicker();
			},
			heading_letter_spacing:function(){
				sli_div['wpi_slide_heading'].css({"letter-spacing":sli['slide_heading_letter_spacing'].val()});	
				this.update_headingTicker();
			},
			heading_padding:function(){
				sli_div['wpi_slide_heading'].css({"padding":sli['slide_heading_padding'].val()});	
				this.update_headingTicker();
			},
			heading_color:function(){
				var val= TOOLS.setColor(sli['slide_heading_color'].val());
				sli['slide_heading_color'].val(val);
				sli_div['wpi_slide_heading'].css({"color":val});				
			},
			heading_margin_top:function(){
				sli_div['wpi_slide_heading'].css({"margin-top":sli['slide_heading_margin_top'].val()});				
			},
			heading_margin_left:function(){
				sli_div['wpi_slide_heading'].css({"margin-left":sli['slide_heading_margin_left'].val()});				
			},
			heading_margin_right:function(){
				sli_div['wpi_slide_heading'].css({"margin-right":sli['slide_heading_margin_right'].val()});				
			},			
			heading_border_width:function(){
				sli_div['wpi_slide_heading'].css({"border-width":sli['slide_heading_border_width'].val()});				
			},
			heading_border_color:function(){
				sli_div['wpi_slide_heading'].css({"border-color":sli['slide_heading_border_color'].val()});				
			},	
			heading_background_color:function(){
				sli_div['wpi_slide_heading'].css({"background-color":sli['slide_heading_background_color'].val()});				
			},
			heading_shadow_distance:function(){				
				this.build_css({ element:'wpi_slide_heading', style:"text-shadow-distance", val: sli['slide_heading_shadow_distance'].val()});
			},			
			text:function(){
				sli_div['wpi_slide_text'].html(sli['slide_text'].val());				
			},
			text_font:function(){
				sli_div['wpi_slide_text'].css({"font-family":sli['slide_text_font'].val()});				
			},
			text_size:function(){
				sli_div['wpi_slide_text'].css({"font-size":sli['slide_text_size'].val()});				
			},
			text_font_weight:function(){
				sli_div['wpi_slide_text'].css({"font-weight":sli['slide_text_font_weight'].val()});				
			},
			text_color:function(){
				var val= TOOLS.setColor(sli['slide_text_color'].val());
				sli['slide_text_color'].val(val);
				sli_div['wpi_slide_text'].css({"color":val});				
			},
			text_margin_top:function(){
				sli_div['wpi_slide_text'].css({"margin-top":sli['slide_text_margin_top'].val()});				
			},
			text_margin_left:function(){
				sli_div['wpi_slide_text'].css({"margin-left":sli['slide_text_margin_left'].val()});				
			},
			text_margin_right:function(){
				sli_div['wpi_slide_text'].css({"margin-right":sli['slide_text_margin_right'].val()});				
			},
			icon:function(){
				remove_class("icon");
				preview_button.addClass("wpi_icon wpi_icon_"+sli['icon'].val());			
			},			
			style_id:function(){
				var no_text_class="";
				var icon_class="";
				if(sli['icon'].val()) icon_class="wpi_icon wpi_icon_"+sli['icon'].val();
				var element="#wpi_db_sty_"+sli['style_id'].val();
				var classes=$(element).attr("class");	
				if(sli['button_text'].val()==""){no_text_class="wpi_no_text";}
				preview_button.attr("class",classes+" "+icon_class+" "+no_text_class);				
			},	
			button_text:function(){					
				var val=sli['button_text'].val();				
				preview_button_text(val);
			},
			button_margin_top:function(){
				sli_div['wpi_slide_button'].css({"margin-top":sli['button_margin_top'].val()});				
			},
			button_margin_bottom:function(){
				sli_div['wpi_slide_button'].css({"margin-bottom":sli['button_margin_bottom'].val()});				
			},
			footer_text:function(){
				if(sli['slide_footer_text'].val()!=""){
					sli_div['wpi_slide_footer'].removeClass("wpi_none");
					sli_div['wpi_slide_footer'].html(sli['slide_footer_text'].val());
				}else{
					sli_div['wpi_slide_footer'].addClass("wpi_none");
				}
			},
			footer_padding:function(){
				sli_div['wpi_slide_footer'].css({"padding":sli['slide_footer_padding'].val()});				
			},
			footer_text_size:function(){
				sli_div['wpi_slide_footer'].css({"font-size":sli['slide_footer_text_size'].val()});				
			},
			background_image:function(){
				if(sli['background_custom_image'].val()!=""){
					var url="url("+sli['background_custom_image'].val()+")";					
				}else{
					var url=this.no_image(sli['background_image'].val());
				}				
				sli_div['wpi_slide_image'].css({"background-image":url});
			},
			background_repeat_image:function(){
				var url=this.no_image(sli['background_repeat_image'].val());
				sli_div['wpi_slide'].css({"background-image":url});
			},
			background_image_opacity:function(){
				var val=(sli['background_image_opacity'].val()/100);				
				sli_div['wpi_slide_image'].css({"opacity":val});
			},
			background_image_blur:function(){
				this.build_css({ element:'wpi_slide_image', style:"blur", val: sli['background_image_blur'].val()});
			},
			background_color:function(){
				var val= TOOLS.setColor(sli['background_color'].val());
				sli['background_color'].val(val);
				sli_div['wpi_slide'].css({"background-color":val});		
			},
			frame_height:function(){
				sli_div['wpi_slide'].css({"min-height":sli['frame_height'].val()});

			},
			no_image:function(val){				
				var url="";
				if(val=="no" || val==""){
					url="none";		
				}else{
					url="url("+(WPiURLS.WPIDB_URL)+"images/"+val+")";		
				}	
				return url;
			},
			apply_sli_preset:function(args){
				set_inputs(args,this.defaults);
				this.prepare_preview();
			},
			apply_preset:function(style){
				var id=$(style).find(".wpi_id").text();
				var no_text_class="";
				sli['style_id'].val(id);
				var icon_class="wpi_icon wpi_icon_"+sli['icon'].val();
				var classes=$(style).attr("class");	
				if(sli['button_text'].val()==""){no_text_class="wpi_no_text";}
				preview_button.attr("class",classes+" "+icon_class+" "+no_text_class);
			},
		};	
			
			
		function get_elements(ids){
			var elements={};
			$.each(ids,function(key,val){
				elements[val]=$("#"+val);			
			});
			return elements;
		};
		function get_defaults(ids){
			var defaults={};
			$.each(ids,function(key,val){
				defaults[val]="";			
			});
			return defaults;
		};
		function current_args(defaults){
			var fields={};
			$.each(defaults, function(key, val) {				
				fields[key]=$("#"+key).val();
			});			
			return fields;
		};
		function set_inputs(args,defaults){			
			var defaults=current_args(defaults);			
			var args=$.extend(defaults, args );
			$.each(args, function(key, val) { 
				$("#"+key).val(val);
			});			
		};	
		
		

		icon_text.click(function(){
			preview_button.removeClass("wpi_no_text wpi_icon_no");
			preview_button.addClass("wpi_icon wpi_icon_videocamera");					 
		});
		only_text.click(function(){
			preview_button.removeClass("wpi_no_text wpi_icon_videocamera");
			preview_button.addClass("wpi_icon wpi_icon_no");					 
		});
		only_icon.click(function(){
			preview_button.removeClass("wpi_icon_no");
			preview_button.addClass("wpi_icon wpi_icon_videocamera wpi_no_text");						 
		});
		
		
		var $classes={};	
		var $normal={};
		var $hover={};
		var $active={};		
		var $states=[
			{"element": sty['text_color'], "prop": 'color', "state": 'normal','type':'color' },
			{"element": sty['background_color'], "prop": 'background-color', "state": 'normal' ,'type':'color'},
			{"element": sty['border_color'], "prop": 'border-color', "state": 'normal','type':'color'},
			{"element": sty['text_color_h'], "prop": 'color', "type": 'hover', "state": 'hover','type':'color'},
			{"element": sty['background_color_h'], "prop": 'background-color', "state": 'hover','type':'color'},
			{"element": sty['border_color_h'], "prop": 'border-color', "state": 'hover','type':'color'},
			{"element": sty['text_color_a'], "prop": 'color', "type": 'hover', "state": 'active','type':'color'},
			{"element": sty['background_color_a'], "prop": 'background-color', "state": 'active','type':'color'},
			{"element": sty['border_color_a'], "prop": 'border-color', "state": 'active','type':'color'}
		];
				
		function create_remove_class(base, arr){			
			var remove_class="";
			$.each(arr,function(key,val){
				remove_class+=base+val+" ";		
			});				
			return remove_class;
		}
		function remove_all_class(types){
			$.each(types,function(key,val){
				remove_class(val);								  
			});
		}
		
		function remove_class(type){			
			var shapes=create_remove_class("wpi_",["rounded", "5px","7px","10px","15px","rectangle",]);
			var padding=create_remove_class("wpi_padding_",["3px", "5px","7px","10px","15px","20px","20px","30px","40px","50px",]);
			var border_sides=create_remove_class("wpi_border_",["all", "left","top","right","bottom",]);
			var shadows=create_remove_class("wpi_shadow_",["no", "1px","2px","3px","4px","5px","6px","7px","8px","9px","10px","inset_wb","inset_wt","inset_bb","inset_bt",]);
			var texture=create_remove_class("wpi_texture_",["no", "noise","checks","checks_small",]);
			var text_shadows="shadow "+create_remove_class("wpi_text_shadow_",["no", "1px","2px","3px","4px","5px",]);
			var icon="wpi_icon "+create_remove_class("wpi_icon_",$wpi_db_icons_arr);
			if(type=='shape'){
				preview_button.removeClass(shapes);
			}else if(type=="shadow"){
				preview_button.removeClass(shadows);
			}else if(type=="text_shadow"){
				preview_button.removeClass(text_shadows);
			}else if(type=="border_sides"){
				preview_button.removeClass(border_sides);
			}else if(type=="padding"){
				preview_button.removeClass(padding);
			}else if(type=="texture"){
				preview_button.removeClass(texture);
			}else if(type=="icon"){
				preview_button.removeClass(icon);
			}
		}		
		
		//Events
		
		
		preview_button.click(function(event){			
			event.preventDefault();
		});
		$(".wpi_designer_button.wpi_display").click(function(event){			
			event.preventDefault();
		});
		
		if ( sty_el.length ) {	//style admin
			export_fn($sty_defaults);
			preview_button.mouseover(function(){
				$(this).css($hover);			
			}).mouseout(function(){
				$(this).css($normal);		
			}).mousedown(function(){
				$(this).css($active);		
			}).mouseup(function(){
				$(this).css($hover);		
			});		
			restore.click(function(event){			
				event.preventDefault();			
				set_style("default", $saved_args );			
			});
			
			sty['shape'].change(function(){
				remove_class("shape");
				preview_button.addClass("wpi_"+$( this ).val());
			});
			sty['padding'].change(function(){
				remove_class("padding");
				preview_button.addClass("wpi_padding_"+$( this ).val());
			});
			sty['shadow'].change(function(){
				remove_class("shadow");
				preview_button.addClass("shadow wpi_shadow_"+$( this ).val());
			});
			sty['glow_size'].change(function(){
									  
				var glow= get_glow($( this ).val(), sty['glow_color'].val());			
				preview_button.css({"box-shadow":glow});
			});
			sty['glow_color'].change(function(){			
				var val= TOOLS.setColor($( this ).val());
				$( this ).val(val);
				
				var glow= get_glow(sty['glow_size'].val(), val);			
				preview_button.css({"box-shadow":glow});
			});
			sty['texture'].change(function(){
				remove_class("texture");
				preview_button.addClass("wpi_texture_"+$( this ).val());
			});			
			sty['text_shadow'].change(function(){
				remove_class("text_shadow");
				preview_button.addClass("wpi_text_shadow_"+$( this ).val());
			});
			sty['border_sides'].change(function(){
				remove_class("border_sides");
				preview_button.addClass("wpi_border_"+$( this ).val());
			});		
			sty['border_width'].change(function(){			
				preview_button.css({"border-width":$( this ).val()});
			});		
			sty['display'].change(function(){			
				preview_button.css({"display":$( this ).val()});
			});
			sty['border_style'].change(function(){			
				preview_button.css({"border-style":$( this ).val()});
			});
			sty['text_size'].change(function(){
				preview_button.css({"font-size":$( this ).val()});			
			});	
			sty['font'].change(function(){
				preview_button.css({"font-family":$( this ).val()});			
			});
			sty['font_weight'].change(function(){
				preview_button.css({"font-weight":$( this ).val()});			
			});	
		}else if ( but_el.length) {			
			but_map.init();	
		}else if ( sli_el.length) {
			sli_map.init();			
		}
		
		function preview_button_text(val){
			if(val==""){
				preview_button.addClass('wpi_no_text');	
			}else{
				preview_button.removeClass('wpi_no_text');		
			}
			preview_button.find('.wpi_text').html(val);	
		}
		
		
		if ( box.length ) {	
			var tabs=$(".wpi_visual_holder .wpi_tabs").set_tabs();	
			$(".wpiHolder").wpiHolder({back:".wpi_back"});
			$(".wpiAccordion").wpiAccordion();
		}
		
		if ( $( ".wpi_db" ).length ) {			
			colors.wpiDynamic_colors();			
		}
		
		if ( sty_el.length ) {
			
			$saved_args=current_args($sty_defaults);			
			set_style("default", $saved_args );	
			
		}else if ( but_el.length && styles.length ) {	
			styles.find(".wpi_style").click(function(){				
				but_map.apply_preset(this);
			});		 
		}else if ( sli_el.length && styles.length ) {	
			styles.find(".wpi_style").click(function(){				
				sli_map.apply_preset(this);
			});		 
		}
		
		
		function set_style(element, args ){
			if(element!="default"){
				$(element).click(function(){			
					set_preset(args);
					restore.removeClass("wpi_none");
				});	
			}else{
				
				set_preset(args);				
				restore.addClass("wpi_none");
			}
		};	
		
		
		function set_preset(args){	
		
			var defaults=current_args($sty_defaults);			
			var args=$.extend(defaults, args );
			$.each(args, function(key, val) { 
				$("#"+key).val(val);
			});
			//remove_class();
			remove_all_class(["shape", "shadow", "text_shadow", "border_sides", "padding", "texture"]);
			$classes="";
			$classes+="wpi_"+args.shape;
			$classes+=" shadow wpi_shadow_"+args.shadow;
			$classes+=" wpi_text_shadow_"+args.text_shadow;
			$classes+=" wpi_padding_"+args.padding;
			$classes+=" wpi_border_"+args.border_sides;	
			$classes+=" wpi_texture_"+args.texture;			
			
			var common={			
				"font-size":args.text_size,
				"font":args.font,
				"font-weight":args.font_weight,
				"border-width":args.border_width,
				"border-style":args.border_style,
				"display":args.display
			};
			common['box-shadow']=get_glow(args.glow_size, args.glow_color);
			
			$normal={
				"background-color":args.background_color, 
				"color":args.text_color,
				"border-color":args.border_color		
			};	
			$hover={
				"background-color":args.background_color_h, 
				"color":args.text_color_h, 
				"border-color":args.border_color_h
			};
			$active={
				"background-color":args.background_color_a, 
				"color":args.text_color_a, 
				"border-color":args.border_color_a
			};
			preview_button.addClass($classes);	
			preview_button.css(common);
			preview_button.css($normal);
			
			stateChange($states);			
		}
		
		function set_icon(element,val){			
			$(element).click(function(){			
				set_inputs({icon:val}, $sty_defaults);			
				remove_class("icon");
				$classes="";			
				$classes+=" wpi_icon wpi_icon_"+val;	
				preview_button.addClass($classes);
			});				
		}
		
		function create_presets(element, args){
			
			var defaults=$sty_defaults;
			
			var args=$.extend(defaults, args );
			/*$.each(args, function(key, val) { 
				$("#"+key).val(val);
			});
			remove_class();*/
			var  classes="";
			classes+="wpi_"+args.shape;
			classes+=" shadow wpi_shadow_"+args.shadow;
			classes+=" wpi_text_shadow_"+args.text_shadow;
			classes+=" wpi_padding_"+args.padding;
			classes+=" wpi_border_"+args.border_sides;	
			classes+=" wpi_texture_"+args.texture;
			
			var common={			
				"font-size":args.text_size,
				"border-width":args.border_width,
				"border-style":args.border_style,
				"display":args.display
			};
			common['box-shadow']=get_glow(args.glow_size, args.glow_color);
			
			var normal={
				"background-color":args.background_color, 
				"color":args.text_color,
				"border-color":args.border_color		
			};				
			$(element).addClass(classes);	
			$(element).css(common);
			$(element).css(normal);			
		}	
		$.fn.create_presets=function(index,name,args){
			$("#wpi_presets").append($("<div class='wpi_preset_holder' ><div id='wpi_db_pre_"+index+"' class='wpi_designer_button'>"+get_brand(index)+"</div></div>"));			
			set_style("#wpi_db_pre_"+index, args );
			args['display']="inline-block";
			create_presets("#wpi_db_pre_"+index, args );			
		};
		$.fn.create_sli_presets=function(index,name,args){
			$("#wpi_sli_presets").append($("<div class='wpi_preset_holder' ><div id='wpi_db_sli_pre_"+index+"' ><img src='"+(WPiURLS.WPIDB_URL)+"images/thumbnails/a"+index+".png'/><div></div>"));
			$("#wpi_db_sli_pre_"+index).click(function(){
				sli_map.apply_sli_preset(args);
			});					
		};
		function get_brand(index){	
			var position=0;
			var brands=["facebook", "Twitter", "Infosys", "WordPress", "Tata", "Amazon", "Ebay", "Flipcart", "Fashion", "Mashable", "Nokia", "Nasa"];
			position=  index % brands.length;
			return brands[position];
					
		}
		$.fn.create_themes=function( args){
			var output="<div id='wpi_db_the_"+$wpi_db_theme_count+"' class='wpi_theme wpi_theme_col"+args.length+"'><div class='wpi_theme_holder'>";
				output+="<div class='wpi_theme_color_holder'>";
				$.each(args,function(key,val){
					output+="<div class='wpi_theme_color' style='background:"+val+"'></div>";	 
				});	
				output+="</div>";
				output+="<div class='wpi_theme_title'>Theme "+$wpi_db_theme_count+"</div>";
			output+="</div></div>";
			$("#wpi_themes").append($(output));			
			args=create_theme_css(args);
			set_style("#wpi_db_the_"+$wpi_db_theme_count, args );
			
			$wpi_db_theme_count++;
		};
		$.fn.create_icons=function( val){			
			var output="<div id='wpi_db_ico_"+$wpi_db_icons_count+"' class='wpi_icons_holder'>";			
			output+="<div class='wpi_icon'><div class='genericon genericon-"+val+"'><div class='wpi_icon_title'>"+val+"</div></div></div>";	 
			output+="</div>";
			$("#wpi_icons").append($(output));				
			set_icon("#wpi_db_ico_"+$wpi_db_icons_count, val);
			
			$wpi_db_icons_count++;
			
		};
		
		$.fn.set_icons_arr=function(icons){
			$wpi_db_icons_arr=icons;
		}
		
		function create_theme_css(args){
			var properties={};			
			var text=args[0];	
			var tones=get_tones(args[1]);
			var background_h=tones['lighter'];
			var background_a=tones['darker'];
			var background=args[1];
			var border=args[2];
			var glow=args[2];
			
			
			properties['text_color']=text;
			properties['background_color']=background;
			properties['border_color']=border;
			properties['text_color_h']=text;
			properties['background_color_h']=background_h;
			properties['border_color_h']=border;
			properties['text_color_a']=text;
			properties['background_color_a']=background_a;
			properties['border_color_a']=border;
			
			properties['glow_color']=glow;
			return properties;
		}
		
		$("#wpi_colors .color input").live('mouseup', function() { $(this).select(); });
		
		
		function stateChange(args){
			
			for(i=0; i<args.length; i++){							
				args[i]['element'].change({element: args[i]},function(event){
					if(	event.data.element['type']=="color"){
						val= TOOLS.setColor($( this ).val());
						$( this ).val(val);
					}
					val= $( this ).val();	
					//alert(val);
					if(event.data.element['state']=="normal"){
						preview_button.css(event.data.element['prop'], val);
						$normal[event.data.element['prop']]=val;
					}else if(event.data.element['state']=="active"){						
						$active[event.data.element['prop']]=val;
					}else{
						$hover[event.data.element['prop']]=val;		
					}									
				});
			};			
		};
		
		
		//global functions
		
		$(".wpi_notes, #wpi_presets, #wpi_help ").disableSelection();
			
		var args={he:$("#wpi_presets_data .wpi_header"), hec:$("#wpi_presets_data .wpi_header_holder")};
		$("#wpi_presets").wpiScroll(args);
		
		var htmlEscapes = {
		  '&': '&amp;',
		  '<': '&lt;',
		  '>': '&gt;',
		  '"': '&quot;',
		  "'": '&#x27;',
		  '/': '&#x2F;'
		};
		var htmlEscaper = /[&<>"'\/]/g;
		function _escape(string) {
		  return ('' + string).replace(htmlEscaper, function(match) {
			return htmlEscapes[match];
		  });
		};
		
		function export_style(defaults){			
			var args=current_args(defaults);
			
			var style="{<br>";
			$.each(args, function(key, val) { 
				var val=$()._escape(val);
				style+=key+":'"+val+"',<br>";
			});
			style+="}";
			
			return style;
		};
		function export_fn(defaults){
			_export.click(function(event){			
				event.preventDefault();	
				var num=$wpi_export_tabs_count++;
				$(".wpi_visual_holder .wpi_tabs").append($("<a id='del_export_tab"+num+"' href='#export_tab"+num+"' class='wpi_tab'>Export "+num+"</a><a href='#del_export_tab"+num+"' class='wpi_delete_tab wpi_icon wpi_icon_close wpi_no_text'><i></i></a>"));
				$(".wpi_visual_holder .wpi_tabs_content").append($("<div id='export_tab"+num+"' class='wpi_export_tab_content wpi_tab_content wpi_none'></div>"));	
				var export_data=export_style(defaults);
				$("#export_tab"+num).html(export_data);			
				tabs.set_tabs();
			});
		}
		
		//ui
		
		var $WPiPN={				
			cur:0,
			nxt:0,
			prv:0,
			total:0,
			init:function(total){					
				this.total=total;
				this.set_pn("s");
			},
			next:function(){
				var out=this.nxt;
				this.set_pn("n");
				return out;
			},
			prev:function(){
				var out=this.prv;
				this.set_pn("p");
				return out;
			},
			current:function(){
				var out=this.cur;
				return out;
			},
			set_pn:function(i){
				if(i=="n"){
					if((this.nxt+1)==(this.total)){
						this.prv=this.cur;
						this.cur=this.nxt;
						this.nxt=0;
					}else{
						this.prv=this.cur;
						this.cur=this.nxt;
						this.nxt=this.nxt+1;
					}
				}else if(i=="p"){
					if((this.prv-1)==(-1)){
						this.nxt=this.cur;
						this.cur=this.prv;
						this.prv=this.total-1;
					}else{
						this.nxt=this.cur;
						this.cur=this.prv;
						this.prv=this.prv-1;
					}
				}else if(i=="s"){						
					this.nxt=1;
					this.cur=0;
					this.prv=this.total-1;
				}
			},
		};
		function set_pn(args){
			
			var pn=$(args['parent']);
			var next=pn.find(args['next']);
			var prev=pn.find(args['prev']);
			var notes_nav=pn.find(".wpi_notes_nav");			
			var notes_holder=pn.find(args['holder']);	
			var notes=notes_holder.find(args['items']);	
			var view_all=pn.find(".wpi_view_all");
			var popup=pn.siblings(".wpi_popup");
			var popup_back=popup.find(".wpi_back");
			
			var wpi_pn=$WPiPN;			
			wpi_pn.init(notes.length);
			
			next.click(function(){
				var go=wpi_pn.next();
				notes.addClass("wpi_none");
				notes_holder.find(args['prefix']+go).removeClass("wpi_none");
			});	
			prev.click(function(){
				var go=wpi_pn.prev();
				notes.addClass("wpi_none");
				notes_holder.find(args['prefix']+go).removeClass("wpi_none");
			});	
			view_all.click(function(){				
				popup.removeClass("wpi_none");				
			});
			popup_back.click(function(){				
				popup.addClass("wpi_none");				
			});
			notes_nav.mouseenter(function(){
				$(this).addClass("wpi_hover");
			}).mouseleave(function(){
				$(this).removeClass("wpi_hover");
			});
			function goswipe(action){
				//alert(action);
				if(action=="n"){
					var go=wpi_pn.next();
				}else if(action=="p"){
					var go=wpi_pn.prev();
				}else{
					return;
				}
				notes.addClass("wpi_none");
				notes_holder.find(args['prefix']+go).removeClass("wpi_none");
			};
			var swipe_args={
				test:23,
				grabbingParent:args['parent'],
			};
			$(args['holder']).wpiSwipe(swipe_args, goswipe);
		};
		
		var args={ parent:".wpi_pn", holder:".wpi_notes_content_holder", items:".wpi_note", prefix:".wpi_note_", next:".wpi_next", prev:".wpi_prev",};
		set_pn(args);
		
		//Helpers
		
		
		//get_tones("#cf8790");
		function get_index(char, arr){
			var index=1;
			for(var i=0; i<arr.length;i++){
				if(char==arr[i]){
					index=i;
				}
			}
			return index;
		}
		function get_tones(color){
			var values=new Array(0,1,2,3,4,5,6,7,8,9,"a","b","c","d","e","f");
			var lighter="";
			var darker="";
			var str=color.toLowerCase();
			var res = str.split("");
			for(var i=0; i<res.length; i++){
				var val=res[i];	
				var position=get_index(val, values);						
				if(val=="#"){
					lighter+=val;	
					darker+=val;
				}else{					
					if(val=="f" || val=="F"){
						lighter+=val;					
					}else{
						//lighter+=val;
						lighter+=values[position+1];
					}
					if(val==0){
						darker+=val;					
					}else{
						//darker+=val;	
						darker+=values[position-1];
					}
				}
				
			};			
			//alert(lighter+ " "+color+" "+darker);
			return {lighter:lighter, darker:darker};			
		}
		function set_color(val){
			if(val!=""){
				val= val.replace(/ +/g, "");
				val= val.replace(/#/g, "");
				var str1 = "#";			
				val = str1.concat(val);				
			}
			return val;
		}
		function get_glow(glow_size, glow_color){
			if(glow_size!="no" && glow_size!="" && glow_color!="" ){
				output="0 0 "+glow_size+" "+glow_color;
			}else if(glow_size!="no" && glow_size!=""){
				output="0 0 "+glow_size;
			}else{
				output="";
			}
			return output;
		}
				
		function splitValue(value, index) {
			return value.substring(0, index) + " " + value.substring(index);
		}
		
	});
}(jQuery));