<?php

class qa_ask_restriction_admin
{
	public function init_queries($tableslc)
	{
		return;
	}

	public function option_default($option)
	{
		switch ($option) {
			case 'qa_ask_restriction_date':
				return 7;
			default:
				return;
		}
	}

	public function allow_template($template)
	{
		return $template != 'admin';
	}

	public function admin_form(&$qa_content)
	{
		// process the admin form if admin hit Save-Changes-button
		$ok = null;
		if (qa_clicked('qa_ask_restriction_save')) {
			qa_opt('qa_ask_restriction_date', (int)qa_post_text('qa_ask_restriction_date'));
			$ok = qa_lang('admin/options_saved');
		}

		// form fields to display frontend for admin
		$fields = array();

		$fields[] = array(
			'label' => qa_lang('qa_ask_restriction_lang/reference_date'),
			'tags' => 'NAME="qa_ask_restriction_date"',
			'value' => qa_opt('qa_ask_restriction_date'),
			'type' => 'number',
			'suffix' => qa_lang('qa_ask_restriction_lang/days'),
		);

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => $fields,
			'buttons' => array(
				array(
					'label' => qa_lang_html('main/save_button'),
					'tags' => 'name="qa_ask_restriction_save"',
				),
			),
		);
	}
}
