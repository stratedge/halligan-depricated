<?php

namespace Halligan;

use ErrorException;

class Error {

	public static function renderException($exception, $show_trace = TRUE)
	{
		$response = "<html>
			<h2>Unhandled Exception</h2>
			<h3>Message:</h3>
			<pre>" . $exception->getMessage() . "</pre>
			<h3>Location:</h3>
			<pre>" . $exception->getFile() . " on line " . $exception->getLine() . "</pre>";

		if($show_trace)
		{
			$trace = '';

			foreach($exception->getTrace() as $key => $item)
			{
				$trace .= "
					<tr>
						<td><pre>" . ($key+1) . "</pre></td>
						<td><pre>" . (isset($item['line']) ? $item['line'] : '---') . "</pre></td>
						<td><pre>" . (isset($item['class']) ? $item['class'] : '---') . "</pre></td>
						<td><pre>" . (isset($item['function']) ? $item['function'] . "()" : '---') . "</pre></td>
						<td><pre>" . (isset($item['file']) ? $item['file'] : '---') . "</pre></td>
					</tr>";
			}

			$response .= "<h3>Trace:</h3>
				<table>
					<tr>
						<th style=\"border-bottom: 1px solid black;\">#</th>
						<th style=\"border-bottom: 1px solid black;\">Line</th>
						<th style=\"border-bottom: 1px solid black;\">Class</th>
						<th style=\"border-bottom: 1px solid black;\">Function</th>
						<th style=\"border-bottom: 1px solid black;\">File</th>
					</tr>" . $trace . "
				</table>";
		}

		$response .= "</html>";

		ob_clean();
		
		ob_start();

		echo $response;
		
		exit(ob_get_clean());
	}

	public static function renderError($code, $error, $file, $line)
	{
		$exception = new ErrorException($error, $code, 0, $file, $line);

		static::renderException($exception);
	}

	public static function renderShutdown()
	{
		$error = error_get_last();

		if(!is_null($error))
		{
			extract($error, EXTR_SKIP);

			static::renderException(new ErrorException($message, $type, 0, $file, $line), FALSE);
		}
	}

}

/* End of file Error.php */
/* Location: ./Halligan/Error.php */