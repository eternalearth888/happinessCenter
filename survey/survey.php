<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Life Satisfaction Survey</title>
<meta name="Generator" content="Hand-coded by Sean Mahnken" />
<link rel="stylesheet" type="text/css" href="./survey.css" />

</head>

<body class="survey">
<div class="wrapper">
<span class="header">
  <img src="logo.jpg" alt="The Happiness Center" />
  <h1>Life Satisfaction Survey</h1>
</span>

<?php
  /* Open XML file and extract questions */
  $xmlDoc = new DOMDocument();
  $xmlDoc->load( "surveyquestions.xml" );
  $root = $xmlDoc->documentElement;
  $qList = $xmlDoc->getElementsByTagName('question');
  $qCount = $qList->length;
  $q=array_fill( 0, $qCount, 0 );

  if ($_POST)
  {
    /* The page is being loaded via form post */
    for( $i=1; $i<=$qCount; $i++ )
      $q[$i-1] = $_POST["Q$i"];

    /* Make sure all the questions were answered */
    if ( in_array( 0, $q ) )
    {
      /* We found an unanswered question.  Display a message. */
      ?>
      <p class="error">Oops!  Some questions remain unanswered.  Please answer the questions marked
      in <span class="bold red">RED</span> and resubmit your survey.</p>
      <?php
    } else {
      /* All questions were answered */
      $score = array_sum($q) - $qCount;
      ?>
      <h2><center>--- RESULTS ---</center></h2>
      <span class="yourscore">Your score is: <?php echo $score; ?></span>
      <p class="result">There are two ways to interpret your score.  The first is your cumulative
      score, which gives you an indication of your overall sense of fulfillment and happiness in
      life:</p>

      <table class="scoring"><tr>
        <td colspan="2" class="title">Scoring</td></tr>
      <tr>
        <td class="score">81-100</td>
        <td class="desc">I am generally contented and happy in my life.  Feedback in specific areas
        might be useful.</td>
      </tr>
      <tr>
        <td class="score">61-80</td>
         <td class="desc">My life is okay, but not always what I would like it to be.  I could use
         some direction in making my life happier.</td>
      </tr>
      <tr>
        <td class="score">41-60</td>
        <td class="desc">My life is not going in a direction I would like it to go. I need guidance
        in learning how to find happiness.</td>
      </tr>
      <tr>
        <td class="score">40 & Under</td>
        <td class="desc">My life lacks fulfillment and joy.  P.S. Don't give up - this is a great
        opportunity for growth!</td>
      </tr></table>

      <p class="result">The second way to interpret your score has to do with individual areas,
      which are covered in the survey.  Research has shown that the twenty-five areas addressed
      in the questions are specific indicators which contribute to one's overall sense of
      happiness.  So, for example, if a score was less than four on a particular question, it
      shows room for improvement <i>in that specific area</i>.  The lower the score, the greater
      the opportunity for growth.</p>

      <form action="http://www.thehappinesscenter.com/" method="get">
        <input type=submit value="Return to main page">
      </form>

      <?php
    }
  }
  else
  {
    /* Display the header/intro paragraph */
    ?>
    <p>The insights learned from this Life Satisfaction Survey will give you an indication how
    happy you feel on your current life's path, and should take a maximum of 5 minutes.</p>
    <?php
  }

  /* The form.  This should be on every page. */
  ?>
  <hr />
  <p> Please answer the following questions, using the criteria below. Please choose the number
  which most closely fits how you feel at this time in your life:</p>
  <h3 class="legend">
  <pre><b>0</b> - Never feel this way        <b>1</b> - Rarely feel this way        <b>2</b> - Sometimes feel this way
         <b>3</b> - Often feel this way       <b>4</b> - Always feel this way</pre>
  </h3>

  <!-- The survey form -->
  <form action="survey.php" method="post">
  <table>
    <tr class="top">
      <td></td> <td><img src="scale.gif" alt="scale"></td>
    </tr>

  <?php
    $i=0;
    $selected = 'selected="selected"';
    foreach ($qList as $question )
    {
      echo '<tr class="';
      if ($_POST && $q[$i] == 0)
        echo 'unanswered';
      else
        echo ($i & 0x01) ? 'odd' : 'even';
      echo "\">\n<td> ".($i+1).") $question->textContent </td>\n";
      echo '<td><select name="Q'.($i+1)."\">\n";
      ?>
      <option value="0"> </option>
      <option value="1" <?php if ($q[$i]==1) echo $selected; ?> > 0) Never </option>
      <option value="2" <?php if ($q[$i]==2) echo $selected; ?> > 1) Rarely </option>
      <option value="3" <?php if ($q[$i]==3) echo $selected; ?> > 2) Sometimes </option>
      <option value="4" <?php if ($q[$i]==4) echo $selected; ?> > 3) Often </option>
      <option value="5" <?php if ($q[$i]==5) echo $selected; ?> > 4) Always </option>
      </select></td></tr>
      <?php
      $i++;
    }
  ?>

  </table>
  <input type="submit" value="Calculate Your Score">
  </form>
<!-- A "start over" button -->
<form><input type="submit" value="Start Over"></form>

<p class="footer">Copyright &copy; 2004-2008 <a href="http://www.TheHappinessCenter.com">
TheHappinessCenter.com</a> - All rights reserved</p>
</div>
</body>
</html>
