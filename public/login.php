<?php

  // configuration
  require("../includes/config.php");

  // if form was submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {

    // validate submission
    if (empty($_POST["username"]))
    {
      apologize("You must provide your username.");
    }
    else if (empty($_POST["password"]))
    {
      apologize("You must provide your password.");
    }


    // query database for user
    $rows = query("SELECT * FROM user WHERE user_name = ?", $_POST["username"]);

    // if we found user, check password
    if (count($rows) == 1)
    {
      // first (and only) row
      $row = $rows[0];

      // compare hash of user's input against hash that's in database
      if (crypt($_POST["password"], $row["user_password"]) == $row["user_password"])
      {
        // remember that user's now logged in by storing user's ID in session
        $_SESSION["id"] = $row["user_id"];

        header("Location: /portfolio.php"); /* Redirect browser */
        exit();
      }
    }
    else
    {
      // else apologize
      apologize("Invalid username and/or password.");
    }
  }
  else
  {
    // render login page
    render("login_form.php", ["title" => "Log In"]);
  }

?>
