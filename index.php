<?php
$title = "Home";
$emails_csv = "input_emails.csv";
/**
 * replace_tags replaces tags in the source string with data from the tags map.
 * The tags are identified by being wrapped in '{{' and '}}' i.e. '{{tag}}'.
 * If a tag value is not present in the tags map, it is replaced with an empty
 * string
 * @param string $string A string containing 1 or more tags wrapped in '{{}}'
 * @param array $tags A map of key-value pairs used to replace tags
 * @param force_lower if true, converts matching tags in string via strtolower()
 *        before checking the tags map.
 * @return string The resulting string with all matching tags replaced.
 */
function replace_tags($string, $tags, $force_lower = false)
{
  return preg_replace_callback(
    '/\\{\\{([^{}]+)\}\\}/',
    function ($matches) use ($force_lower, $tags) {
      $key = $force_lower ? strtolower($matches[1]) : $matches[1];
      return array_key_exists($key, $tags)
        ? $tags[$key]
        : '';
    },
    $string
  );
}

function generate_email($first_name, $last_name, $email, $campus, $personalMSG, $emails_csv)
{
  $email_encoding = array('mailto:{{toEmails}}?cc={{toCC}}&subject=Racist%20and%20Sexist%20Abuses%20in%20the%20SA&body=Dear', '%20{{title}}', '%20{{toName}}', '%2C%0D%0A%0D%0AI%20hope%20this%20email%20finds%20you%20well.%20As%20a%20student', '%20at%20{{campus}}', '%2C%20I%20am%20asking%20you%20to%20call%20for%20a%20formal%20investigation%20of%20the%20claims%20of%20discrimination%2C%20abuses%20of%20power%2C%20and%20mistreatment%20of%20staff%20by%20the%202019-2020%20SUNY%20Student%20Assembly%20leadership.%20I%20also%20urge%20the%20new%20leadership%20in%20the%20SUNY%20Student%20Assembly%20to%20share%20with%20students%20how%20they%20plan%20to%20restructure%20the%20organization%20so%20that%20discrimination%2C%20intimidation%2C%20and%20abuses%20of%20power%20are%20prevented%20and%20reprimanded.', '%0D%0A%0D%0A{{personalMSG}}', '%0D%0A%0D%0ABest%2C%0D%0A{{firstName}}%20{{lastName}}');
  //
  $fileHandle = fopen($emails_csv, "r");
  $row = fgetcsv($fileHandle, 0, ",");
  while ((trim($row[0]) != $campus) && $row[0] != '') {
    $row = fgetcsv($fileHandle, 0, ",");
  }
  $map = array("toName" => rawurlencode($row[1]), "toEmails" => str_replace(" ", "", $row[2]), "title" => rawurlencode($row[3]), "toCC" => str_replace(" ", "", $row[4]), "firstName" => rawurlencode($first_name), "lastName" => rawurlencode($last_name), "campus" => rawurlencode($campus), "personalMSG" => rawurlencode($personalMSG));
  $email_text = $email_encoding[0];
  if ($row[3] != "") $email_text = $email_text . $email_encoding[1]; //Has title
  if ($row[1] != "") $email_text = $email_text . $email_encoding[2]; //Has toName
  $email_text = $email_text . $email_encoding[3]; //Add main body text 1
  if ($campus != "OTHER") $email_text = $email_text . $email_encoding[4]; //Add school name, or none if OTHER
  $email_text = $email_text . $email_encoding[5]; //Add main body text 2
  if ($personalMSG != "") $email_text = $email_text . $email_encoding[6];
  $email_text = $email_text . $email_encoding[7];

  return replace_tags($email_text, $map);
}
/*
TO: {{toEmails}} //////NOTE these are comma-separated, as many as u like, all in 1 string don't separate
CC: {{toCC}}

Dear {{title}} {{toName}},

I hope this email finds you well. As a student at {{campus}}, I am asking you to call for a formal investigation of the claims of discrimination, abuses of power, and mistreatment of staff by the 2019-2020 SUNY Student Assembly leadership. I also urge the new leadership in the SUNY Student Assembly to share with students how they plan to restructure the organization so that discrimination, intimidation, and abuses of power are prevented and reprimanded.

{{personalMSG}}

Best,
{{firstName}} {{lastName}}
*/
$is_adult = TRUE;
// Age verification

// Application Form
$sticky_first = trim(@$_POST['first_name']);
$sticky_last = trim(@$_POST['last_name']);
$sticky_personalMSG = trim(@$_POST['personalMSG']);
$sticky_campus = trim(@$_POST['coverage']);
$myMailTo = "";

$show_confirmation = FALSE;

$show_first_feedback = FALSE;
$show_last_feedback = FALSE;
$show_campus_feedback = FALSE;

// Was the form submitted? Was there a POST request?
if (isset($_POST['application_submit'])) {
  $show_confirmation = TRUE;

  $client_first = filter_var($sticky_first, FILTER_SANITIZE_STRING);
  if (!$client_first) {
    $show_confirmation = FALSE;
    $show_first_feedback = TRUE;
  }

  $client_last = filter_var($sticky_last, FILTER_SANITIZE_STRING);
  if (!$client_last) {
    $show_confirmation = FALSE;
    $show_last_feedback = TRUE;
  }

  $campus = filter_var($sticky_campus, FILTER_SANITIZE_STRING);
  if (!$campus) {
    $show_confirmation = FALSE;
    $show_campus_feedback = TRUE;
  }

  $myMailTo = generate_email($sticky_first, $sticky_last, $sticky_email, $sticky_campus, $sticky_personalMSG, $emails_csv);
}
?>
<!DOCTYPE html>
<html lang="en">

<?php
$scripts = array("scripts/jquery-3.4.1.min.js", "scripts/validation.js");
include("includes/head.php");
?>

<body>
  <header>
    <h1 class="title">Email Your Rep</h1>
  </header>

  <main>
    <?php if (True) {

      if ($show_confirmation) { ?>
        <h2><?php echo ("<a href=" . htmlspecialchars($myMailTo) . ">Click Me to Email Your Rep!") ?> </h2>
      <?php } else { ?>

        <h2>Enter Your Information</h2>

        <form id="insurance_form" method="post" action="index.php" novalidate>

          <p id="first_feedback" class="form_feedback <?php echo ($show_first_feedback) ? '' : 'hidden'; ?>">Please provide a first name.</p>

          <div class="group_label_input">
            <label>First Name:</label>
            <input id="insurance_first" type="text" name="first_name" value="<?php echo htmlspecialchars($sticky_first); ?>" required />
          </div>

          <p id="last_feedback" class="form_feedback <?php echo ($show_last_feedback) ? '' : 'hidden'; ?>">Please provide a last name.</p>

          <div class="group_label_input">
            <label>Last Name:</label>
            <input id="insurance_last" type="text" name="last_name" value="<?php echo htmlspecialchars($sticky_last); ?>" required />
          </div>

          <p id="campus_feedback" class="form_feedback <?php echo ($show_campus_feedback) ? '' : 'hidden'; ?>">Please choose your SUNY campus, or choose 'Other' if not listed.</p>
          <div class="group_label_input">
            <label>Campus:</label>
            <select name="coverage">
              <option value="" selected disabled>Choose Your Campus</option>
              <?php
              $fileHandle = fopen($emails_csv, "r");
              $row = fgetcsv($fileHandle, 0, ","); //SKIP over label row
              while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
                $cats = $row[0];
              ?>
                <option value="<?php echo $cats; ?>"><?php echo $cats; ?></option>
              <?php
              }
              ?>
            </select>
          </div>

          <div class="group_label_input">
            <label>Personal Message (Optional)</label>
            <input id="insurance_personalMSG" type="text" name="personalMSG" value="<?php echo htmlspecialchars($sticky_personalMSG); ?>" required />
          </div>


          <div class="group_label_input">
            <span>
              <!-- empty element; used to align submit button --></span>
            <input type="submit" name="application_submit" value="Submit" />
          </div>
        </form>

    <?php }
    } ?>
  </main>

  <footer>
    <p>Contact us at <a href="">ORG Email Here</a>.</p>
  </footer>

</body>

</html>
