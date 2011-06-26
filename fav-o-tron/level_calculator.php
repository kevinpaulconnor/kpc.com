<?php
// level_calculator.php
// Kevin Connor 2/2010
/* 	level_calculator operates on the Metafilter favorites database. It iterates
	through table favorites_data, recording the number of times a particular user
	has favorited the content of another user, and records this information in table
	favorite_level. favorite_level will be accessed by the client-facing script to
	return color coded results about metafilter relationships.
	This should save time: most of the sql calls will be done here late at night
	when the data dump is updated, improving end user performance
*/

//Use favorite account on db "favorites". Guest has only "SELECT and INSERT" priveledges.
$username="favorite";
$password="";
$host="simmons.db";
$database="favorites";

// graceful mysql failure
function showerror(  )
   {
      die("Error " . mysql_errno(  ) . " : " . mysql_error(  ));
   }

$start_id = $_GET["start_id"];
echo "processing id=".$start_id."<br>";

// Open connection and select db
mysql_connect($host,$username,$password);
@mysql_select_db($database) or die ("Unable to select database");

//let's toss out the old level data if we're starting from the first id
if ($start_id == 1) {
	$drop_query = "delete from favorite_level";
	if (!($drop_response = mysql_query($drop_query))) { showerror( );}
}

// If we're given the only
if (!($start_id == NULL)) {
// grab next user id to pass for meta tag refresh
$id_query = "SELECT user_id FROM user_data WHERE user_id >".$start_id."
	ORDER BY user_id ASC LIMIT 1";
if (!($id_response = mysql_query($id_query))) { showerror( ); };
$row = mysql_fetch_row($id_response);
$next_user_id = $row[0];
echo "next_user_id=".$next_user_id."<br>";
}
//If we've forgotten where we are in the process, input Null,
// and the script will tell you
else {

	

	$where_query = "SELECT max(faver) FROM favorite_level";
	if (!($where_response = mysql_query($where_query))) { showerror( ); };
	$row = mysql_fetch_row($where_response);
	$where_id = $row[0];
	if ($where_id == NULL) { echo "Nothing in database. Start with #1";}
	else { 
	
		$id_query = "SELECT user_id FROM user_data WHERE user_id >".$where_id."
		ORDER BY user_id ASC LIMIT 1";
		if (!($id_response = mysql_query($id_query))) { showerror( ); };
		$row = mysql_fetch_row($id_response);
		$next_user_id = $row[0];
		
		echo "Highest Id in database is ".$where_id.". Try starting with "
		.$next_user_id; }
	return;
}


// let's give back that SQL call memory
mysql_free_result($id_response); 

//set up data structures for the user id iteration
/*
// standard query
$iteration_base = "select favee from favorites_data where faver=";
//holds mysql result
$iteration_query;

$user_count = count($user_ids);
echo $user_count;
//will hold the number of favees by each faver; $level[faver_id][favee_id]
// will equal the number of favorites for that relationship when we're finished
// So first we'll have to set each of those values to zero, in this loop.
// this would be a good job for a class constructor...
*/
$level = array();
$total_counter = 0;
$user_count = 0;

//ok, let's iterate over all user ids and fill out our level table
$iteration_query = "select favee from favorites_data where faver=".$start_id;
if(!($iteration_response = mysql_query($iteration_query))) { showerror (); }

// this loop gives us each favee favorited by the faver in question, in turn
while ($row = mysql_fetch_row($iteration_response)) {
	if ($level[$row[0]] == NULL) { $level[$row[0]] = 1; }
		else { $level[$row[0]]++; }
		$total_counter++;
		if ($row[0] > $user_count) { $user_count = $row[0]; }
	}


//now, we need to insert our level results into our db table
$insertion_base = "insert into favorite_level values(";

$user_count = 999999;
echo "usercount = ".$user_count;
for ($k=0; $k<=$user_count;$k++) {
	if (!($level[$k] == NULL)) {
		$insertion_query = $insertion_base.$start_id.", ".$k.", ".$level[$k].")";
		echo "insertion_query=".$insertion_query."<br>";
		if(!($insertion_response = mysql_query($insertion_query))) { showerror (); }
	}
	
}

echo "total_counter=".$total_counter."<br>";
$start_id++; 

echo "Done.";

?>
<<meta http-equiv="refresh" content=
".5;url=level_calculator.php?start_id=<?php echo $next_user_id?>">
