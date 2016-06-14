<?php

require_once QA_PLUGIN_DIR.'q2a-ask-restriction/ask_restriction.php';

class qa_html_theme_layer extends qa_html_theme_base
{
	public $input_profile_ok;

	public function __construct($template, $content, $rooturl, $request)
	{
		if(qa_is_logged_in()) {
			$userid = qa_get_logged_in_userid();
			$this->input_profile_ok=ask_restriction::input_profile_ok($userid);
		}

		qa_html_theme_base::__construct($template, $content, $rooturl, $request);
	}
	public function main_part($key, $part)
	{
		if($key == 'form' && !$this->input_profile_ok) {
			$message = qa_lang_html('qa_ask_restriction_lang/ask_must_profile');
			$link = qa_path('account');
			// $error_message = $this->insert_into_links($message, $link);
			$this->output('質問を投稿する前に');
			$this->output($message);
		} else {
			qa_html_theme_base::main_part($key, $part);
		}
	}

	private function insert_into_links($message, $link)
	{
		return strtr(
			$message,
			array(
				'^1' => '<a href="'.qa_html($link).'">',
				'^2' => '</a>',
			)
		);
	}
}
