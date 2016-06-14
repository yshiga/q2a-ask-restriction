<?php

/*
		Plugin Name: Ask Restriction
		Plugin URI:
		Plugin Update Check URI:
		Plugin Description: If it has passed for 24 hours, the question which has no answers is sent by mail.
		Plugin Version: 0.1
		Plugin Date: 2016-06-14
		Plugin Author: 38qa.net
		Plugin Author URI:
		Plugin License: GPLv2
		Plugin Minimum Question2Answer Version: 1.7
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
			header('Location: ../../');
			exit;
	}

	// language file
	qa_register_plugin_phrases('qa-ask-restriction-*.php', 'qa_ask_restriction_lang');
	// layer
	qa_register_plugin_layer('qa-ask-restriction-layer.php', 'Ask Restriction Layer');

/*
	Omit PHP closing tag to help avoid accidental output
*/
