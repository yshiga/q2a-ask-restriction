<?php

require_once QA_PLUGIN_DIR.'q2a-ask-restriction/ask_restriction.php';

class qa_html_theme_layer extends qa_html_theme_base
{
	public $input_profile_ok;

	public function __construct($template, $content, $rooturl, $request)
	{
		if(qa_is_logged_in() && $template == 'ask') {
			$userid = qa_get_logged_in_userid();
			$this->input_profile_ok=ask_restriction::input_profile_ok($userid);
		} else {
			$this->input_profile_ok=true;
		}

		qa_html_theme_base::__construct($template, $content, $rooturl, $request);
	}

	public function head_css()
	{
		qa_html_theme_base::head_css();

		if(!$this->input_profile_ok) {
			$css = "<style>
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
</style>";
			$this->output($css);
		}
	}
	public function page_title_error()
	{
		if(!$this->input_profile_ok) {
			$this->content['title'] = '';
		}
		qa_html_theme_base::page_title_error();
	}

	public function main_part($key, $part)
	{
		if($key == 'form' && !$this->input_profile_ok) {
			$this->output_profile_message();
		} else {
			qa_html_theme_base::main_part($key, $part);
		}
	}

	private function output_profile_message()
	{
		$title='質問を投稿する前に';
		$this->output('<h1>',$title,'</h1>');
		$message='あと少しで質問が投稿できます。回答者の方のために、ご協力をお願いいたします。';
		$this->output('<p><b>',$message,'</b></p>');
		$this->output('<br>');

		$title='プロフィールの入力';
		$this->output('<h2>',$title,'</h2>');
		$message='回答者の方に気持よく、適切な質問を投稿していただくため、';
		$message.='プロフィールの入力をお願い致します。';
		$message.='プロフィールを入力し終わると、質問ができるようになります。';
		$this->output('<p>',$message,'</p>');
		$this->output('<br>');
		$message='・自己紹介の入力';
		$this->output('<p>',$message,'</p>');
		$message='あなたがどのような人で、どんな飼育を行っているかを書きましょう。';
		$this->output('<p>',$message,'</p>');
		$this->output('<br>');
		$message='・活動場所';
		$this->output('<p>',$message,'</p>');
		$message='地域によって飼育方法や適切なアドバイスが変わってきます。活動場所は必ず記載してください。';
		$this->output('<p>',$message,'</p>');
		$this->output('<br>');
		$message='プロフィールを入力する';
		$this->output('<a href="',qa_path('account'),'">');
		$this->output('<button class="input-profile-button">',$message,'</button>');
		$this->output('</a>');
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
