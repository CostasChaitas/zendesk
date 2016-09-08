<?php require("curl.php"); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form</title>

    <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
  </head>
  <body>

    <div class="container">
      <h3 class="text-center">Contact Form Zendesk Example</h3><hr />
      <form id="zFormer" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="former">
      	<div class="form-group col-md-offset-3" > <!-- Name field -->
      		<label class="control-label labels "  for="z_name">Name</label>
      		<input class="form-control inputs" id="z_name" name="z_name" type="text"/>
      	</div>

      	<div class="form-group col-md-offset-3"> <!-- Email field -->
      		<label class="control-label requiredField labels" for="z_requester">Email</label>
      		<input class="form-control inputs" id="z_requester" name="z_requester" type="text"/>
      	</div>

        <div class="form-group col-md-offset-3"> <!-- ProblemType field -->
          <label class="control-label labels" for="z_type">Problem Type</label>
          <select name ="z_type" id="z_type" class="form-control inputs">
            <option value="25563525">Contact</option>
            <option value="25434289">Order</option>
          </select>
        </div>

      	<div class="form-group col-md-offset-3"> <!-- Subject field -->
      		<label class="control-label labels" for="z_subject">Subject</label>
      		<input class="form-control inputs" id="z_subject" name="z_subject" type="text"/>
      	</div>

      	<div class="form-group col-md-offset-3"> <!-- Message field -->
      		<label class="control-label labels" for="z_description">Message</label>
      		<textarea class="form-control inputs" cols="40" id="z_description" name="z_description" rows="10"></textarea>
      	</div>



      	<div class="form-group">
      		<button class="btn btn-primary center-block" name="submit" type="submit" id="submitter">Submit</button>
      	</div>
      </form>
  </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
  </body>
</html>



<?php
if (isset($_POST["submit"])) {

  foreach($_POST as $key => $value){
  	if(preg_match('/^z_/i',$key)){
  		$arr[strip_tags($key)] = strip_tags($value);
  	}

  }

  $create = json_encode(array('ticket' => array(
    'subject' => $arr['z_subject'],
    'comment' => array( "value"=> $arr['z_description']),
     'requester' => array('name' => $arr['z_name'], 'email' => $arr['z_requester']),
     "group_id" => $arr["z_type"]
   )));
  $return = curlWrap("/tickets.json", $create, "POST");


}

?>
