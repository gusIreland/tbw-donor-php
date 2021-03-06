<?php 
    require_once('includes/config.php'); 
?>
<?php 
    require_once('includes/config.php'); 
    include('includes/sc-includes.php');
    $pagetitle = 'Dashboard';
    
    if (empty($_GET['s']) && isset($_GET['s'])) {
        header('Location: '.$_SERVER['HTTP_REFERER']); die;
    }
    
    $cwhere = "WHERE history_status = 1";
    if (isset($_GET['s'])) {
        $cwhere = "WHERE (history_status = 1 OR history_status IS NULL) AND ($like_where)";
    }
    
    $search = 0;
    $nwhere = "";
    if (isset($_GET['s'])) {
        $search = 1;
        $nwhere = "WHERE note_text LIKE '%".addslashes($_GET['s'])."%' ";
    }
    //SORTING
    $sorder = '';
    $name = "name_up";
    if (isset($_GET['name_up'])) {
        $sorder = "ORDER BY contact_first ASC";
        $name = "name_down";
    } elseif (isset($_GET['name_down'])) {
        $sorder = "ORDER BY contact_first DESC";
    }
    
    $campaign = "campaign_up";
    if (isset($_GET['campaign_up'])) {
        $sorder = "ORDER BY alloc_short_name ASC";
        $campaign = "campaign_down";
    } elseif (isset($_GET['campaign_down'])) {
        $sorder = "ORDER BY alloc_short_name DESC";
    }
    
    $amount = "amount_up";
    if (isset($_GET['amount_up'])) {
        $sorder = "ORDER BY legal_amount ASC";
        $amount = "amount_down";
    } elseif (isset($_GET['email_phone'])) {
        $sorder = "ORDER BY legal_amount DESC";
    }
    
    $date = "date_up";
    if (isset($_GET['date_up'])) {
        $sorder = "ORDER BY dt_date_record ASC";
        $date = "date_down";
    } elseif (isset($_GET['date_down'])) {
        $sorder = "ORDER BY dt_date_record DESC";
    }

    $matching_company = "matching_company_up";
    if (isset($_GET['matching_company_up'])) {
        $sorder = "ORDER BY match_company_name ASC";
        $matching_company = "matching_company_down";
    } elseif (isset($_GET['matching_company_down'])) {
        $sorder = "ORDER BY match_company_name DESC";
    }

    //END SORTING
    
    //PAGINATION
    $limit = "";
    $epp = 25;  //entries per page

     record_set('results',"SELECT note_id FROM notes, contacts, users WHERE note_pin = 1 AND contacts.contact_id = notes.note_contact AND users.user_id = notes.note_user");

    $entries_per_page = $epp;
    
    $page_number = empty($_GET['page']) ? 1 : $_GET['page']; //current page
    
    $total_pages = ceil($totalRows_results / $entries_per_page); 
    $offset = ($page_number - 1) * $entries_per_page; 
    
    $prev = $page_number -1;
    $next = $page_number + 1;
    
    $limit = "LIMIT $offset, $entries_per_page";


    if(isset($_GET['s']) && preg_match("/^\d+$/", $_GET['s'])) {
        record_set('results_donations', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount = '".$_GET['s']."' ORDER BY legal_amount DESC");
    }

    elseif(isset($_GET['s']) && preg_match("/^<\d+$/", $_GET['s'])) {
        record_set('results_donations', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount < '".substr($_GET['s'], 1)."' ORDER BY legal_amount DESC");
        
    }

    elseif(isset($_GET['s']) && preg_match("/^>\d+$/", $_GET['s'])) {
        record_set('results_donations', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount > '".substr($_GET['s'], 1)."' ORDER BY legal_amount DESC");
        
    }

    elseif(isset($_GET['s']) && preg_match("/^<=\d+$/", $_GET['s'])) {
        record_set('results_donations', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount <= '".substr($_GET['s'], 2)."' ORDER BY legal_amount DESC");
        
    }

    elseif(isset($_GET['s']) && preg_match("/^>=\d+$/", $_GET['s'])) {
        record_set('results_donations', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount >= '".substr($_GET['s'], 2)."' ORDER BY legal_amount DESC");
    }

    $entries_per_page = $epp;
    
    $page_number_donations = empty($_GET['page_donations']) ? 1 : $_GET['page_donations']; //current page
    
    $total_pages_donations = ceil($totalRows_results_donations / $entries_per_page); 
    $offset_donations = ($page_number_donations - 1) * $entries_per_page; 
    
    $prev_donations = $page_number_donations -1;
    $next_donations = $page_number_donations + 1;
    
    $limit_donations = "LIMIT $offset_donations, $entries_per_page";
    
    //

    //get notes
    // record_set('notes',"SELECT * FROM notes INNER JOIN contacts ON note_contact = contact_id $nwhere ORDER BY note_date DESC LIMIT 0, 20");
    $query = "SELECT * FROM notes, contacts, users WHERE note_pin = 1 AND contacts.contact_id = notes.note_contact AND users.user_id = notes.note_user ORDER BY note_date DESC $limit";
    $pinned_notes = mysql_query($query);    
    
    //search results
    $climit = !empty($_GET['s']) ? 1000 : 10;
    record_set('contactlist',"SELECT * FROM history RIGHT OUTER JOIN contacts ON contact_id = history_contact $cwhere ORDER BY history_date DESC LIMIT 0, $climit");

    $comparison = '';
    if(isset($_GET['s']) && preg_match("/^=\d+$/", $_GET['s'])) {
        record_set('donationslist', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount = '" . substr($_GET['s'], 1) . "' ORDER BY legal_amount DESC $limit_donations");
        $comparison = $_GET['s'];
    }

    elseif(isset($_GET['s']) && preg_match("/^<\d+$/", $_GET['s'])) {
        record_set('donationslist', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount < '" . substr($_GET['s'], 1)."' ORDER BY legal_amount DESC $limit_donations");
        $comparison = $_GET['s'];
    }

    elseif(isset($_GET['s']) && preg_match("/^>\d+$/", $_GET['s'])) {
        record_set('donationslist', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount > '" . substr($_GET['s'], 1) . "' ORDER BY legal_amount DESC $limit_donations");
        $comparison = $_GET['s'];
    }

    elseif(isset($_GET['s']) && preg_match("/^<=\d+$/", $_GET['s'])) {
        record_set('donationslist', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount <= '" . substr($_GET['s'], 2) . "' ORDER BY legal_amount DESC $limit_donations");
        $comparison = $_GET['s'];
    }

    elseif(isset($_GET['s']) && preg_match("/^>=\d+$/", $_GET['s'])) {
        record_set('donationslist', "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount >= '" . substr($_GET['s'], 2) . "' ORDER BY legal_amount DESC $limit_donations");
        $comparison = $_GET['s'];
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?php echo $pagetitle; ?></title>
        <link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include('includes/header.php'); ?>
        <div class="container">
            <div class="leftcolumn">
                <?php if ($search==1) { ?>
                    Search results for <em><?php echo $_GET['s']; ?></em>.<br />
                    <br />
                <?php } ?>
                   
                <?php if ($totalRows_donationslist > 0) { ?>
                    <h2>Donations</h2>
                    <form id="form1" name="form1" method="post" action="">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td colspan="4" align="right">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php display_msg(); ?></td>
                            </tr>
                            <tr>
                                <th width="25%"  style="padding-left:5px">Donor Name</th>
                                <th width="25%">Campaign</th>
                                <th width="25%">Donation Amount</a></th>
                                <th width="25%">Date of Donation</a></th>
                                <th width="25%">Matching Company</a></th>
                        
                                <th width="7%">&nbsp;</th>
                            </tr>
                        
                            <?php $row_count = 1; do {  ?>
                                <tr <?php if ($row_count%2) { ?>bgcolor="#F4F4F4"<?php } ?>>
                                    <td style="padding-left:5px">
                                        <a href="contact-details.php?id=<?php echo $row_donationslist['donor_id']; ?>"><?php echo $row_donationslist['contact_first']; ?> <?php echo $row_donationslist['contact_last']; ?></a></td>
                                    <td><?php echo $row_donationslist['receipt_number'] ? $row_donationslist['alloc_short_name'] : $na; ?></td>
                                    <td><?php if ($user_admin) echo "$". $row_donationslist['legal_amount']; ?></td>
                                    <td><?php echo $row_donationslist['date_added']; ?></td>
                                    <td><?php echo $row_donationslist['match_company_name']; ?></td>
                                    <td><a href="donation.php?id=<?php echo $row_donationslist['id']; ?>&redirect=donations.php">Edit</a><br>
                                        <a href="delete.php?donation=<?php echo $row_donationslist['id']; ?>&redirect=donations.php" onclick="javascript:return confirm('Are you sure?')">Delete</a></td>
                                </tr>
                            <?php $row_count++; } while ($row_donationslist = mysql_fetch_assoc($donationslist)); ?>
                        </table>
                    </form>
            <?php if ($search) { if ($totalRows_results_donations > $epp) {
            echo "<div class=\"pagination\">";

            if ($page_number_donations != 1) {
                echo "<a href='index.php?s=$comparison&search=1&page_donations=$prev_donations'>&laquo; Previous </a>";
            }

            if ($page_number_donations == 1) {
                echo "&laquo; Previous ";
            }


            $i = 1;
            do {
                if ($_GET['page_donations'] != $i) {
                    echo "<a href='index.php?s=$comparison&search=1&page_donations=$i'>&nbsp;$i&nbsp;</a>";
                }

                if ($_GET['page_donations'] == $i) {
                    echo "&nbsp;$i&nbsp;";
                }


                $i++;
            } while ($i <= $total_pages_donations);

            if ($page_number_donations != $total_pages_donations) {
                echo "<a href='index.php?s=$comparison&search=1&page_donations=$next_donations&'> Next &raquo;</a>";
            }

            if ($page_number_donations == $total_pages_donations) {
                echo " Next &raquo;";
            }    
            echo "</div>";
        }} ?>
                    <br>
                    <form action="csvR.php" method="post">
                        <input type="hidden" name="comparison_string" value='<?php echo addslashes($comparison); ?>'>
                        <input type="hidden" name="offset" value='<?php echo $offset ?>'>
                        <input type="hidden" name="pp" value='<?php echo $entries_per_page ?>'>
                        <input type="hidden" name="order" value='<?php echo $sorder ?>'>
                        <input type="submit" name="button" id="button" value="Export Above Results"/>
                    </form>
                    <?php } else { 
                    if($_GET['s'])
                        echo "There were no donation results"; 
                } ?>

                <br>

                <?php if ($totalRows_contactlist > 0) { ?>
                    <h2>Donors</h2>
                    <br />
                    <?php $i = 1; do { 
                        $comma = "";
                        if ($i != $totalRows_contactlist) {
                            $comma = ",";
                        }
                    ?>
                    <a href="contact-details.php?id=<?php echo $row_contactlist['contact_id']; ?>">
                        <?php echo $row_contactlist['contact_first']; ?> <?php echo $row_contactlist['contact_last']; ?>
                    </a><?php echo $comma; ?>
                    <?php $i++; } while ($row_contactlist = mysql_fetch_assoc($contactlist)); ?>
                    <?php if ($totalRows_contactlist > 10) { ?>
                        <a href="contacts.php">View all...</a>
                    <?php } ?>
                <?php } else { 
                                if($_GET['s'])
                                    echo "There were no donor results"; 
                            } ?>
                <br />
                <br />
                <hr />
                <br />

                <?php 
                    if(!$_GET['s']) { 
                ?>
                    <h2>Pinned Notes</h2>
                    <br />
                    <?php 
                    if(mysql_num_rows($pinned_notes) == 0) {
                        echo "There are no pinned notes!";
                    } else {
                        $i = 1; 
                        do { 
                            if ($row_notes['note_pin'] == 1) { ?>
                                <div <?php if ($row_notes['note_date'] > time()-1) { ?>id="newnote"<?php } ?>>
                                    <span class="datedisplay">
                                        <a href="contact-details.php?id=<?php echo $row_notes['note_contact']; ?>&note=<?php echo $row_notes['note_id']; ?>">
                                            <?php echo date('F d, Y \a\t g:h:s A', $row_notes['note_date']); ?>
                                        </a>
                                    </span> for 
                                    <a href="contact-details.php?id=<?php echo $row_notes['note_contact']; ?>">
                                            <?php echo $row_notes['contact_first']; ?> <?php echo $row_notes['contact_last']; ?>
                                    </a> - last updated by <?php echo $row_notes['user_email'] ?>
                                    <br />
                                    <?php echo $row_notes['note_text']; ?>
                                </div>
                                <?php if ($totalRows_notes!=$i) { echo "<hr />"; } ?>
                            <?php } ?>
                            
                        <?php $i++;  } while ($row_notes = mysql_fetch_assoc($pinned_notes)); } 
                              include('includes/pagination_index.php');
                      } ?>

                </div>
            <?php include('includes/right-column.php'); ?>
            <br clear="all" />
        </div>
        <?php include('includes/footer.php'); ?>
    </body>
</html>