<?php

class qa_html_theme_layer extends qa_html_theme_base
{

	public function header()
	{
		if($this->template != 'ask') {
			qa_html_theme_base::header();
		}
	}
}
