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
		if (empty($userid)) {
			return;
		}
		$sql="SELECT t1.postid AS qid, t1.title,
		 COUNT(t1.postid) AS answer_num
		 FROM qa_posts t1
		 JOIN qa_posts t2
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

	public static function get_no_comment_answer_question($userid=null, $month=null)
	{
		$sql='SELECT t1.userid AS questioner_id,
		 t1.title AS question,
		 t2.parentid AS question_id,
		 t2.content AS answer,
		 t2.postid AS answer_id,
		 count(t3.postid) AS c_count
		 FROM qa_posts t2
		 LEFT JOIN qa_posts t3 ON t2.postid = t3.parentid
		 JOIN qa_posts t1 ON t2.parentid = t1.postid
		 WHERE t1.type="Q"
		 AND t2.type="A"';

		if(isset($month)) {
			$sql.=qa_db_apply_sub(" AND t2.created > DATE_SUB(NOW(), INTERVAL # MONTH)",array((int)$month));
		}

		$sql .= ' GROUP BY t2.postid
		 HAVING c_count = 0';

		if(isset($userid)) {
			$sql.=qa_db_apply_sub(' AND questioner_id = #' , array((int)$userid));
		}
		$sql .= ' ORDER BY questioner_id';

		$result = qa_db_query_sub($sql);
		return qa_db_read_all_assoc($result);
	}
}
