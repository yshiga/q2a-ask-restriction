<?php

require_once QA_PLUGIN_DIR.'q2a-ask-restriction/ask-restriction.php';

class qa_html_theme_layer extends qa_html_theme_base
{
	const ANSWER_MIN_COUNT = 2;
	public $input_profile_ok;
	public $no_comment_answer_question;

	public function __construct($template, $content, $rooturl, $request)
	{
		if (qa_is_logged_in() && $template == 'ask') {
			$days = qa_opt('qa_ask_restriction_date') ? (int)qa_opt('qa_ask_restriction_date') : 7;
			$userid = qa_get_logged_in_userid();
			$this->input_profile_ok = ask_restriction::input_profile_ok($userid);
			$this->no_comment_answer_question = ask_restriction::get_no_comment_answer_question($userid, $days);
		} else {
			$this->input_profile_ok = true;
			$this->no_comment_answer_question = array();
		}

		qa_html_theme_base::__construct($template, $content, $rooturl, $request);
	}

	public function head_css()
	{
		qa_html_theme_base::head_css();

		if (!$this->input_profile_ok
		|| count($this->no_comment_answer_question) > 0) {
			$css = "<style>
#profile-container {
	padding: 0 30px;
}
.input-profile-button {
	background-color: #f95225;
	color: #fff;
	text-align: center;
	padding: 5px 20px;
	border: 0;
	cursor: pointer;
}
.input-profile-button:hover {
	opacity: 0.8;
}
.question_list {
	list-style-position: inside;
}
</style>";
			$this->output($css);
		}
	}
	public function page_title_error()
	{
		if (!$this->input_profile_ok
		|| count($this->no_comment_answer_question) > 0) {
			$this->content['title'] = '';
		}
		qa_html_theme_base::page_title_error();
	}

	public function main_part($key, $part)
	{
		if ($key === 'form' && (
		!$this->input_profile_ok
		|| count($this->no_comment_answer_question) > 0 )) {
			$this->output('<div id="profile-container">');
			$this->output_message_header();
			$this->output_profile_message();
			$this->output_no_comment_answer_questions();
			$this->output_no_best_answer_questions();
			$this->output('</div>');
		} else {
			qa_html_theme_base::main_part($key, $part);
		}
	}

	private function output_message_header()
	{
		$title = qa_lang_html('qa_ask_restriction_lang/title');
		$this->output('<h1>', $title, '</h1>');
		$content = qa_lang_html('qa_ask_restriction_lang/little_only_after');
		$this->output('<p><b>', $content, '</b></p>');
		$this->output('<br>');
	}

	private function output_profile_message()
	{
		if ($this->input_profile_ok) {
			return;
		}
		$title = qa_lang_html('qa_ask_restriction_lang/input_profile_title');
		$this->output('<h2>', $title, '</h2>');
		$content = qa_lang_html('qa_ask_restriction_lang/input_message');
		$this->output('<p>', $content, '</p>');
		$this->output('<br>');

		$title = qa_lang_html('qa_ask_restriction_lang/input_profile');
		$this->output('<p>', $title, '</p>');
		$content = qa_lang_html('qa_ask_restriction_lang/profile_content');
		$this->output('<p>', $content, '</p>');
		$this->output('<br>');

		$title = qa_lang_html('qa_ask_restriction_lang/acitvity_location');
		$this->output('<p>', $title, '</p>');
		$content = qa_lang_html('qa_ask_restriction_lang/location_content');
		$this->output('<p>', $content, '</p>');
		$this->output('<br>');

		$content = qa_lang_html('qa_ask_restriction_lang/button_catption');
		$this->output('<a href="', qa_path('account'), '">');
		$this->output('<button class="input-profile-button">', $content, '</button>');
		$this->output('</a>');
		$this->output('<br><br>');
	}

	private function output_no_comment_answer_questions()
	{
		if(count($this->no_comment_answer_question) <= 0) {
			return;
		}
		$title = qa_lang_html('qa_ask_restriction_lang/comment_title');
		$this->output('<h2>', $title, '</h2>');
		$content = qa_lang_html('qa_ask_restriction_lang/comment_content');
		$this->output('<p>', $content, '</p>');
		$this->output('<br>');
		$this->output('<ul class="question_list">');
		foreach ($this->no_comment_answer_question as $question) {
			$url = qa_opt('sit_url') . "/" . $question["question_id"] . "#" . $question["answer_id"];
			$link = '<a href="'.$url.'">' . $question["question"] . "</a>";
			$this->output('<li>', $link, '</li>');
		}
		$this->output('</ul>');
		$this->output('<br>');
	}
}
