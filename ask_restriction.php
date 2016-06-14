<?php
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
require_once QA_INCLUDE_DIR.'qa-db-selects.php';

class ask_restriction
{
	public static function input_profile_ok($userid = null)
	{
		$location = false;
		$about = false;
		$sql = "SELECT profile.title, profile.content
		 FROM qa_userprofile AS profile
		 INNER JOIN qa_userfields AS fields
		 ON profile.title = fields.title";
		$sql .= qa_db_apply_sub(" WHERE profile.userid = #", array((int)$userid));
		// echo $sql."\n";
		$results = qa_db_read_all_assoc(qa_db_query_sub($sql));
		if(empty($results)) {
			return false;
		}
		// 地域と自己紹介が入力されているかチェック
		foreach($results as $result) {
			if ($result['title'] == 'location' && !empty($result['content'])) {
				$location = true;
			}
			if ($result['title'] == 'about' && !empty($result['content'])) {
				$about = true;
			}
		}
		
		return $location && $about;
	}
}
