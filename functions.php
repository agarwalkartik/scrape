<?php
include 'database.php';

function refreshStats($tagName,$tagId)
{
  
  $ch = curl_init("http://stackoverflow.com/questions/tagged/".$tagName);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$cl = curl_exec($ch);

  
  

   $dom = new DOMDocument();
   @$dom -> loadHTML($cl);
   $xpath = new DOMXPath($dom);
   
   
   $data  = array();
   $tags = array();
   $numberOfQuestions = 0;
   
    $spaner = $xpath->query("//*[contains(@class, 'question-hyperlink')]"); 
    $spaner1 = $xpath->query("//*[contains(@class, 'vote-count-post')]"); 
    $spaner2 = $xpath->query("//*[contains(@class, 'views')]");
    $spaner3 = $xpath->query("//*[contains(@class, 'status')]");
  $spaner4 = $xpath -> query("//*[contains(@class, 'post-tag')]");
  
  
  

  
  
  $query = "Delete  from questionstats where tagId = '$tagId'";
  mysql_query($query) or die(mysql_error());
   foreach ($spaner as $node) {

  $link = $data[$numberOfQuestions]['link'] = mysql_escape_string($node->getAttribute('href'));
  $text = $data[$numberOfQuestions]['text'] = mysql_escape_string($node->nodeValue);
  $votes = $data[$numberOfQuestions]['votes'] = $spaner1->item($numberOfQuestions)->nodeValue;
    $views = $data[$numberOfQuestions]['views'] = trim($spaner2->item($numberOfQuestions)->nodeValue);
  $answers = $data[$numberOfQuestions]['answers'] = trim($spaner3->item($numberOfQuestions)->nodeValue);
  
  $query = "INSERT INTO `questionstats`(`questionLink`, `questionTitle`, `questionVotes`, `questionViews`, `questionAnswers`, `tagId`) VALUES ('$link','$text','$votes','$views','$answers','$tagId')";
  mysql_query($query) or die(mysql_error());
  
  $numberOfQuestions++;
  if($numberOfQuestions == 15) break;

    }
    
      $query = "Delete  from relevant_tags where tagId = '$tagId'";
  
  mysql_query($query) or die(mysql_error());
  foreach ($spaner4 as $node) {
    
    if($node->nodeValue!= $tagName)
    {
  $query = "INSERT INTO `relevant_tags`(`tagId`, `relevantTagName`) VALUES ('$tagId','$node->nodeValue')";
  
  mysql_query($query) or die(mysql_error());
    array_push($tags,$node->nodeValue);
    }
  }
  
  
   
  return;
}

function showtags()
{
	
$query = "select * from tags";
$result = mysql_query($query) or die(mysql_error());
$data = "<ul>";

while ($row = mysql_fetch_assoc($result)) {
	$data .= "<li><a href='tagstats.php?tagName=".urlencode($row['tag_name'])."&tagId=".$row['tag_id']."'>".$row['tag_name']."</a></li>";
}
$data .= "</ul>";
echo $data;
}

function relevantTagsData($tagId)
{

$tagdata = array();
 $query = "select DISTINCT(relevantTagName) as relevantTagName from relevant_tags where tagId = '$tagId'";
  $result = mysql_query($query) or die(mysql_error());
  $i=0;
  while ($row = mysql_fetch_assoc($result)) {
  
    $rTagName = $row['relevantTagName'];

    $query1 = "select count(*) from relevant_tags where tagId = '$tagId' and relevantTagName = '$rTagName'";
    $result1 = mysql_query($query1) or die(mysql_error());
    
    while ($row1 = mysql_fetch_assoc($result1)) {
       $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
    
      $tagdata[$i]['label'] = $rTagName;
      $tagdata[$i]['color'] =  $color;
      $tagdata[$i]['value']  = $row1['count(*)'] * 100;
      $i++;
  }
    
    
}
print_r($data);
return $data = json_encode($tagdata);

}


?>
