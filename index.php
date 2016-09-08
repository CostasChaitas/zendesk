<?php
  require('config.php');
  require("curl.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Zendesk Tickets</title>
</head>
<body>

<?php

  $stmt = $db->query('SELECT id, fullname, email, status, deadline, ticketId, autocomplete ,changed FROM orders WHERE status = "incomplete" AND checked = "no"');
  $rows = $stmt->fetchALL();

  foreach($rows as $row) {

      $checked = "yes";
      $stmt = $db->prepare('UPDATE orders SET checked = :checked WHERE id = :id') ;
      $stmt->execute(array(
        ':id' => $row['id'],
        ':checked' => $checked
      ));

      $dead = strtotime($row["deadline"]);
      $now = strtotime(date("Y/m/d"));
      $secs = $dead - $now;// == <seconds between the two times>
      $days = $secs / 86400;


      if($days>14){
        $create = json_encode(array(
          'ticket' => array(
            'subject' => "O_". $row["deadline"] . "_ID[" . $row['id'] . "]",
            'comment' => array( "value"=> "The order status is " . $row["status"] . " the autocomplete field is TRUE and the Ticket ID is: " . $row["ticketId"]) ,
            'requester' => array('name' => $row["fullname"], 'email' => $row['email']),
            "status" => "new",
            "group_id" => "25434289",
            "priority" => "normal"
        )));
      }else{
        $create = json_encode(array(
          'ticket' => array(
            'subject' => "O_". $row["deadline"] . "_ID[" . $row['id'] . "]",
            'comment' => array( "value"=> "The order status is " . $row["status"] . " and the Ticket ID is: " . $row["ticketId"]),
            'requester' => array('name' => $row["fullname"], 'email' => $row['email']),
            "status" => "new",
            "group_id" => "25434289",
            "priority" => "urgent"
        )));
      }
      $return = curlWrap("/tickets.json", $create, "POST");


      $ticketId = $return->ticket->id;
      $stmt = $db->prepare('UPDATE orders SET ticketId = :ticketId WHERE id = :id');
      $stmt->execute(array(
        ':id' => $row['id'],
        ':ticketId' => $ticketId
      ));


      echo '<div>';
        echo '<h1>'. "The order ID is: " . $row['id'] . '</h1>';
        echo "<h2>". "The name is " . $row["fullname"]. "</h2>";
        echo '<p>'. "The email is " . $row['email'].'</p>';
        echo '<p>'. "The orders status is " . $row["status"]. '</p>';
        echo '<p>'. "The deadline is " . $row['deadline'].'</p>';
        echo '<p>'. "The ticketId is " . $row['ticketId'].'</p>';
      echo '</div><hr>';

  }

?>

<?php

$stmt = $db->query('SELECT id, fullname, email, status, deadline, ticketId, autocomplete, changed FROM orders');
$rows = $stmt->fetchALL();

foreach($rows as $row) {

  if($row["changed"]==1){
    if($row["status" == "complete"]){
      $body = "The order status with ID " . $row["id"] . " and Ticket ID " . $row["ticketId"] .  " changed to " . $row["status"];
      $payload = array('ticket' => array('comment' => array('body' => $body), 'status' => "solved", "priority" => "low","group_id" => "25434289"));
      $json = json_encode($payload);
      $ticketid = $row["ticketId"];
      $url = "/tickets/" . $ticketid . ".json";
      $data = curlWrap($url, $json, "PUT");
      echo '<p>' . $body . '</p>';

      $stmt = $db->prepare('UPDATE orders SET changed = :changed WHERE id = :id') ;
      $stmt->execute(array(
        ':id' => $row['id'],
        ':changed' => 0
      ));
    }
  }
}

?>

<?php
/*
    $data = curlWrap("/groups.json", null, "GET");
    print "<pre>";
    print_r($data);
    print "</pre>";
*/
?>

<!--
    //Searching for user by email
    //$data =curlWrap("/search.json?query=type:user+email:$user_email", $json, "GET");


    $body = "This is the ticket body";
    $payload = array('ticket' => array('comment' => array('body' => $body)));
    $json = json_encode($payload);
    $ticketid = 23737;
    $url = "/tickets/" . $ticketid . ".json";
    $data = curlWrap($url, $json, "PUT");
    echo '<br />';
    print_r($data);
-->




<!--
<script>
//refresh the page every
setTimeout(function () { window.location.reload(); }, 30*1000);
// just show current time stamp to see time of last refresh.
document.write(new Date());
</script>
-->

</body>
</html>
