<?php

	function get_scoutnet_api_url()	{
		$scoutnet_api_url = "www.scoutnet.se";
		return $scoutnet_api_url;
	}

	/*
	Kår-id
	*/
	function scoutnet_get_option_kar_id()	{
		return 123;	
	}

	/* Bekräftelsetext */
	function getSuccessMsg()	{
		return "<p><b>Tack för din anmälan av en ny eventuell scout.</b> Om du har frågor om din anmälan, kontakta Test Testsson på medlem@testscout.se eller ring 
        070-123 45 67.</p>";
	}

    /* Bekräftelsetext signatur */
	function getSuccessMsgSign()	{
		return "<p><br/>/Test Scoutkår</p>";
	}

    /* Avsändare e-post */
	function getFromEmail()	{
		return "medlem@testscout.se";
	}

    /* Avsändare namn för e-post */
	function getFromEmailName()	{
		return "Test Scoutkår";
	}

    /* E-post för medlemsregistrerare */
	function getEmailMembermanager()	{
		return "medlem@testscout.se";
	}

    /*
	API-nyckeln för att registrera på väntelistan
	*/
	function scoutnet_get_option_api_nyckel_waitinglist()	{
		return '123asdfggh4h676jkli8uer3iusjhsd';
	}
		
	/*
	 * Ger url:en för att registrera en medlem på kårens väntelista
	 */
	function scoutnet_get_url_register_on_waitinglist() {
		// registrera på väntelistan api/organisation/register/member

		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_waitinglist();
		$apiurl = get_scoutnet_api_url();

		$result = "https://$karid:$apinyckel@$apiurl/api/organisation/register/member";

        return $result;		
	}
?>