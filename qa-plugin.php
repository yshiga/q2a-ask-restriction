<?php

/*
		Plugin Name: Ask Restriction
		Plugin URI:
		Plugin Update Check URI:
		Plugin Description: Restriction is ask question.
		Plugin Version: 0.2
		Plugin Date: 2016-06-14
		Plugin Author: 38qa.net
		Plugin Author URI: 38qa.net
		Plugin License: GPLv2
		Plugin Minimum Question2Answer Version: 1.7
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
			header('Location: ../../');
			exit;
	}

	// language file
	qa_register_plugin_phrases('qa-ask-restriction-lang-*.php', 'qa_ask_restriction_lang');
	// layer
	qa_register_plugin_layer('qa-ask-restriction-layer.php', 'Ask Restriction Layer');
	// admin
	qa_register_plugin_module('module', 'qa-ask-restriction-admin.php', 'qa_ask_restriction_admin', 'Ask Restriction Admin');

/*
	Omit PHP closing tag to help avoid accidental output
*/
