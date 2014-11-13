<?php
include 'database.php';
$tagId = $_GET['tagId'];
$query = "select * from questionstats where tagId = '$tagId'";
	$result = mysql_query($query);
	$questionsdata = array();
$data = array('0'=>'Question No.' , '1'=> 'Votes' , '2' => 'Answers' , '3' => 'Views');
//array_push($questionsdata, $data);
	$totalviews = 0;
	$qNo = 1;
	while ($row = mysql_fetch_array($result)) {
		$str = "Question ".(string)$qNo++;
$data = array('Question'=> $str , 'Votes'=> $row['questionVotes'] , 'Answers' => $row['questionAnswers'] , 'Views' => $row['questionViews']);

	array_push($questionsdata, $data);
	
	}

	echo json_encode($questionsdata);
?>
