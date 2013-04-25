<?php require_once('includes/config.php');
include('includes/sc-includes.php');
$pagetitle = 'Donation';

//SORTING
$sorder = '';
$name = "name_up";
if (isset($_GET['name_up'])) {
$sorder = "ORDER BY contact_last ASC";
$name = "name_down";
} elseif (isset($_GET['name_down'])) {
$sorder = "ORDER BY contact_last DESC";
}

$email = "email_up";
if (isset($_GET['email_up'])) {
$sorder = "ORDER BY contact_email ASC";
$email = "email_down";
} elseif (isset($_GET['email_down'])) {
$sorder = "ORDER BY contact_email DESC";
}

$phone = "phone_up";
if (isset($_GET['phone_up'])) {
$sorder = "ORDER BY contact_phone ASC";
$phone = "phone_down";
} elseif (isset($_GET['email_phone'])) {
$sorder = "ORDER BY contact_phone DESC";
}
//END SORTING

//PAGINATION
$limit = "";
$epp = 25;  //entries per page

record_set('results',"SELECT contact_id FROM contacts");

$entries_per_page = $epp;

$page_number = empty($_GET['page']) ? 1 : $_GET['page']; //current page

$total_pages = ceil($totalRows_results / $entries_per_page); 
$offset = ($page_number - 1) * $entries_per_page; 

$prev = $page_number -1;
$next = $page_number + 1;

$limit = "LIMIT $offset, $entries_per_page";
//

//get contacts
record_set('contactlist',"SELECT *, COUNT(*) AS 'count'
                           FROM contacts, donations
                           WHERE contacts.contact_id = donations.donor_id
                           GROUP BY contacts.contact_first
                           ORDER BY donations.dt_date_record DESC $limit");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $pagetitle; ?>s</title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>

<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include('includes/header.php'); ?>
  
  <div class="container">
  <div class="leftcolumn">
    <h2>Donations</h2>

<?php if (!$totalRows_contactlist) { ?>
<br />
No contacts have been added yet.
<br />
<br />
<strong><a href="contact.php">Add</a> or <a href="import.php">Import</a> Contacts </strong><br />
<br />
<?php } ?>

<?php if ($totalRows_contactlist) { ?>
    <form id="form1" name="form1" method="post" action="">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="4" align="right">
 </td>
        </tr>
        <tr>
          <td colspan="4">
            <?php display_msg(); ?></td>
        </tr>
        <tr>
          <th width="26%"  style="padding-left:5px"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $name; ?>">Name</a></th>
          <th width="27%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $phone; ?>">Number of Donations</a></th>
          <th width="40%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $email; ?>">Most Recent Donation Amount</a></th>
          <th width="40%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $email; ?>">Date of Most Recent Donation</a></th>

          <th width="7%">&nbsp;</th>
        </tr>

  <?php $row_count = 1; do {  ?>
        <tr <?php if ($row_count%2) { ?>bgcolor="#F4F4F4"<?php } ?>>
          <td style="padding-left:5px"><a href="contact-details.php?id=<?php echo $row_contactlist['contact_id']; ?>"><?php echo $row_contactlist['contact_first']; ?> <?php echo $row_contactlist['contact_last']; ?></a></td>
          <td><?php echo $row_contactlist['count'] ? $row_contactlist['count'] : $na; ?></td>
          <?php
                $query = "SELECT legal_amount, dt_date_record
                           FROM contacts, donations
                           WHERE contacts.contact_id = donations.donor_id
                           AND contacts.contact_id = " . $row_contactlist['contact_id'] . "
                           ORDER BY donations.dt_date_record DESC
                           LIMIT 1";
                $result = mysql_query($query);
                $legal_amount_row = mysql_fetch_row($result);
                echo ("<td>" . $legal_amount_row[0] . "</td><td>" . (strftime("%m/%d/%y", strtotime($legal_amount_row[1]))) ."</td>");
                ?>
          <td><a href="delete.php?contact=<?php echo $row_contactlist['contact_id']; ?>" onclick="javascript:return confirm('Are you sure?')">Delete</a></td>
        </tr>
        <?php $row_count++; } while ($row_contactlist = mysql_fetch_assoc($contactlist)); ?>
      </table>
    </form>

<?php
include('includes/pagination_contacts.php');
?>

    <?php } ?>



  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>