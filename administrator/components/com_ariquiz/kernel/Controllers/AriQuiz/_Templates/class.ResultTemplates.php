<?php
define('ARI_RESULT_HTML_TEMPLATE',
<<<HTML_TEMPLATE
		<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:ns0="urn:schemas-microsoft-com:office:smarttags" xmlns="http://www.w3.org/TR/REC-html40">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			</head>
			<body>
				%s
			</body>
		</html>
HTML_TEMPLATE
);

define('ARI_RESULT_HTML_QUIZ_DATA_HEADER',
<<<HTML_QUIZ_DATA_HEADER
				<table style="width: 100%%;">
					<tr valign="top">
						<th>#</th>
						<th>Question</th>
						<th>User Score</th>
						<th>Max Score</th>
					</tr>
					%s
				</table>
HTML_QUIZ_DATA_HEADER
);

define('ARI_RESULT_HTML_QUIZ_HEADER',
<<<HTML_QUIZ_HEADER
					<table style="width: 100%%;border: solid 1px black;">
						<tr>
							<td>Quiz Name:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>Start Date:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>End Date:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>Total Time:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>User Score:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>Total Score:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>Passing Percentage:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>Passed:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>Passing Name:</td>
							<td>%s</td>
						</tr>
						<tr>
							<td>Email:</td>
							<td>%s</td>
						</tr>
					</table>
HTML_QUIZ_HEADER
);

define('ARI_RESULT_HTML_DATA_ROW',
<<<DATA_ROW
				<tr valign="top">
					<td align="center">%s. </td>
					<td>%s</td>
					<td align="center">%s</td>
					<td align="center">%s</td>
				</tr>
DATA_ROW
);

define('ARI_RESULT_HTML_EXCEL_TEMPLATE',
<<<HTML_EXCEL_TEMPLATE
		<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:ns0="urn:schemas-microsoft-com:office:smarttags" xmlns="http://www.w3.org/TR/REC-html40">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			</head>
			<body>
				<table>
					<tr>
						<th>#</th>
						<th>Quiz Name</th>
						<th>User</th>
						<th>Email</th>
						<th>Question Count</th>
						<th>Passed</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Spent Time</th>
						<th>User Score</th>
						<th>User Score Percent</th>
						<th>Max Score</th>
						<th>Passing Score</th>
					</tr>
					%s
				</table>
			</body>
		</html>
HTML_EXCEL_TEMPLATE
);

define('ARI_RESULT_HTML_EXCEL_ROW',
<<<EXCEL_DATA_ROW
				<tr valign="top">
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
				</tr>
EXCEL_DATA_ROW
);
?>