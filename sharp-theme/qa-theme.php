<?php

$theme_dir = dirname( __FILE__ ) . '/';
$theme_url = qa_opt('site_url') . 'qa-theme/' . qa_get_site_theme() . '/';
qa_register_layer('/qa-admin-options.php', 'Theme Options', $theme_dir , $theme_url );

class qa_html_theme extends qa_html_theme_base{
	function body_content()
	{
		$this->body_prefix();
		$this->notices();
		$this->output('<DIV CLASS="qa-top-header"><DIV CLASS="qa-top-wrapper">');
		$this->nav_user();
		$this->output('<a class="qa-nav-ask qa-nav-main-selected" href="./ask">Ask Now</a>');
		$this->output('</DIV></DIV>');
		
		
		$this->output('<DIV CLASS="qa-body-wrapper">', '');

		$this->widgets('full', 'top');
		$this->header();
		$this->widgets('full', 'high');
		$this->sidepanel();
		$this->main();
		$this->widgets('full', 'low');
		$this->footer();
		$this->widgets('full', 'bottom');
		
		$this->output('</DIV> <!-- END body-wrapper -->');
		
		$this->body_suffix();
	}
	function head_css()
	{
		if (qa_opt('qat_compression')==2) //Gzip
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'qa-styles-gzip.php'.'"/>');
		elseif (qa_opt('qat_compression')==1) //CSS Compression
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.'qa-styles-commpressed.css'.'"/>');
		else // Normal CSS load
			$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$this->rooturl.$this->css_name().'"/>');
		
		if (isset($this->content['css_src']))
			foreach ($this->content['css_src'] as $css_src)
				$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.$css_src.'"/>');
				
		if (!empty($this->content['notices']))
			$this->output(
				'<STYLE><!--',
				'.qa-body-js-on .qa-notice {display:none;}',
				'//--></STYLE>'
			);
	}	
	function head_script()
	{
			qa_html_theme_base::head_script();	
			$this->output('<script>$(document).ready(function(){$("#back-top").hide();$(function () {$(window).scroll(function () {if ($(this).scrollTop() > 100) {$("#back-top").fadeIn();} else {$("#back-top").fadeOut();}});$("#back-top a").click(function () {$("body,html").animate({scrollTop: 0}, 800);return false;});});});</script>');
	}
	function nav_user()
	{
		$this->nav('user');
	}
	function nav_main_sub()
	{
		$this->nav('main');
	}
	function nav_user_search()
	{
		$this->search();
	}
	
	function search_field($search)
	{
		$this->output('<INPUT type="text" '.$search['field_tags'].' VALUE="'.@$search['value'].'" CLASS="qa-search-field" placeholder="What are you looking for?"/>');
	}
	
	function nav($navtype, $level=null)
	{
		$navigation=@$this->content['navigation'][$navtype];
		
		if (($navtype=='user') || isset($navigation)) {
			$this->output('<DIV CLASS="qa-nav-'.$navtype.'">');
			
			if ($navtype=='user')
				$this->logged_in();
				
			// reverse order of 'opposite' items since they float right
			foreach (array_reverse($navigation, true) as $key => $navlink)
				if (@$navlink['opposite']) {
					unset($navigation[$key]);
					$navigation[$key]=$navlink;
				}
			
			$this->set_context('nav_type', $navtype);
			$this->nav_list($navigation, 'nav-'.$navtype, $level);
			if($navtype=="main") {
					$this->social="";
					if (qa_opt('qat_social_feed')!="")$this->social='<a href="'.qa_opt('qat_social_feed').'"><div class="social rss" social="RSS"><div class="triangle"></div></div></a>'.$this->social;
					if (qa_opt('qat_social_flicker')!="")$this->social='<a href="'.qa_opt('qat_social_flicker').'"><div class="social flickr" social="Flickr"><div class="triangle"></div></div></a>'.$this->social;
					if (qa_opt('qat_social_blog')!="")$this->social='<a href="'.qa_opt('qat_social_blog').'"><div class="social blogs" social="Blogs"><div class="triangle"></div></div></a>'.$this->social;
					if (qa_opt('qat_social_linkedin')!="")$this->social='<a href="'.qa_opt('qat_social_linkedin').'"><div class="social linkedin" social="Linkedin"><div class="triangle"></div></div></a>'.$this->social;
					if (qa_opt('qat_social_youtube')!="")$this->social='<a href="'.qa_opt('qat_social_youtube').'"><div class="social youtube" social="YouTube"><div class="triangle"></div></div></a>'.$this->social;
					if (qa_opt('qat_social_fb')!="")$this->social='<a href="'.qa_opt('qat_social_fb').'"><div class="social facebook" social="Facebook"><div class="triangle"></div></div></a>'.$this->social;
					if (qa_opt('qat_social_twitter')!="")$this->social='<a href="'.qa_opt('qat_social_twitter').'"><div class="social twitter" social="Twitter"><div class="triangle"></div></div></a>'.$this->social;
					if (!$this->social=="")	$this->output('<div class="socialstuff">'.$this->social.'</div>');
				}
			$this->nav_clear($navtype);
			$this->clear_context('nav_type');

			$this->output('</DIV>');
		}
	}
	
	function main()
	{
		qa_html_theme_base::main();
		$this->output('<p id="back-top"><a href="#top"><span></span>To Top</a></p>', '');		
	}
	
	function page_title_error()
	{
		$title=@$this->content['title'];
		$favorite=@$this->content['favorite'];
		
		if (isset($favorite))
			$this->output('<FORM '.$favorite['form_tags'].'>');

		$this->output('<H1>');



		if (isset($title))
			$this->output($title);

		if (isset($this->content['error'])) {
			$this->output('</H1>');
			$this->error(@$this->content['error']);
		} else
			$this->output('</H1>');
		if (isset($favorite)) {
			$this->output('<DIV CLASS="qa-favoriting" '.@$favorite['favorite_tags'].'>');
			$this->favorite_inner_html($favorite);
			$this->output('</DIV>');
		}
		if (isset($favorite)) {
			$this->form_hidden_elements(@$favorite['form_hidden']);
			$this->output('</form>');
		}
		$this->nav('sub');	
		$this->q_item_clear();
	}

	function q_item_stats($q_item)
	{
		$this->output('<DIV CLASS="qa-q-item-stats">');
		
		$this->voting($q_item);
		$this->a_count($q_item);
		$this->view_count($q_item);		
		$this->output('</DIV>');
	}
	
	function q_item_main($q_item)
	{
		$this->output('<DIV CLASS="qa-q-item-main">');
		
		
		$this->q_item_title($q_item);
		$this->q_item_content($q_item);
		
		$this->post_avatar($q_item, 'qa-q-item');
		$this->post_meta($q_item, 'qa-q-item');
		$this->post_tags($q_item, 'qa-q-item');
		$this->q_item_buttons($q_item);
			
		$this->output('</DIV>');
	}

	function post_meta($post, $class, $prefix=null, $separator='<BR/>')
	{
		$this->output('<SPAN CLASS="'.$class.'-meta">');
		
		if (isset($prefix))
			$this->output($prefix);
		
		$order=explode('^', @$post['meta_order']);
		
		foreach ($order as $element)
			switch ($element) {
				case 'what':
					$this->post_meta_what($post, $class);
					break;
					
				case 'when':
					$this->post_meta_when($post, $class);
					break;
					
				case 'where':
					$this->post_meta_where($post, $class);
					break;
					
				case 'who':
					$this->post_meta_who($post, $class);
					break;
			}
			
		$this->post_meta_flags($post, $class);
		
		if (!empty($post['what_2'])) {
			$this->output($separator);
			
			foreach ($order as $element)
				switch ($element) {
					case 'what':
						$this->output('<SPAN CLASS="'.$class.'-what">'.$post['what_2'].'</SPAN>');
						break;
					
					case 'when':
						$this->output_split(@$post['when_2'], $class.'-when');
						break;
					
					case 'who':
						$this->output_split(@$post['who_2'], $class.'-who');
						break;
				}
		}
		
		$this->output('</SPAN>');
	}
	
	function q_view($q_view)
	{
		if (!empty($q_view)) {
			$this->output('<div class="qa-q-view'.(@$q_view['hidden'] ? ' qa-q-view-hidden' : '').rtrim(' '.@$q_view['classes']).'"'.rtrim(' '.@$q_view['tags']).'>');
			
			if (isset($q_view['main_form_tags']))
				$this->output('<form '.$q_view['main_form_tags'].'>'); // form for voting buttons
			$this->voting($q_view);
			if (isset($q_view['main_form_tags'])) {
				$this->form_hidden_elements(@$q_view['voting_form_hidden']);
				$this->output('</form>');
			}

			$this->a_count($q_view);
			$this->q_view_main($q_view);
			$this->q_view_clear();
			
			$this->output('</DIV> <!-- END qa-q-view -->', '');
		}
	}
	

	function a_item_main($a_item)
	{
		$this->output('<DIV CLASS="qa-a-item-main">');
		
		if ($a_item['hidden'])
			$this->output('<DIV CLASS="qa-a-item-hidden">');
		elseif ($a_item['selected'])
			$this->output('<DIV CLASS="qa-a-item-selected">');

		$this->a_selection($a_item);
		$this->error(@$a_item['error']);
		$this->a_item_content($a_item);
		$this->post_avatar($a_item, 'qa-a-item');
		$this->post_meta($a_item, 'qa-a-item');
		
		if ($a_item['hidden'] || $a_item['selected'])
			$this->output('</DIV>');
		
		$this->a_item_buttons($a_item);
		
		$this->c_list(@$a_item['c_list'], 'qa-a-item');

		if (isset($a_item['main_form_tags']))
			$this->output('</FORM>'); // form ends just before comment form (breaks nesting)

		$this->c_form(@$a_item['c_form']);

		$this->output('</DIV> <!-- END qa-a-item-main -->');
	}
	function attribution()
	{
		// you can disable this links in admin options
		if (!(qa_opt('qat_theme_attribution'))) 
			$this->output(
				'<DIV CLASS="qa-attribution">',
				', Design by <A HREF="http://QA-Themes.com/" title="Q2A Themes and plugins">Q2A Themes</A>',
				'</DIV>'
			);
		if (!(qa_opt('qat_qa_attribution'))) 
			qa_html_theme_base::attribution();
	}
}


/*
	Omit PHP closing tag to help avoid accidental output
*/