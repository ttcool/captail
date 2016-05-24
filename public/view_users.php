<?php # Script 9.6 - view_users.php #2
// This script retrieves all the records from the users table.

require ('includes/config.inc.php');
$page_title = 'View the Current Users';
include ('includes/header.html');

// Page header:
echo '<h1>Registered Users</h1>';

require (MYSQL); // Connect to the db.


$display = 10;
//计算显示页数
if (isset($_GET['P']) && is_numeric($_GET['P'])){
  $pages = $_GET['P'];
}else{
   //计算记录数
   $q = "SELECT COUNT(user_id) FROM users";
   $r = @mysqli_query($dbc,$q);
   $row = @mysqli_fetch_array($r,MYSQLI_NUM);
   $records = $row[0];
 
   //计算页数
   if ($records > $display){
        //多于一页
     $pages = ceil($records/$display);
   } else{
     $pages = 1;
  }
}

//计算数据库开始返回查询结果的位置
if (isset($_GET['s']) && is_numeric($_GET['s'])){
  $start = $_GET['s'];
 }else{
   $start = 0;   
}	

// Determine the sort...
// Default is by registration date.
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'rd';

// Determine the sorting order:
switch ($sort) {
	case 'ln':
		$order_by = 'last_name ASC';
		break;
	case 'fn':
		$order_by = 'first_name ASC';
		break;
	case 'rd':
		$order_by = 'registration_date ASC';
		break;
	default:
		$order_by = 'registration_date ASC';
		$sort = 'rd';
		break;
}

	
// Make the query:
$q = "SELECT last_name, first_name, DATE_FORMAT(registration_date, '%M %d, %Y') AS dr, user_id FROM users ORDER BY $order_by LIMIT $start, $display";
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.

	// Print how many users there are:
	echo "<p>There are currently $num registered users.</p>\n";

	// Table header.
	echo '<table align="center" cellspacing="3" cellpadding="3" width="75%">
	<tr>
          <td align="left"><b><a href="view_users.php?sort=ln">Last Name</a></b></td>
          <td align="left"><b><a href="view_users.php?sort=fn">First Name</a></b></td>
  	  <td align="left"><b><a href="view_users.php?sort=rd">Date Registered</a></b></td>
          <td align="left"><b>Edit</b></td>
          <td align="left"><b>Delete</b></td>
       </tr>
';
	
	// Fetch and print all the records:
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<tr>
                         <td align="left">' . $row['last_name'] . '</td>
                         <td align="left">' . $row['first_name'] . '</td>
                         <td align="left">' . $row['dr'] . '</td>
                         <td align="left"><a href="edit_user.php?id='.$row['user_id'].'">Edit</a></td>
                         <td align="left"><a href="delete_user.php?id='.$row['user_id'].'">Delete</a></td>
                     </tr>';
	}

	echo '</table>'; // Close the table.
	
	mysqli_free_result ($r); // Free up the resources.	

} else { // If no records were returned.

	echo '<p class="error">There are currently no registered users.</p>';

}

mysqli_close($dbc); // Close the database connection.

if ($pages > 1){
  echo '<br/><p>';

  //当前页
 $current_page = ($start/$display) + 1;

  //如果当前显示不是第一页，显示previous 
  if($current_page != 1){
     echo '<a href="view_users.php?s='.($start-$display).'$p='.$pages.'&sort='.$sort.'">Previous</a>';
}
  for($i=1;$i<=$pages;$i++){
    if($i != $current_page){
      echo '<a href="view_users.php?s='.(($display*($i-1))).'&p='.$pages.'&sort='.$sort.'">'.$i.'</a>';
}else{
  echo $i.'';
}
}
   if($current_page !=$pages){
      echo '<a href="view_users.php?s='.($start + $display).'&p='.$pages.'&sort='.$sort.'">Next</a>';
  }
   echo '</p>';
}
include ('includes/footer.html');
?>
