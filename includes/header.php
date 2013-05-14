<?php
    record_set('history',"SELECT * FROM history INNER JOIN contacts ON history_contact = contact_id WHERE history_status = 1 ORDER BY history_date DESC LIMIT 0, 4");
?>
<script language="javascript" src="includes/calendar/calendar.js"></script>
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
<div class="headercontainer"> 
  <img src="images/TBWtype.png" width="200" class="tbwicon" id="tbwtype"/>
  <div class="header">
    <h1>Donation Management</h1>

  <a href="index.php" class="menubuttons <?php if ($pagetitle == 'Dashboard') { echo menubuttonsactive; } ?>">Dashboard</a>

<a href="contacts.php" class="menubuttons <?php if (($pagetitle== 'Donor' || $pagetitle == 'ContactDetails') && !($_GET['import'] == 'success')) { echo 'menubuttonsactive'; } ?>">Donors</a>
<a href="donations.php" class="menubuttons <?php if ($pagetitle == 'Donation') { echo 'menubuttonsactive'; } ?>">Donations</a>
<?php
  if ($user_admin) {
?>
  <a href="users.php" class="menubuttons <?php if ($pagetitle == 'Users') { echo 'menubuttonsactive'; } ?>">Users</a>
  <?php
  }
?>
  <span class="headerright">Logged in as <?php echo $row_userinfo['user_email']; ?> | <a href="logout.php">Log Out</a> | <a href="profile.php">Update Profile</a> </span><br clear="all" />
  </div>
  </div>

<?php if ($totalRows_history) { ?>
<?php if($user_admin){ ?>
<div class="historycontainer">Recent: 
    <?php $ih = 1; do { 
        //GET CONTACT INFO FROM HISTORY
        record_set('histcont',"SELECT * FROM contacts WHERE contact_id = ".$row_history['history_contact']."");
        //
    ?>
    <a href="contact-details.php?id=<?php echo $row_histcont['contact_id']; ?>">
      <?php
      echo $row_histcont['contact_first'] ." ";
      echo $row_histcont['contact_last'];
      

      ?></a>
      <?php if ($totalRows_history!=$ih) {?> &middot; <?php } ?>
      <?php $ih++; } while ($row_history = mysql_fetch_assoc($history)); ?></div>
<?php } ?>
<?php } ?>