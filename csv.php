<?php 
    require_once('includes/config.php');
    include('includes/sc-includes.php');
    
    //get contacts
    // record_set('contactlist',"SELECT * FROM contacts");
    $query = "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id";
    record_set('donationlist', $query);
    
    //get custom fields
    record_set('fields',"SELECT * FROM fields ORDER BY field_title ASC");
    $csv_fields = array();
    
    $csv_fields[0] = 'contact_id';
    $csv_fields[1] = 'contact_first';
    $csv_fields[2] = 'contact_last';
    $csv_fields[3] = 'contact_title';
    $csv_fields[4] = 'contact_tags';
    $csv_fields[5] = 'contact_company';
    $csv_fields[6] = 'contact_street';
    $csv_fields[7] = 'contact_city';
    $csv_fields[8] = 'contact_state';
    $csv_fields[9] = 'contact_country';
    $csv_fields[10] = 'contact_zip';
    $csv_fields[11] = 'contact_email';
    $csv_fields[12] = 'id';
    $csv_fields[13] = 'cmu_transaction_id';
    $csv_fields[14] = 'dt_date_record';
    $csv_fields[15] = 'date_added';
    $csv_fields[16] = 'legal_amount';
    $csv_fields[17] = 'credit_amount';
    $csv_fields[18] = 'transaction_type_description';
    $csv_fields[19] = 'alloc_short_name';
    $csv_fields[20] = 'prsp_manager';
    $csv_fields[21] = 'receipt_number';
    $csv_fields[22] = 'prim_comment';
    $csv_fields[23] = 'match_company_name';
    $csv_fields[24] = 'donor_id';

    $i = 15;
    if ($totalRows_fields) do {
        $csv_fields[$i] = $row_fields[field_title];
        $i++;
    } while ($row_fields = mysql_fetch_assoc($fields));
    
    foreach ($csv_fields as $key => $value) {
        $csv_output .= "\"".$value."\",";
    }
    
    $csv_output .= "\n"; 
    
    
    do {
        $csv_output_r = array();
        $csv_output_r[0] = $row_donationlist['contact_id'];
        $csv_output_r[1] = $row_donationlist['contact_first'];
        $csv_output_r[2] = $row_donationlist['contact_last'];
        $csv_output_r[3] = $row_donationlist['contact_title'];
        $csv_output_r[4] = $row_donationlist['contact_tags'];
        $csv_output_r[5] = $row_donationlist['contact_company'];
        $csv_output_r[6] = $row_donationlist['contact_street'];
        $csv_output_r[7] = $row_donationlist['contact_city'];
        $csv_output_r[8] = $row_donationlist['contact_state'];
        $csv_output_r[9] = $row_donationlist['contact_country'];
        $csv_output_r[10] = $row_donationlist['contact_zip'];
        $csv_output_r[11] = $row_donationlist['contact_email'];
        $csv_output_r[12] = $row_donationlist['id'];
        $csv_output_r[13] = $row_donationlist['cmu_transaction_id'];
        $csv_output_r[14] = $row_donationlist['dt_date_record'];
        $csv_output_r[15] = $row_donationlist['date_added'];
        $csv_output_r[16] = $row_donationlist['legal_amount'];
        $csv_output_r[17] = $row_donationlist['credit_amount'];
        $csv_output_r[18] = $row_donationlist['transaction_type_description'];
        $csv_output_r[19] = $row_donationlist['alloc_short_name'];
        $csv_output_r[20] = $row_donationlist['prsp_manager'];
        $csv_output_r[21] = $row_donationlist['receipt_number'];
        $csv_output_r[22] = $row_donationlist['prim_comment'];
        $csv_output_r[23] = $row_donationlist['match_company_name'];
        $csv_output_r[24] = $row_donationlist['donor_id'];
        
        //get custom fields for this contact
        record_set('lfields',"SELECT * FROM fields ORDER BY field_title ASC");
        
        $i = 15; 
        if ($totalRows_lfields) do {
            record_set('cf',"SELECT * FROM fields_assoc WHERE cfield_contact = ".$row_donationlist['contact_id']." AND cfield_field = ".$row_lfields['field_id']."");
            $csv_output_r[$i] = $row_cf['cfield_value'];
            $i++; 
        } while ($row_lfields = mysql_fetch_assoc($lfields));
        //
        
        foreach ($csv_output_r as $key => $value) {
            $csv_output .= "\"".$value."\",";
        }
        
            $csv_output .= "\n";
        
    } while($row_donationlist = mysql_fetch_array($donationlist));
    
    //You cannot have the breaks in the same feed as the content. 
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: csv; filename=simplecustomer.csv");
    print $csv_output;
    exit;
?>