<?php

namespace Halligan;

use Exception;

class Email {

	protected $_bcc = array();
	protected $_boundary = NULL;
	protected $_cc = array();
	protected $_charset = 'utf-8';
	protected $_from = NULL;
	protected $_headers = NULL;
	protected $_html = NULL;
	protected $_html_template_data = array();
	protected $_html_template_file = NULL;
	protected $_mail_type = 'plain';
	protected $_message_html = NULL;
	protected $_message_text = NULL;
	protected $_reply_to = NULL;
	protected $_subject = NULL;
	protected $_text = NULL;
	protected $_text_template_data = array();
	protected $_text_template_file = NULL;
	protected $_to = array();

	public function __construct()
	{

	}


	//---------------------------------------------------------------------------------------------
	

	public function to($to)
	{
		$this->_addEmailsByType('To', $to);

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function cc($to)
	{
		$this->_addEmailsByType('Cc', $to);

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function bcc($to)
	{
		$this->_addEmailsByType('Bcc', $to);

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _addEmailsByType($type, $emails)
	{
		if(empty($emails)) throw new Exception('No emails provided for ' . $type . ' field.');

		if((is_array($emails) && !is_simple($emails)) || is_object($emails)) throw new Exception('Incorrect format provided for ' . $type . ' field.');

		$type = ucfirst(strtolower($type));

		switch($type)
		{
			case 'To':
				$type_key = '_to';
				break;

			case 'Cc':
				$type_key = '_cc';
				break;

			case 'Bcc':
				$type_key = '_bcc';
				break;

			default:
				throw new Exception('Invalid field type provided for email recipients');
				break;
		}

		$emails = (array) $emails;

		foreach($emails as $key => $email)
		{
			if((bool) filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) unset($emails[$key]);
		}

		if(empty($emails)) throw new Exception('No valid emails provided for ' . $type . ' field.');

		$emails = array_values($emails);

		$this->$type_key = $emails;
	}


	//---------------------------------------------------------------------------------------------
	

	public function from($email, $name = NULL)
	{
		if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) return $this;

		$this->_from = !is_null($name) ? sprintf('"%s" <%s>', $name, $email) : $email;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function replyTo($email, $name = NULL)
	{
		if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE) return $this;

		$this->_reply_to = !is_null($name) ? sprintf('"%s" <%s>', $name, $email) : $email;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function subject($subject)
	{
		if(is_string($subject) || is_numeric($subject)) $this->_subject = $subject;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function text($text)
	{
		$this->_message_text = $text;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function html($html)
	{
		$this->_message_html = $html;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function htmlTemplate($template, $data = array())
	{
		$this->_template('html', $template, $data);

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function textTemplate($template, $data = array())
	{
		$this->_template('text', $template, $data);

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _template($type, $template, $data = array())
	{
		$file = "_" . $type . '_template_file';
		$vars = "_" . $type . '_template_data';

		$this->$file = $template;

		if(is_array($data) || is_object($data)) $this->$vars = (array) $data;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function charset($charset)
	{
		if(!is_array($charset) && !is_object($charset)) $this->_charset = $charset;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function mailType($mailType)
	{
		if(!is_array($mailType) && !is_object($mailType)) $this->_mail_type = $mailType;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function send()
	{
		//First, construct the headers
		$this->_constructHeaders();

		//Generate the HTML portion
		if(!is_null($this->_html_template_file))
		{
			$tpl = new Template($this->_html_template_file);
			$tpl->addData($this->_html_template_data);
			$tpl->addData('charset', $this->_charset);
			
			$this->_message_html = $tpl->build();
		}

		if(!is_null($this->_message_html))
		{
			$html_parts = array(
				"--" . $this->_boundary,
				sprintf("Content-Type: text/html; charset=\"%s\"", $this->_charset),
				"Content-Transfer-Encoding: 7bit\r\n",
				$this->_message_html
			);

			$this->_html = implode("\r\n", $html_parts);
		}

		//Build the text portion
		if(!is_null($this->_text_template_file))
		{
			$tpl = new Template($this->_text_template_file);
			$tpl->addData($this->_text_template_data);
			
			$this->_message_text = $tpl->build();
		}

		if(!is_null($this->_message_text))
		{
			$text_parts = array(
				"--" . $this->_boundary,
				sprintf("Content-Type: text/plain; charset=\"%s\"", $this->_charset),
				"Content-Transfer-Encoding: 7bit\r\n",
				$this->_message_text
			);

			$this->_text = implode("\r\n", $text_parts);
		}
		
		$message_parts = array_filter(array($this->_text, $this->_html, "--" . $this->_boundary . "--"));

		$message = implode("\r\n\r\n", $message_parts);

		return mail(implode(", ", $this->_to), $this->_subject, $message, $this->_headers);
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _constructHeaders()
	{
		$headers = array();

		//From
		if(!is_null($this->_from)) $headers[] = "From: " . $this->_from;

		//Reply-To
		if(!is_null($this->_reply_to)) $headers[] = "Reply-To: " . $this->_reply_to;

		//CC
		if(!empty($this->_cc)) $headers[] = "CC: " . implode(", ", $this->_cc);

		//BCC
		if(!empty($this->_bcc)) $headers[] = "BCC: " . implode(", ", $this->_bcc);

		//MIME Version
		$headers[] = "MIME-Version: 1.0";

		//Guess the content type
		if(!is_null($this->_html_template_file) || !is_null($this->_message_html)) $this->_mail_type = 'html';

		$this->_boundary = substr(sha1(microtime()), 0, 28);

		$headers[] = "Content-Type: multipart/alternative; boundary=" . $this->_boundary;

		$this->_headers = implode("\r\n", $headers);
	}

}

/* End of file Email.php */
/* Location: ./Halligan/Email.php */