<html>
<meta name="keywords" content="">
<body bgcolor=yellow>
<h2 align=center>Results</h2>
<?
// Select data from nflpicks database and return to user

//Use guest account on db "nflpicks". Guest has only "SELECT" priveledges.
$username="guest";
$password="";
$host="simmons.db";
$database="nflpicks";

//Gather posted data in easy-to-reference variables
$year = $_POST["year"];
$week = $_POST["week"];
$pick = $_POST["pick"];
$home = $_POST["home"];
$opp = $_POST["opp"];
$lineopr = $_POST["lineopr"];
$line = $_POST["line"];
$result = $_POST["result"];

//echo $year;
//echo $week;
//echo $pick;
//echo $home;
//echo $opp;
//echo $lineopr;
//echo $line;
//echo $result;

// Open connection and select db
mysql_connect($host,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");



//Initial query string. Filter criteria will be appended as necessary
$query="Select * from picks";
//Intial string for checking your query. This string will remind the user
// which criteria he used to search the database.
$show="Here are all the results";
// If firstflag==1, append ' where ' before appending variable to query
$firstflag = 1;

//Process inputs into a valid sql query. If input is '*' do  nothing, Otherwise,
// if firstflag is set, append " where " and set $firstflag=0; Then append variable

if ($year!="*") { 
	if ($firstflag==1) {
		$query=$query." where year=".$year;
		$show=$show." from ".$year;
		$firstflag=0;
	}
	else {
		$query=$query." and year=".$year;
		$show=$show." and from ".$year;
	}
}
if ($week!="*") {
	if ($firstflag==1) {
		$query=$query." where week=".$week;
		$show=$show." from week ".$week;
		$firstflag=0;
	}
	else {
		$query=$query." and week=".$week;
		$show=$show." week ".$week;
	}
}
if ($pick!="*") {
	if ($firstflag==1) {
		$query=$query." where pick='".$pick."'";
		$show=$show." where Simmons picked the ".$pick;
		$firstflag=0;
	}
	else {
		$query=$query." and pick='".$pick."'";
		$show=$show." and where Simmons picked the ".$pick;
	}
}
if ($home!="*") {
	if ($firstflag==1) {
		$query=$query." where home='".$home."'";		
		$show=$show." where Simmons picked";
		$firstflag=0;
	}	
	else {
		$query=$query." and home='".$home."'";
		//Need to display different text if sorting by pick
		if ($pick!="*") {$show=$show." as";}
		else {};
	}
	// Need to display different text depending on whether Simmons picked
	// home or road team
	if ($home=="y") {$show=$show." the home team";}
	else {$show=$show." the road team";}
}
if ($opp!="*") {
	if ($firstflag==1) {
		$query=$query." where opp='".$opp."'";
		$show=$show." where Simmons picked against the ".$opp;
		$firstflag=0;
	}
	else {
		$query=$query." and opp='".$opp."'";
		$show=$show." and where Simmons picked against the ".$opp;
	}
}
// It's a little different for $lineopr and $line: process $lineopr and $line
// into one variable for mysql syntax. First check $lineopr to see if user wants
// to filter his search with the line. If not set $line to '*' for mysql. Then
// proceed as usual.

if ($lineopr!="*") {
	//Validate that $line is a float or die.
	if (filter_var($line, filter_id("float"))==FALSE) {
		// hack solution since the float filter doesn't recognize "0" as a float
		if ($line!=0){
		die("Unable to parse submission. Please enter a number for the line.");  }
	}
	//Append $lineopr to to $line for well-formed mysql query
	$newline=$lineopr.$line;
	//Get $line absolute value, since we'll be taking out the negative through
	// written language
	$absline=abs($line);
	//Then continue appending to $query as with the other variables. We want to
	// use $ line for $show appending, not $newline, because we'll use words to
	// represent the various scenarios rather than >/</=.
	if ($firstflag==1) {
		$query=$query." where line".$newline;
		$show=$show." where";
		$firstflag=0;
	}
	else {
		$query=$query." and line".$newline;
		$show=$show." and where";
	}
	//Figure out what to append to $show depending on $lineopr and $line
	if ($line==0) //user filters by Simmons picking favorite or underdog
	{
		if($lineopr==">") {$show=$show." Simmons picked the underdog";}
		else if($lineopr=="<") {$show=$show." Simmons picked the favorite";}
		else $show=$show." the game was a PK";
	}
	else if ($line>0) //user filters by how many points Simmons got
	{
		if($lineopr==">") {$show=$show." Simmons got at least ".$absline. "points";}
		else if($lineopr=="<") {$show=$show." Simmons got fewer than ".$absline." points";}
		else $show=$show." Simmons got exactly ".$absline." points";
	}
	else if ($line<0) // user filters by how many points Simmons gave
	{
		if($lineopr==">") {$show=$show." Simmons gave fewer than ".$absline." points";}
		else if($lineopr=="<") {$show=$show." Simmons gave more than ".$absline." points";}
		else $show=$show." Simmons gave exactly".$absline." points";
	}
	
}
if ($result!="*") {
	if ($firstflag==1) {
		$query=$query." where result='".$result."'";
		$show=$show." where";
		$firstflag=0;
	}
	else {
	$query=$query." and result='".$result."'";
	$show=$show." and where";
	}
	// Need to modify the text depending on whether Simmons won, lost, or pushed
	if ($result=="w") {$show=$show." Simmons picked correctly";}
	if ($result=="l") {$show=$show." Simmons picked incorrectly";}
	if ($result=="push") {$show=$show." Simmons pushed";}		
}

//Append a colon on to database query response string and display it.
$show=$show.":";
echo "<h5 align=center> $show </h5>";

//mysql query should be fully formed in $query at this point.
//Send $query to db and save
$response=mysql_query($query);
//echo "($query)";
//Now we need to process $response into pieces for display
//If the query didn't return anything, quit.
if (@mysql_numrows($response)==false) {echo ("<p align=center>
No entries matched your query.</p>");}

//Otherwise print out matching rows
else{
//Count the rows in the response
$num=mysql_numrows($response);
//echo $num;
//Don't need the database anymore
mysql_close();

?>
<! Format the data in an html table>
<table border="0" cellspacing="2" cellpadding="2"  align=center>
<tr><th>Year</th><th>Week</th><th>Simmons Pick</th>
<th>Pick Home?</th><th>Opponent</th>
<th>Spread</th><th>Simmons Result</th></tr>

<?

// Loop one row in the table for each record returned; store records
$i=0;
// Store Simmons' wins, losses, and pushed for the search
$wins=0;
$losses=0;
$pushes=0;

while ($i < $num) {

	$year=mysql_result($response,$i,"year");
	$week=mysql_result($response,$i,"week");
	$pick =mysql_result($response,$i,"pick");
	$home=mysql_result($response,$i,"home");
	$opp=mysql_result($response,$i,"opp");
	$line=mysql_result($response,$i,"line");
	$result=mysql_result($response,$i,"result");
?>
<tr>
<td align=center><? echo $year; ?></td>
<td align=center><? echo $week; ?></td>
<td align=center><? echo $pick; ?></td>
<td align=center><? echo $home; ?></td>
<td align=center><? echo $opp; ?></td>
<td align=center><? echo $line; ?></td>
<td align=center><? echo $result; ?></td>
<? 
// Increment the relevant result counter
if($result=='w') {$wins++;}
if($result=='l') {$losses++;}
if($result=='push') {$pushes++;}
?>
</tr>

<?
$i++;
}

echo "</table>";

// Print Simmons record for this dataset
echo "<p align=center>(For this dataset, Simmons had $wins win(s), $losses loss(es),
and $pushes push(es) in $num games.)</p>";

} // end of else (the one that checks for any rows to output)
?>


<p align=center><a href="simmons.html">Another query?</a></p>
<hr>
<footer> <p align=center>
<i>All content &copy; Kevin Connor 2009. Isn't this an ugly yellow? </i>
<a href="../index.html">Home</a></p></footer>
</body>
</html>

