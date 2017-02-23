<?php

  /**
   * functions.php
   *
   * WITH CODE FROM CS50
   * Helper functions.
   */


  /**
   * Renders template, passing in values.
   */
  function render($template, $values = [])
  {
    // if template exists, render it
    if (file_exists("../templates/$template"))
    {
      // extract variables into local scope
      extract($values);

      // render header
      require("../templates/header.php");

      // render template
      require("../templates/$template");

      // render footer
      require("../templates/footer.php");
    }

    // else err
    else
    {
      trigger_error("Invalid template: $template", E_USER_ERROR);
    }
  }


   /**
   * Apologizes to user with message.
   */
  function apologize($message)
  {
    render("apology.php", ["message" => $message]);
    exit;
  }


   /**
   * Redirects user to destination, which can be
   * a URL or a relative path on the local host.
   *
   * Because this function outputs an HTTP header, it
   * must be called before caller outputs any HTML.
   */
  function redirect($destination)
  {
    // handle URL
    if (preg_match("/^https?:\/\//", $destination))
    {
      header("Location: " . $destination);
    }

    // handle absolute path
    else if (preg_match("/^\//", $destination))
    {
      $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
      $host = $_SERVER["HTTP_HOST"];
      header("Location: $protocol://$host$destination");
    }

    // handle relative path
    else
    {
      // adapted from http://www.php.net/header
      $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
      $host = $_SERVER["HTTP_HOST"];
      $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
      header("Location: $protocol://$host$path/$destination");
    }

    // exit immediately since we're redirecting anyway
    exit;
  }


  /**
   * Executes SQL statement, possibly with parameters, returning
   * an array of all rows in result set or false on (non-fatal) error.
   */
  function query(/* $sql [, ... ] */)
  {
    // SQL statement
    $sql = func_get_arg(0);

    // parameters, if any
    $parameters = array_slice(func_get_args(), 1);

    // try to connect to database
    static $handle;
    if (!isset($handle))
    {
      try
      {
        // connect to database
        $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

        // ensure that PDO::prepare returns false when passed invalid SQL
        $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      }
      catch (Exception $e)
      {
        // trigger (big, orange) error
        trigger_error($e->getMessage(), E_USER_ERROR);
        exit;
      }
    }

    // prepare SQL statement
    $statement = $handle->prepare($sql);
    if ($statement === false)
    {
      // trigger (big, orange) error
      trigger_error($handle->errorInfo()[2], E_USER_ERROR);
      exit;
    }

    // execute SQL statement
    $results = $statement->execute($parameters);

    // return result set's rows, if any
    if ($results !== false)
    {
      return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
      return false;
    }
  }


  /**
   * Starts an SQL transaction
   * returns database handle if successful or false on (non-fatal) error.
   */
  function start_transaction()
  {

    // try to connect to database
    static $handle;
    if (!isset($handle))
    {
      try
      {
        // connect to database
        $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

        // ensure that PDO::prepare returns false when passed invalid SQL
        $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      }
      catch (Exception $e)
      {
        // trigger (big, orange) error
        trigger_error($e->getMessage(), E_USER_ERROR);
        exit;
      }
    }

    // begin SQL transaction
    if (($handle->beginTransaction()) === true)
    {
      return $handle;
    }
    else
    {
      return false;
    }
  }

  /**
   * Ends (commits) an SQL transaction
   * returns true if successful or false on (non-fatal) error.
   */
  function commit_transaction($handle)
  {

    // try to connect to database
    if (!isset($handle))
    {
      try
      {
        // connect to database
        $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

        // ensure that PDO::prepare returns false when passed invalid SQL
        $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      }
      catch (Exception $e)
      {
        // trigger (big, orange) error
        trigger_error($e->getMessage(), E_USER_ERROR);
        exit;
      }
    }

    // begin SQL transaction
    if (($handle->commit()) === true)
    {
      return true;
    }
    else
    {
      return false;
    }
  }


  /**
   * Logs out current user, if any. Based on Example #1 at
   * http://us.php.net/manual/en/function.session-destroy.php.
   */
  function logout()
  {
    // unset any session variables
    $_SESSION = [];

    // expire cookie
    if (!empty($_COOKIE[session_name()]))
    {
      setcookie(session_name(), "", time() - 42000);
    }

    // destroy session
    session_destroy();
  }



  /**
  * Gets portfolio on a given date
  */
  function get_portfolio($portfolio_date)
  {
    // retrieve user's portfolio from database
    $rows = query("SELECT t1.asset_id, asset_name, asset_currency, asset_type, SUM(asset_quantity) AS asset_quantity, t3.asset_price, t3.asset_price_date, asset_irr
            FROM dbinvestments.transaction t1 JOIN dbinvestments.asset t2 ON t1.asset_id = t2.asset_id
                             JOIN dbinvestments.asset_price t3 ON t1.asset_id = t3.asset_id
                             JOIN dbinvestments.asset_return t4 ON t1.asset_id = t4.asset_id
            WHERE t1.user_id = ? AND asset_price_date = ?
            GROUP BY asset_id
            ORDER BY asset_type ASC, asset_name ASC",
            $_SESSION["id"],
            $portfolio_date);

    if ($rows === false)
    {
      apologize("Error in database. Please try again.");
      exit;
    }

    return $rows;
   }

  /**
  * Gets portfolio broken down by asset type (equity, fixed income, etc...)
  */
function get_portfolio_by_asset_type($portfolio_date)
  {
    // retrieve user's portfolio from database
    $rows = query("SELECT t4.asset_type, SUM(t4.asset_total) AS type_total FROM
            (SELECT t1.asset_id, asset_type, SUM(asset_quantity)* t3.asset_price AS asset_total
            FROM dbinvestments.transaction t1 JOIN dbinvestments.asset t2 ON t1.asset_id = t2.asset_id JOIN dbinvestments.asset_price t3 ON t1.asset_id = t3.asset_id
            WHERE t1.user_id = ? AND asset_price_date = ?
            GROUP BY asset_id) t4
            GROUP BY asset_type;",
            $_SESSION["id"],
            $portfolio_date);

    if ($rows === false)
    {
      apologize("Error in database. Please try again.");
      exit;
    }

    return $rows;
   }

  /**
  * Gets portfolio broken down by asset currency
  */

function get_portfolio_by_currency($portfolio_date)
  {
    // retrieve user's portfolio from database
    $rows = query("SELECT t4.asset_currency, SUM(t4.asset_total) AS currency_total FROM
            (SELECT t1.asset_id, asset_currency, SUM(asset_quantity)* t3.asset_price AS asset_total
            FROM dbinvestments.transaction t1 JOIN dbinvestments.asset t2 ON t1.asset_id = t2.asset_id JOIN dbinvestments.asset_price t3 ON t1.asset_id = t3.asset_id
            WHERE t1.user_id = ? AND asset_price_date = ?
            GROUP BY asset_id) t4
            GROUP BY asset_currency;",
            $_SESSION["id"],
            $portfolio_date);

    if ($rows === false)
    {
      apologize("Error in database. Please try again.");
      exit;
    }

    return $rows;
   }


  /**
  * Get latest date where prices are available for all assets
  */
  function get_latest_pricing_date()
  {
    // gets latest (most updated) pricing date for all assets in the database
    $rows = query("SELECT MIN(t.max_date) as max_date FROM (SELECT asset_id, MAX(asset_price_date) as max_date FROM asset_price GROUP BY asset_id) t");

    if ($rows === false)
    {
      apologize("Error in database. Please try again.");
      exit;
    }

    // return only date
    return $rows[0]["max_date"];

  }


  /**
  * Calculate portfolio total value
  */
  function calculate_portfolio_total($positions)
  {

    if ($positions === false)
    {
      apologize("Cannot calculate portfolio total. Please try again.");
      exit;
    }

    $total = 0;

    // Calculate total value of positions held
    foreach($positions as $position)
    {
      $total = $total + $position["asset_price"]*$position["asset_quantity"];
    }

    // return only date
    return $total;
  }

 ?>
