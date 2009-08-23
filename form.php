<?php
	include 'connect.php';
	include 'html.php';

	// form 1 (new record) and 2 (editing) defaults
	$author = '';
	$generic = '';
	$topic = '';
	$volinfo = '';
	$year = '';
	$publisher = '';
	$edition = 1;
	$pages = '';
	$identifier = ''; //'ISBN '
	$language = 'English';
	$library = '';
	$issue = '';
	$orientation = 'portrait';
	$dpi = 300;
	$color = 'no';
	$cleaned = 'yes';
	$commentary = '';
	$series = '';

	// form 1 or 2 submitted?
	if ($_POST['Form'] == 1)
	{
		if (trim($_FILES['uploadedfile']['name']) == '')
			die($htmlhead."<font color='#A00000'><h1>No file selected</h1></font>Use 'Browse...' to choose a file on your computer, then 'Send!' to upload it.<br><a href='registration.php'>Return to the last page</a> and try again!".$htmlfoot);

		$pi = pathinfo($_FILES['uploadedfile']['name']);

		$md5 = md5_file($_FILES['uploadedfile']['tmp_name']);

		$title = htmlspecialchars($pi['filename'],ENT_QUOTES);
		$filesize = $_FILES['uploadedfile']['size'];
		@$fileext = strtolower($pi['extension']);

		if (is_null($fileext))
			die($htmlhead."<font color='#A00000'><h1>Error</h1></font>Cannot upload a file with no extension. Assign one and try again!".$htmlfoot);
	}
	else
	{
		if (strlen($_POST['MD5']) != 32)
			die($htmlhead."<font color='#A00000'><h1>Wrong MD5</h1></font>MD5-hashsum must contain 32 symbols.<br>Check it and <a href='registration.php'>try again</a>.<p><h2>Thank you!</h2>".$htmlfoot);

		$md5 = $_POST['MD5'];

		$title = '';
		$filesize = 0;
		$fileext = '';
	}

	// now look up in the database
	$sql="SELECT * FROM $dbtable WHERE MD5='$md5'";
	$result = mysql_query($sql,$con);
	if (!$result)
	{
		die($htmlhead."<font color='#A00000'><h1>Error</h1></font>".mysql_error()."<br>Cannot proceed.<p>Please, report the error from <a href=>the main page</a>.".$htmlfoot);
	}

	$rows = mysql_fetch_assoc($result);
	mysql_close($con);

	// if book found
	if (strlen($rows['MD5']) == 32){
		$editing = true;
		$mode = "<font color=red><h1>Editing existing record</h1></font>";

		// replace all single-quotes, they work as delimiters in HTML and SQL

		$generic = htmlspecialchars($rows['Generic'],ENT_QUOTES);
		$title = htmlspecialchars($rows['Title'],ENT_QUOTES);
		$filesize = $rows['Filesize'];
		$fileext = $rows['Extension'];
		$author = htmlspecialchars($rows['Author'],ENT_QUOTES);
		$topic = htmlspecialchars($rows['Topic'],ENT_QUOTES);
		$volinfo = htmlspecialchars($rows['VolumeInfo'],ENT_QUOTES);
		$year = $rows['Year'];
		$publisher = htmlspecialchars($rows['Publisher'],ENT_QUOTES);
		$edition = htmlspecialchars($rows['Edition'],ENT_QUOTES);
		$pages = htmlspecialchars($rows['Pages'],ENT_QUOTES);
		$identifier = htmlspecialchars($rows['Identifier'],ENT_QUOTES);
		$language = htmlspecialchars($rows['Language'],ENT_QUOTES);
		$library = htmlspecialchars($rows['Library'],ENT_QUOTES);
		$issue = htmlspecialchars($rows['Issue'],ENT_QUOTES);
		$orientation = htmlspecialchars($rows['Orientation'],ENT_QUOTES);
		$dpi = $rows['DPI'];
		$color = htmlspecialchars($rows['Color'],ENT_QUOTES);
		$cleaned = htmlspecialchars($rows['Cleaned'],ENT_QUOTES);
		$commentary = htmlspecialchars($rows['Commentary'],ENT_QUOTES);
		$series = htmlspecialchars($rows['Series'],ENT_QUOTES);
	} else {
		$editing = false;
		$mode = "<font color=green><h1>Registering a new book</h1></font>";
	}

	$regform = $htmlheadfocus."<form action='register.php' method='post'>
<table width=100% border=0 cellspacing=0>
<caption>".$mode."</caption>
<tr><td width=25%><font face=arial size=3><b>Topics</b> <font size=2 color=gray>(separated by '/')</font></font><td><input type='text' name='Topic' id='1' size=100 value='".$topic."' maxlength=500/>
<tr><td><font face=arial size=3><b>Authors</b> <font size=2 color=gray>(authors, editors, ...)</font></font><td><input type='text' name='Author' size=100 value='".$author."' maxlength=1000/>
<tr><td><font face=arial size=3><b>Title</b></font><td><input type='text' name='Title' size=100 value='".$title."' maxlength=500/>
<tr><td><font face=arial size=3><b>Volume</b> <font size=2 color=gray>(part 1, issue 3-6, chapter A, ...)</font></font><td><input type='text' name='VolumeInfo' size=100 value='".$volinfo."' maxlength=500/>
<tr><td><font face=arial size=3><b>Year of Issue</b></font><td><input type='text' name='Year' size=10 value='".$year."' maxlength=10/>
<tr><td><font face=arial size=3><b>Edition</b></font><td><input type='text' name='Edition' size=10 value='".$edition."' maxlength=50/>
<tr><td><font face=arial size=3><b>Series</b> <font size=2 color=gray>(common title of book sequence)</font></font><td><input type='text' name='Series' size=100 value='".$series."' maxlength=300/>
<tr><td><font face=arial size=3><b>Publisher</b></font><td><input type='text' name='Publisher' size=50 value='".$publisher."' maxlength=200/>
<tr><td><font face=arial size=3><b>Number of Pages</b></font><td><input type='text' name='Pages' size=5 value='".$pages."' maxlength=10/>
<tr><td><font face=arial size=3><b>Language</b> <font size=2 color=gray>(Russian, English, ...)</font></font><td><input type='text' name='Language' size=50 value='".$language."' maxlength=50/>
<tr><td><font face=arial size=3><b>Identifier</b> <font size=2 color=gray>(ISBN 1234567890, ISSN ...)</font></font><td><input type='text' name='Identifier' size=20 value='".$identifier."' maxlength=100/>
<tr><td><font face=arial size=3><b>Library</b> <font size=2 color=gray>(kolxoz, homelab, mexmat, ...)</font></font><td><input type='text' name='Library' size=10 value='".$library."' maxlength=50/>
<tr><td><font face=arial size=3><b>Issue</b> <font size=2 color=gray>(DVD-, release number, ...)</font></font><td><input type='text' name='Issue' size=5 value='".$issue."' maxlength=10/>
<tr><td><font face=arial size=3><b>Orientation</b> <font size=2 color=gray>(landscape, portrait)</font></font><td><input type='text' name='Orientation' size=15 value='".$orientation."' maxlength=50/>
<tr><td><font face=arial size=3><b>DPI</font></font><td><input type='text' name='DPI' size=5 value='".$dpi."' maxlength=6/>
<tr><td><font face=arial size=3><b>Color</b> <font size=2 color=gray>(yes, no)</font></font><td><input type='text' name='Color' size=10 value='".$color."' maxlength=50/>
<tr><td><font face=arial size=3><b>Cleaned</b> <font size=2 color=gray>(yes, no)</font></font><td><input type='text' name='Cleaned' size=10 value='".$cleaned."' maxlength=50/>
<tr><td><font face=arial size=3><b>Commentary</b> <font size=2 color=gray>(5000 characters max)</font></font><td><input type='text' name='Commentary' size=100 value='".$commentary."' maxlength=5000/>
<tr><td><font face=arial size=3><b>MD5 of a Better Version</b> <font size=2 color=gray>(if known)</font></font><td><input type='text' name='Generic' size=35 value='".$generic."' maxlength=32/>

<tr><td><font face=arial size=3 color=gray><b>Filesize</b> <font size=2>(bytes)</font></font><td><input readonly type='text' name='Filesize' size=10 value='".$filesize."' maxlength=20/>
<tr><td><font face=arial size=3 color=gray><b>MD5</b></font><td><input readonly type='text' name='MD5' size=35 value='".$md5."' maxlength=32/>
<tr><td><font face=arial size=3 color=gray><b>File Extension</b></font><td><input readonly type='text' name='Extension' size=10 value='".$fileext."' maxlength=50/>
<tr><th colspan=2><input type='submit' value='Register!'/>
</table>

<input type='hidden' name='Edit' value='".$editing."'/>
</form>".$htmlfoot;

	// add new record, edit if already exists
	if ($_POST['Form'] == 1){
		if ($editing){
			echo $regform;
		} else {
			// save from the cache to the temporary directory (otherwise it might be automatically wiped);
			// to be copied to the repository in case of successful database registration (follows after this step)
			$tmp=str_replace('\\','/',getcwd().'/'.$tmpdir);
			@mkdir($tmp,0777,true);
			$saveto = "{$tmp}/{$md5}";
			if(copy($_FILES['uploadedfile']['tmp_name'],$saveto)) {
				echo $regform;
			} else {
				echo $htmlhead."<font color='#A00000'><h1>Upload failed</h1></font>There was an error uploading the file. Please try again!".$htmlfoot;
			}
		}
	}

	// edit, if MD5 found
	if ($_POST['Form'] == 2){
		if ($editing) echo $regform;
		else echo $htmlhead."<font color='#A00000'><h1>Book not found</h1></font>There is no such book in the database.<br>You are welcome to upload this piece!<p><a href='registration.php'>Go back to the upload page</a><p><h2>Thank you!</h2>".$htmlfoot;
	}
?>
