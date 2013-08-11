<?php

/*
=====================================================
 Is Message Read?
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2013 Yuri Salimovskiy
=====================================================
 This software is intended for usage with
 ExpressionEngine CMS, version 2.0 or higher
=====================================================
 File: pi.is_message_read.php
-----------------------------------------------------
 Purpose: Check whether PM in 'sent' folder is read by recipient
=====================================================
*/


$plugin_info = array(
		'pi_name'			=> 'Is message read?',
		'pi_version'		=> '1.0',
		'pi_author'			=> 'Yuri Salimovskiy',
		'pi_author_url'		=> 'http://www.intoeetive.com/',
		'pi_description'	=> "Check whether PM in 'sent' folder is read by recipient",
		'pi_usage'			=> Is_message_read::usage()
	);


class Is_message_read {

    var $return_data;
    
    /** ----------------------------------------
    /**  Constructor
    /** ----------------------------------------*/

    function __construct()
    {        
    	$this->EE =& get_instance(); 
    }
    /* END */	    

    
    /** ----------------------------------------
    /**  Check
    /** ----------------------------------------*/

    function check()
    {
        
        $this->EE->db->select('received.message_read')
        	->from('message_copies AS received')
        	->where('received.sender_id', $this->EE->session->userdata('member_id'))
        	->where('received.recipient_id != ', $this->EE->session->userdata('member_id'));
       	if ($this->EE->TMPL->fetch_param('message_id')!==false)
       	{
       		$this->EE->db->where('message_id', $this->EE->TMPL->fetch_param('message_id'));
       	}
       	else if ($this->EE->TMPL->fetch_param('copy_id')!==false)
       	{
       		$this->EE->db->join('message_copies AS sent', 'sent.message_id=received.message_id', 'left');
       		$this->EE->db->where('sent.copy_id', $this->EE->TMPL->fetch_param('copy_id'));
			$this->EE->db->where('message_id', $this->EE->TMPL->fetch_param('message_id'));
       	}
       	else
       	{
       		$this->return_data = $this->EE->TMPL->no_results();
       		return $this->return_data;
       	}
       	
       	$q = $this->EE->db->get();
       	
       	if ($q->num_rows()!=1)
       	{
       		$this->return_data =  $this->EE->TMPL->no_results();
       		return $this->return_data;
       	}
       	
       	if ($q->row('message_read')=='n')
       	{
       		$this->return_data =  $this->EE->TMPL->no_results();
       		return $this->return_data;
       	}
       	
       	$this->return_data =  $this->EE->TMPL->tagdata;
       	
       	return $this->return_data;

    }
    /* END */
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>
Check whether PM in 'sent' folder is read by recipient.
Parameters (use one of them):
message_id - ID of private messsage (provided by Friends module as {message_id})
copy_id - ID of PM copy in 'sent' folder (provided by Messaging module as {message_id})

Returns tagdata if message is read, and no results if it's not read yet.

{exp:messaging:private_messages folder="sent"}
{exp:is_message_read:check copy_id="{message_id}"}
{if no_results}
&lt;div class="unread"&gt;
{/if}
&lt;div class="read"&gt;
{/exp:is_message_read:check}
{subject}&lt;/div&gt;
{/exp:messaging:private_messages}

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
/* END */


}
// END CLASS
?>