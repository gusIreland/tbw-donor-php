<?php 
    require_once('includes/config.php');
    include('includes/sc-includes.php');
    $pagetitle = 'Donation';
    
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
    
    //datePicker
    
    
    //PAGINATION
    $limit = "";
    $epp = 25;  //entries per page
    record_set('results',"SELECT id FROM donations, contacts WHERE donations.donor_id = contacts.contact_id");


    $entries_per_page = $epp;
    
    $page_number = empty($_GET['page']) ? 1 : $_GET['page']; //current page
    
    $total_pages = ceil($totalRows_results / $entries_per_page); 
    $offset = ($page_number - 1) * $entries_per_page; 
    
    $prev = $page_number -1;
    $next = $page_number + 1;
    
    $limit = "LIMIT $offset, $entries_per_page";
    //
    
    //get donations and their donors for the current page with the current sort
    record_set('contactlist',"SELECT * FROM donations, contacts WHERE donations.donor_id = contacts.contact_id $sorder $limit");
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
                    No donations have been added yet.
                    <br />
                    <br />
                    <strong><a href="contact.php">Add</a> or <a href="import.php">Import</a> Donations </strong><br />
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
                                <td colspan="4"><?php display_msg(); ?></td>
                            </tr>
                            <tr>
                                <th width="26%"  style="padding-left:5px"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $name; ?>">Donor Name</a></th>
                                <th width="27%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $campaign; ?>">Campaign </a></th>
                                <th width="40%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $amount; ?>">Donation Amount</a></th>
                                <th width="40%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $date; ?>">Date of Donation</a></th>
                                <th width="40%"><a href="?page=<?php echo $page_number; ?>&amp;<?php echo $matching_company; ?>">Matching Company</a></th>
                        
                                <th width="7%">&nbsp;</th>
                            </tr>
                        
                            <?php $row_count = 1; do {  ?>
                                <tr <?php if ($row_count%2) { ?>bgcolor="#F4F4F4"<?php } ?>>
                                    <td style="padding-left:5px">
                                        <a href="contact-details.php?id=<?php echo $row_contactlist['donor_id']; ?>"><?php echo $row_contactlist['contact_first']; ?> <?php echo $row_contactlist['contact_last']; ?></a></td>
                                    <td><?php echo $row_contactlist['receipt_number'] ? $row_contactlist['alloc_short_name'] : $na; ?></td>
                                    <td><?php echo $row_contactlist['legal_amount'];?></td>
                                    <td><?php echo $row_contactlist['date_added']; ?></td>
                                    <td><?php echo $row_contactlist['match_company_name']; ?></td>
                                    <td><a href="delete.php?donation=<?php echo $row_contactlist['id']; ?>&redirect=donations.php" onclick="javascript:return confirm('Are you sure?')">Delete</a></td>
                                </tr>
                            <?php $row_count++; } while ($row_contactlist = mysql_fetch_assoc($contactlist)); ?>
                        </table>
                    </form>
                    <?php include('includes/pagination_contacts.php'); ?>
                <?php } ?>
                
            </div>
        <?php include('includes/right-column.php'); ?>
        <br clear="all" />
        </div>
        <?php include('includes/footer.php'); ?>
    </body>
</html>