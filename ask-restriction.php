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
		if (empty($results)) {
			return false;
		}
		// 地域と自己紹介が入力されているかチェック
		foreach ($results as $result) {
			if ($result['title'] == 'location' && !empty($result['content'])) {
				$location = true;
			}
			if ($result['title'] == 'about' && !empty($result['content'])) {
				$about = true;
			}
		}

		return $location && $about;
	}

	public static function get_no_best_answer_question($userid=null, $mincount=2, $days=7)
	{
		if (empty($userid)) {
			return;
		}
		$sql = "SELECT t1.postid AS qid, t1.title,
		 COUNT(t1.postid) AS answer_num
		 FROM qa_posts t1
		 JOIN qa_posts t2
		 ON t1.postid = t2.parentid
		 WHERE t1.userid = #
		 AND t1.type = 'Q'
		 AND t1.selchildid IS NULL
		 AND t1.created > DATE_SUB(NOW(), INTERVAL # DAY)
		 AND t2.type = 'A'
		 GROUP BY t1.postid
		 HAVING answer_num >= #";
		$result = qa_db_query_sub($sql, $userid, $days, $mincount);
		return qa_db_read_all_assoc($result);
	}

	public static function get_no_comment_answer_question($userid=null, $days=365)
	{
		$sql = "SELECT t1.userid AS questioner_id,
		 t1.title AS question,
		 t2.parentid AS question_id,
		 t2.content AS answer,
		 t2.postid AS answer_id,
		 count(t3.postid) AS c_count
		 FROM qa_posts t2
		 LEFT JOIN qa_posts t3 ON t2.postid = t3.parentid
		 JOIN qa_posts t1 ON t2.parentid = t1.postid
		 WHERE t1.type = 'Q'
		 AND t2.type = 'A'";
		// $sql .= " AND t2.created > DATE_SUB(NOW(), INTERVAL # MONTH)";
		$sql .= " AND t2.created > DATE_SUB(NOW(), INTERVAL # DAY)";

		$sql .= " GROUP BY t2.postid
		 HAVING c_count = 0
		 AND questioner_id = #
		 ORDER BY questioner_id";

		$result = qa_db_query_sub($sql, (int)$days, (int)$userid);
		return qa_db_read_all_assoc($result);
	}
}
