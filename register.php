<?php
	include 'connect.php';
	include 'html.php';

	// LOCK DB
	mysql_query('LOCK TABLE '.$dbtable.' WRITE');

	// compute into which folder the file should be dispatched
	$id = mysql_next_id($dbtable);
	$relativeID = $id % $modulobase;
	$savedir = $id - $relativeID;

	$relPath = $savedir.'/'.$_POST['MD5'];
	$repository = str_replace('\\','/',realpath($repository));

	// check correctness of numerical values (they cannot be equal to empty strings)
	// and some textual
	$year = $_POST['Year'];
	$pages = $_POST['Pages'];
	$issue = $_POST['Issue'];
	$dpi = $_POST['DPI'];
	$identifier = $_POST['Identifier'];

	if ($year == '') $year = 0;
	if ($pages == '') $pages = 0;
	if ($issue == '') $issue = 0;
	if ($dpi == '') $dpi = 0;
	if ($identifier == 'ISBN ') $identifier = '';

	// escape single quotes

	// check for proper MD5
	$generic = clean('Generic');
	if ($generic == '' || $generic == $_POST['MD5']) {
		$generic = '';
	} elseif (!preg_match('/^[A-Fa-f0-9]{32}$/',$generic)) {
		echo $htmlhead."<font color='#A00000'><h1>Wrong Hash Value</h1></font>The MD5 of a better book provided is not a valid MD5.<br>You can modify it later by using MD5 as the key.".$htmlfoot;
		$generic = '';
	}

	$topic = clean('Topic');
	$title = clean('Title');
	$author = clean('Author');
	$volinfo = clean('VolumeInfo');
	$publisher = clean('Publisher');
	$edition = clean('Edition');
	$identifier = clean('Identifier');
	$language = clean('Language');
	$library = clean('Library');
	$orientation = clean('Orientation');
	$color = clean('Color');
	$cleaned = clean('Cleaned');
	$commentary = clean('Commentary');
	$series = clean('Series');
	$udc = clean('UDC');
	$lbc = clean('LBC');

	// open file read-only and lock before SQL-query
	if (!$_POST['Edit']){
		$tmp=str_replace('\\','/',getcwd().'/'.$tmpdir);
		$file = $tmp.'/'.$_POST['MD5'];
		@$h = fopen($file,'r');
		if (!$h) die("Cannot open temporary file '".$file."'");

		if (!flock($h,LOCK_EX)) die("<p>Cannot lock temporary file '".$file."'");

		$sql="INSERT INTO $dbtable (ID,Topic,Author,Title,VolumeInfo,Year,Publisher,Edition,Identifier,Pages,Filesize,Issue,Orientation,DPI,Color,Cleaned,Language,MD5,Extension,Library,Commentary,Series,UDC,LBC) VALUES
		('$id','$topic','$author','$title','$volinfo','$year','$publisher','$edition','$identifier','$pages','$_POST[Filesize]','$issue','$orientation','$dpi','$color','$cleaned','$language','$_POST[MD5]','$_POST[Extension]','$library','$commentary','$series','$udc','$lbc')";
	} else {
		$sql="UPDATE $dbtable SET `Generic`='$generic',`Topic`='$topic',`Author`='$author',`Title`='$title',`VolumeInfo`='$volinfo',`Year`='$year',`Publisher`='$publisher',`Edition`='$edition',`Identifier`='$identifier',`Pages`='$pages',`Issue`='$issue',`Orientation`='$orientation',`DPI`='$dpi',`Color`='$color',`Cleaned`='$cleaned',`Language`='$language',`Extension`='$_POST[Extension]',`Library`='$library',`Commentary`='$commentary',`Series`='$series',`UDC`='$udc',`LBC`='$lbc' WHERE `MD5`='$_POST[MD5]' LIMIT 1";
	}

	if (!mysql_query($sql,$con))
		die('Error: ' . mysql_error());

	if (!$_POST['Edit']){
		$savedir = "{$repository}/{$savedir}";
		@mkdir($savedir,0777,true);
		$saveto = $savedir.'/'.$_POST['MD5'];

		if(!copy($file,$saveto))
			die("<p>There was an error copying file ".$file.".");
		chmod($saveto,0444);

		flock($h,LOCK_UN);
		fclose($h);

		@unlink($file);

		$sql="UPDATE $dbtable SET `Filename`='$relPath' WHERE `ID`='$id' LIMIT 1";
		if (!mysql_query($sql,$con))
			die('Error: ' . mysql_error());
	}

	mysql_query('UNLOCK TABLES');
	// UNLOCK DB

	echo $htmlhead."<font color='#A00000'><h1>Registration complete!</h1></font>Write down the uploaded book MD5:<br><font face='courier new'><b>$_POST[MD5]</b></font><p><h2>Thank you!</h2><a href=registration.php>Go back to the upload page</a>".$htmlfoot;
	mysql_close($con);

// removes unnecessary whitespace and tab characters
function clean($var){
	$c = "'\\";
	$str = str_replace("\t",' ',$_POST[$var]); // replace tabs
//   $str = preg_replace('/\s\s+/',' ',$str); // delete multiple spaces
	return trim(addcslashes($str,$c));
}

function mysql_next_id($dbtable) {
	$result = mysql_query("SHOW TABLE STATUS WHERE name='".$dbtable."'");
	$rows = mysql_fetch_assoc($result);
	return $rows['Auto_increment'];
}
?>
