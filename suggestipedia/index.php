<html>

<head>
<title> favorites / bookmark title goes here </title>
</head>

<body bgcolor="white" text="blue">

<h1> My first page </h1>
<?php
$source = file_get_contents('http://en.wikipedia.org/w/api.php?action=opensearch&search=ar&namespace=0&suggest');
echo $source;

?>
</body>

</html>