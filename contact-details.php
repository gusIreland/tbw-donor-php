<?php 
    require_once('includes/config.php'); 
?>
<?php
    include('includes/sc-includes.php');
    
    $pagetitle = 'ContactDetails';
    
    $update = 0;
    if (isset($_GET['note'])) {
        $update = 1;
    }
    
    //contact
    record_set('contact',"SELECT * FROM contacts WHERE contact_id = ".$_GET['id']."");
    
    //donations
    $get_donation_for_donor = "SELECT * 
                               FROM contacts, donations
                               WHERE contacts.contact_id = donations.donor_id
                               AND donor_id = " . $_GET['id'] . "
                               ORDER BY donations.dt_date_record DESC";
    
    
    //notes
    record_set('notes',"SELECT * FROM notes, users WHERE notes.note_user = users.user_id AND note_contact = ".$_GET['id']." ORDER BY note_date DESC");
    
    record_set('note',"SELECT * FROM notes WHERE note_id = -1");

    if ($update == 1) {
        record_set('note',"SELECT * FROM notes WHERE note_id = ".$_GET['note']."");
    }
    
    //INSERT NOTE FOR CONTACT
    if ($update == 0 && !empty($_POST['note_text'])) {
        $is_pinned = $_POST["pinned"];

        if ($is_pinned == true) {
            $pin = 1;
        } else {
            $pin = 0;
        }

        mysql_query("INSERT INTO notes (note_contact, note_text, note_date, note_status, note_pin, note_user) VALUES 
          (
          ".$row_contact['contact_id'].",
          '".addslashes($_POST['note_text'])."',
          ".time().",
          1,
          ". $pin .",
          '" . $row_userinfo['user_id'] . "' 
          )
        ");
      
        $goto = "contact-details.php?id=$_GET[id]";
        redirect('Note Added',$goto);
    }
    //
    
    //UPDATE NOTE
    if ($update==1 && !empty($_POST['note_text'])) {
        $is_pinned = $_POST["pinned"];

        if ($is_pinned == true) {
            $pin = 1;
        } else {
            $pin = 0;
        }
      
        mysql_query("UPDATE notes SET note_text = '" . addslashes($_POST['note_text']) . "',
                                      note_pin =  '" . $pin . "',
                                      note_user = '" . $row_userinfo['user_id'] . "' 
                                      WHERE note_id = ".$_GET['note']."");
    
      $goto = "contact-details.php?id=$_GET[id]";
      redirect('Note Updated',$goto);
    }
    //
    
    
    //UPDATE HISTORY
    record_set('checkhistory',"SELECT history_contact FROM history WHERE history_contact = ".$_GET['id']."");
    
    if ($totalRows_checkhistory > 0) { 
        mysql_query("UPDATE history SET history_status = 2 WHERE history_contact = ".$_GET['id']."");
    }
    
    mysql_query("INSERT INTO history (history_contact, history_date, history_status) VALUES
    (
      ".$row_contact['contact_id'].",
      ".time().",
      1
    )
    ");
    
    //
    
    //can this user edit this contact?
    $can_edit = 0;

    if ($user_admin || $userid == $row_contact['contact_id']) {
        $can_edit = 1;
    }
    //
    
    //automatically add custom field data to contacts contact_custom field
    record_set('cfields',"SELECT * FROM fields_assoc WHERE cfield_contact = ".$_GET['id']."");
    
    do {
        $data .= $row_cfields['cfield_value'].", ";
        mysql_query("UPDATE contacts SET contact_custom = '".$data."' WHERE contact_id = ".$_GET['id']."");
    } while ($row_cfields = mysql_fetch_assoc($cfields));
    //
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?php echo $row_contact['contact_first']; ?> <?php echo $row_contact['contact_last']; ?></title>
        
        <script src="includes/src/unittest.js" type="text/javascript"></script>
        <link href="includes/style.css" rel="stylesheet" type="text/css" />
        <link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
    </head>
    
    <body <?php if ($row_notes['note_date'] > time()-1) { ?>onload="new Effect.Highlight('newnote'); return false;"<?php } ?>>
        <?php include('includes/header.php'); ?>
        <div class="container">
            <div class="leftcolumn">
                <?php display_msg(); ?>
        
                <div style="display:block; margin-bottom:5px">
                        <?php if ($row_contact['contact_image']) { ?>
                            <img src="images/<?php echo $row_contact['contact_image']; ?>" width="95" height="71" class="contactimage" />
                        <?php } ?>
                    <h2>
                        <?php echo $row_contact['contact_first']; ?> <?php echo $row_contact['contact_last']; ?>
                        <?php if ($row_contact['contact_company']) { ?>
                            <span style="color:#999999"> with <?php echo $row_contact['contact_company']; ?></span>
                        <?php } ?>
    
                        <?php if ($can_edit) { ?>
                            <a style="font-size:12px; font-weight:normal" href="contact.php?id=<?php echo $row_contact['contact_id']; ?>">
                                &nbsp;&nbsp;+ Edit donor
                            </a>
                        <?php } ?>
                    </h2>
                    <?php if ($row_contact['contact_email']) { ?>
                        <h3><a href="mailto:<?php echo $row_contact['contact_email']; ?>?subject=Thanks for donating to TechBridgeWorld!"><?php echo $row_contact['contact_email']; ?></a></h3>       
                    <?php } ?>
                    <br clear="all" />
                </div>
        
                <p>
                    <?php
                        $result_donations = mysql_query($get_donation_for_donor);
                        if (!$result_donations) { // add this check.
                            die('Invalid query: ' . mysql_error());
                        }
        
                        if(mysql_num_rows($result_donations) == 0){
                            echo "This donor does not have any donations!<br><br>";
                            
                        }
        
                        else{
                            echo "<h3>Donations</h3>";
                            echo "<table>";
                            echo "<tr>";
                            echo "<th width=\"25%\" style=\"padding-left:5px\">Campaign</th>";
                            echo "<th width=\"25%\"  >Legal Amount</th>";
                            echo "<th width=\"25%\" >Date of Donation</th>";
                            echo "<th width=\"25%\" >Matching Company</th>";
                            echo "</tr>";     
                            $counter = 0;
                            while($row = mysql_fetch_array($result_donations))
                            {   
                                if ($counter % 2) {
                                 echo "<tr bgcolor=\"#F4F4F4\" >";
                                } else {
                                    echo "<tr>";
                                }
                                if ($row['alloc_short_name']){
                                    echo "<td style=\"padding-left:5px\">" . $row['alloc_short_name'] . "</td>";
                                } else {
                                    echo "<td style=\"padding-left:5px\">" . $na ."</td>";
                                }
                                echo "<td>" . $row['legal_amount'] . "</td>";
                                echo "<td>" . (strftime("%m/%d/%Y", strtotime($row['dt_date_record']))) . "</td>";
                                echo "<td>" . $row['match_company_name'] . "</td>";
                                echo "<td><a href=\"donation.php?id=".$row['id'] ."\">Edit</a></td>";
                                echo "<td><a href=\"delete.php?donation=". $row['id'] . "&redirect=contact-details.php?id=".$_GET['id']."\" onclick=\"javascript:return confirm('Are you sure?')\">Delete</a></td>";
                                echo "</tr >";
                                $counter = $counter + 1;
                            }
        
                            echo "</table>";
                        }
                    ?>
                    <br />
                </p>
        
                <?php 
                    if (!$update) { 
                        echo "Add a new note <br>"; 
                    } else {
                        echo "<br><br><h2>Updating Note</h3>";
                    }
                ?>
        
                <form action="" method="POST" enctype="multipart/form-data" name="form1" id="form1">
                    <textarea name="note_text" style="width:95% "rows="2" id="note_text"><?php echo $row_note['note_text']; ?></textarea>
                    <br />
                    Pinned?<input type="checkbox" name="pinned" value="true" <?php if ($row_note['note_pin']) {echo "checked";}?>></input>
                    <input type="submit" name="Submit2" value="<?php if ($update==1) { echo 'Update'; } else { echo 'Add'; } ?> note" />
                </form>
                <br>
                <br>
                
                <?php if ($update) { ?>
                    <a href="delete.php?note=<?php echo $row_note['note_id']; ?>&amp;id=<?php echo $row_note['note_contact']; ?>" onclick="javascript:return confirm('Are you sure you want to delete this note?')">Delete Note</a>
                <?php } else { ?>

                    <?php if ($totalRows_notes > 0) { ?>
                        <hr />
                        <?php do { ?>
                            <div <?php if ($row_notes['note_date'] > time()-1) { ?>id="newnote"<?php } ?>>
                                <span class="datedisplay">
                                    <a href="?id=<?php echo $row_contact['contact_id']; ?>&note=<?php echo $row_notes['note_id']; ?>">
                                        <?php echo date('F d, Y', $row_notes['note_date']); ?>
                                    </a>
                                </span> - last updated by <?php echo $row_notes['user_email'] ?>
                                <br />
                                <?php echo $row_notes['note_text']; ?>
                            </div>
                            <hr />
                        <?php } while ($row_notes = mysql_fetch_assoc($notes)); ?>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php include('includes/right-column.php'); ?>
            <br clear="all" />
        </div>
        <?php include('includes/footer.php'); ?>
    </body>
</html>