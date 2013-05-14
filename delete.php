<?php 
    require_once('includes/config.php'); 
    include('includes/sc-includes.php');
    
    //DELETE CONTACT
    if (isset($_GET['contact'])) {
        mysql_query("DELETE FROM contacts WHERE contact_id = ".$_GET['contact']."");
        mysql_query("DELETE FROM history WHERE history_contact = ".$_GET['contact']."");
        mysql_query("DELETE FROM notes WHERE note_contact = ".$_GET['contact']."");
        mysql_query("DELETE FROM donations WHERE donor_id = ".$_GET['contact']."");
        redirect('Contact Deleted',"contacts.php");
    }
    //

    //DELETE DONATION
    if (isset($_GET['donation']) && isset($_GET['redirect'])) {
        mysql_query("DELETE FROM donations WHERE id = ".$_GET['donation']."");
        redirect('Donation Deleted',$_GET['redirect']);
    }
    //
    
    //DELETE NOTE
    if (isset($_GET['note'])) {
        mysql_query("DELETE FROM notes WHERE note_id = ".$_GET['note']."");
        $redirect = "contact-details.php?id=$_GET[id]";
        redirect('Note Deleted',$redirect);
    }
    //
    
    //DELETE FIELD
    if (isset($_GET['field'])) {
        mysql_query("DELETE FROM fields WHERE field_id = ".$_GET['field']."");
        $redirect = 'fields.php';
        redirect('Field Deleted',$redirect);
    }
    //

    if (isset($_GET['user'])) {
        mysql_query("DELETE FROM users WHERE user_id = ".$_GET['user']."");
        $redirect = "users.php";
        redirect('User Deleted',$redirect);
    }
?>