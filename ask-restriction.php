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
		$sql="SELECT profile.title, profile.content
		 FROM qa_userprofile AS profile
		 INNER JOIN qa_userfields AS fields
		 ON profile.title = fields.title";
		$sql.=qa_db_apply_sub(" WHERE profile.userid = #", array((int)$userid));
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

	public static function get_no_best_answer_question($userid=null, $mincount=2)
	{
		$sql="SELECT t1.postid AS qid, t1.title,
		 COUNT(t1.postid) AS answer_num
		 FROM qa_posts t1
		 INNER JOIN qa_posts t2
		 ON t1.postid = t2.parentid ";
		$sql.=qa_db_apply_sub(" WHERE t1.userid = #" , array((int)$userid));
		$sql.=" AND t1.type = 'Q'
		 AND t1.selchildid IS NULL
		 AND t2.type='A'
		 GROUP BY t1.postid";
		$sql.=qa_db_apply_sub(" HAVING answer_num >= #" , array((int)$mincount));
		// echo $sql.PHP_EOL;
		$result = qa_db_query_sub($sql);
		return qa_db_read_all_assoc($result);
	}
}
