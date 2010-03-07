<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf8">
  <title>Search ISBN</title>
 </head>
 <body>

<?php
include 'config.php';

if (isset($_POST['amazon'])) {

    $number = htmlspecialchars($_POST['isbn'], ENT_QUOTES);

    if (strlen($number) != 10) {

        require_once 'ISBN-0.1.6/ISBN.php';
        $number = ISBN::convert($number, ISBN::validate($number), ISBN_VERSION_ISBN_10);

    }

    $isbn = $number;

    include 'amazonRequest.php';
    $amazonInfo = amazonInfo($isbn, $public_key, $private_key);
    $amazonError = $amazonInfo['error'];
    if ($amazonError == '') {

        $title = htmlspecialchars($amazonInfo['Title'], ENT_QUOTES);
        $author = htmlspecialchars($amazonInfo['Author'], ENT_QUOTES);
        $year = htmlspecialchars($amazonInfo['Year'], ENT_QUOTES);
        $publisher = htmlspecialchars($amazonInfo['Publisher'], ENT_QUOTES);
        $edition = htmlspecialchars($amazonInfo['Edition'], ENT_QUOTES);
        $pages = htmlspecialchars($amazonInfo['Pages'], ENT_QUOTES);
        $identifier = 'ISBN ' . htmlspecialchars($amazonInfo['ISBN'], ENT_QUOTES);
        $language = htmlspecialchars($amazonInfo['Language'], ENT_QUOTES);
        $commentary = htmlspecialchars($amazonInfo['Content'], ENT_QUOTES);
        $image = htmlspecialchars($amazonInfo['Image'], ENT_QUOTES);

    }
}

if (isset($_POST['ozon'])) {


    $number = htmlspecialchars($_POST['isbn'], ENT_QUOTES);

    if (!(substr_count(trim($number), '-') == 3) && (strlen(trim($number)) == 13) ||
        !(substr_count(trim($number), '-') == 4) && (strlen(trim($number)) == 17)) {

        require_once 'ISBN-0.1.6/ISBN.php';
        $isbn = new ISBN($number);
        $number = substr($isbn->getISBNDisplayable(), 9);

    }

    $isbn = $number;

    include 'ozonRequest.php';
    $ozonError = $ozonInfo['error'];
    if ($ozonError == '') {

        $title = htmlspecialchars($ozonInfo['Title'], ENT_QUOTES);
        $author = htmlspecialchars($ozonInfo['Author'], ENT_QUOTES);
        $publisher = htmlspecialchars($ozonInfo['Publisher'], ENT_QUOTES);
        $year = htmlspecialchars($ozonInfo['Year'], ENT_QUOTES);
        $pages = htmlspecialchars($ozonInfo['Pages'], ENT_QUOTES);
        $identifier = 'ISBN ' . $isbn;
        $commentary = htmlspecialchars($ozonInfo['Content'], ENT_QUOTES);
        $topic = htmlspecialchars($ozonInfo['Topic'], ENT_QUOTES);
        $image = htmlspecialchars($ozonInfo['Image'], ENT_QUOTES);
        $image = str_replace("/small", "", $image);
        $image = str_replace(".gif", ".jpg", $image);

    }
}

//RGB
if (isset($_POST['rgb'])) {


    $number = htmlspecialchars($_POST['isbn']);

    if (!(substr_count(trim($number), '-') == 3) && (strlen(trim($number)) == 13) ||
        !(substr_count(trim($number), '-') == 4) && (strlen(trim($number)) == 17)) {

        require_once 'ISBN-0.1.6/ISBN.php';
        $isbn = new ISBN($number);
        $number = substr($isbn->getISBNDisplayable(), 9);

    }

    $isbn = $number;

    include 'rgbRequest.php';

}

$isbnForm = "<form action='" . $_SERVER["PHP_SELF"] . "' method='post' >
ISBN: <input type='text' name='isbn' size='20' maxlength='25' value='" .
    htmlspecialchars($_POST['isbn'], ENT_QUOTES) . "' />
search in: 
<input type='submit' value='Amazon' name='amazon'/>
<input type='submit' value='Ozon' name='ozon'/>
<input type='submit' value='RSL' name='rgb'/>
\t" . $amazonError . $ozonError . $rgbError . "</form>";

echo $isbnForm;


echo '<br /><br />'.
    '' . $author . ' - ' . $title . '[' . $year . ', ' . $language . ']' . '<br />' .
    '[img=right]' . $image . '[/img]<br />' .
    '[b]Название:[/b] ' . $title . '<br />' .
    '[b]Автор:[/b] ' . $author . '<br />' .
    '[b]Город:[/b] ' . $city . '<br />' .
    '[b]Издательство:[/b] ' . $publisher . '<br />' .
    '[b]Год:[/b] ' . $year . '<br />' .
    '[b]Издание:[/b] ' . $edition . '<br />' .
    '[b]Том:[/b] ' . $volumeninfo . '<br />' .
    '[b]Страниц:[/b] ' . $pages . '<br />' .
    '[b]ISBN:[/b] ' . $identifier . '<br />' .
    '[b]Язык:[/b] ' . $language . '<br />' .
    '[b]Жанр:[/b] ' . $topic . '<br />' .
    '[b]Формат:[/b] ' . '<br />' .
    '[b]УДК:[/b] ' . $udc . '<br />' .
    '[b]Серия:[/b]' . $series . '<br />'.
    '[b]Описание:[/b] ' . $commentary . '<br />' .
    '[b]Примеры страниц:[/b] [spoiler][/spoiler]' . '<br />';

?>


  
</body>
</html>