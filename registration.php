<?php
include 'html.php';

$page = "<center><table border=1 cellspacing=0 cellpadding=12 bordercolor='#A00000'>
<caption><font color='#A00000'><h1>Library Genesis</h1></font></caption>

<!-- File-Upload Form -->
<tr><td><form enctype='multipart/form-data' action='form.php' method='POST'>
<input type='hidden' name='MAX_FILE_SIZE' value='67108863'/>
<input type='hidden' name='Form' value='1'/>
<input type='hidden' name='MD5' value=''/>
Choose file to upload:<br><input name='uploadedfile' type='file' size=50/> <input type='submit' value='Send!'/><br><font face=Arial color=gray size=1>Calculates MD5 upon completion</font></td></tr>
</form>

<!-- MD5-check-up Form -->
<tr><td><form enctype='multipart/form-data' action='form.php' method='POST'>
<input type='hidden' name='Form' value='2'/>
Enter MD5 to look for:<br><input name='MD5' id='1' type='text' size=50 maxlength=32/> <input type='submit' value='Check MD5!'/>
<br><font face=Arial color=gray size=1>Helps avoid a tedious upload, if the book is already in the database</font></td></tr>

</form>
</table></center>";

echo $htmlheadfocus;
echo $page;
echo $htmlfoot;
?>
