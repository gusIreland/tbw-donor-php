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

$number = "number_up";
if (isset($_GET['number_up'])) {
  $sorder = "ORDER BY count ASC";
  $number = "number_down";
} elseif (isset($_GET['email_down'])) {
  $sorder = "ORDER BY count DESC";
}

$amount = "amount_up";
if (isset($_GET['amount_up'])) {
  $sorder = "ORDER BY contact_phone ASC";
  $amount = "amount_down";
} elseif (isset($_GET['email_phone'])) {
  $sorder = "ORDER BY contact_phone DESC";
}

$date = "date_up";
if (isset($_GET['date_up'])) {
  $sorder = "ORDER BY contact_phone ASC";
  $date = "date_down";
} elseif (isset($_GET['email_phone'])) {
  $sorder = "ORDER BY contact_phone DESC";
}

//END SORTING

//datePicker
$search = 0;
if(isset($_REQUEST["dateStart"])){

  $search = 1;
  $startDate = isset($_REQUEST["dateStart"]) ? $_REQUEST["dateStart"] : "";
  $endDate = isset($_REQUEST["dateEnd"]) ? $_REQUEST["dateEnd"] : "";
}






//PAGINATION
$limit = "";
$epp = 25;  //entries per page
record_set('results',"SELECT id FROM donations");





$entries_per_page = $epp;

$page_number = empty($_GET['page']) ? 1 : $_GET['page']; //current page

$total_pages = ceil($totalRows_results / $entries_per_page); 
$offset = ($page_number - 1) * $entries_per_page; 

$prev = $page_number -1;
$next = $page_number + 1;

$limit = "LIMIT $offset, $entries_per_page";
//

//get contacts
if($search == 1){
  record_set('contactlist',"SELECT * FROM donations where date_added between $startDate and $endDate $sorder $limit");

}
else
record_set('contactlist',"SELECT * FROM donations $sorder $limit ");
 
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
  <?php include('includes/header.php');
  ?>
  
  <div class="container">
    <div class="leftcolumn">
      <h2>Donations</h2>

      <form action="donations.php" method="post">
      <div style="float: left; padding-right: 3px; line-height: 18px;">from:</div>
        <?php
//get class into the page
        require_once('includes/calendar/classes/tc_calendar.php');
//instantiate class and set properties
        $date3_default = "2013-04-29";
        $date4_default = "2013-05-05";

        $myCalendar = new tc_calendar("dateStart", true, false);
        $myCalendar->setIcon("includes/calendar/images/iconCalendar.gif");
        $myCalendar->setDate(date('d', strtotime($date3_default))
          , date('m', strtotime($date3_default))
          , date('Y', strtotime($date3_default)));
        $myCalendar->setPath("includes/calendar/");
        $myCalendar->setYearInterval(1970, 2020);
        $myCalendar->setAlignment('left', 'bottom');
        $myCalendar->setDatePair('dateStart', 'dateEnd', $dateEnd_default);
        $myCalendar->writeScript();   
        ?>
        <div style="float: left; padding-right: 3px; line-height: 18px;">to:</div>
        <?php
        $myCalendar = new tc_calendar("dateEnd", true, false);
        $myCalendar->setIcon("includes/calendar/images/iconCalendar.gif");
        $myCalendar->setDate(date('d', strtotime($date4_default))
         , date('m', strtotime($date4_default))
         , date('Y', strtotime($date4_default)));
        $myCalendar->setPath("includes/calendar/");
        $myCalendar->setYearInterval(1970, 2020);
        $myCalendar->setAlignment('left', 'bottom');
        $myCalendar->setDatePair('dateStart', 'dateEnd', $dateStart_default);
        $myCalendar->writeScript();   
        ?>
        <input type="submit" name="button" id="button" value="Submit" />
      </form>


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
        <th width="26%"  style="padding-left:5px"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $name; ?>">Donor Name</a></th>
        <th width="27%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $number; ?>">Reciept </a></th>
        <th width="40%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $amount; ?>">Donation Amount</a></th>
        <th width="40%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $date; ?>">Date of Donation</a></th>

        <th width="7%">&nbsp;</th>
      </tr>

      <?php $row_count = 1; do {  ?>
      <tr <?php if ($row_count%2) { ?>bgcolor="#F4F4F4"<?php } ?>>
        <td style="padding-left:5px"><a href="contact-details.php?id=<?php echo $row_contactlist['donor_id']; ?>"><?php echo $row_contactlist['donor_id']; ?> <?php echo $row_contactlist['contact_last']; ?></a></td>
        <td><?php echo $row_contactlist['receipt_number'] ? $row_contactlist['receipt_number'] : $na; ?></td>
       <td><?php echo $row_contactlist['legal_amount'];?></td>
       <td><?php echo $row_contactlist['date_added']; ?></td>
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