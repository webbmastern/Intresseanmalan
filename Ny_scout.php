<a id="formular"></a><!--  För att tillåta att länkar till formuläret på sidan -->

<?php


// --- CURL och PHPMailer måste vara installerade. 

//Ange lokal sökväg till PHPMailer nedan (för Joomla   /libraries/phpmailer/) 
$phpmailerpath = 'class-phpmailer.php';

if (file_exists($phpmailerpath)) {
    require_once $phpmailerpath;
} else {
    die("<p>Sökvägen till PHPMailer verkar felaktig, eller PHPMailer är inte installerat.</p>"); 
}

// och nu testar vi för CURL
if (!function_exists("curl_init")){ // is cURL installed?
	die("<p>Servern stöder inte formuläret (CURL inte installerat eller fel sökväg till CURL)</p>");
}

// --- URL för Scoutnet API Endpoint aktiveras och hämtas i Scoutnet, kräver hög (Ko) behörighet

/************Standardinställning för API:er***************/
require_once('intresseanmalan_config.php');


// --- Sätt och nollställ variabler. Anpassa dessa för din kår och ditt system
$url = scoutnet_get_url_register_on_waitinglist();

$success_msg = getSuccessMsg();

$success_msg_sign = getSuccessMsgSign();

$from_email = getFromEmail();

$from_name = getFromEmailName();

$medlemsreg_email = getEmailMembermanager();

$success_subject = "Bekräftelse anmälan för "; // namn läggs till automatiskt.

$success_ga = "<script type='text/javascript'>ga('send', 'pageview', {  'page': '/intresseanmalan/tack',  'title': 'Tack för din intresseanmälan'});</script>";

$query_string = ""; //nollställ query-string

$form_val = 0;	//Sätts via query-param nedan
				//0==intresseanmälan
				//1==direktregistrering
				//2==direktregistrering vuxen

//Ändra inte nedan om du inte vet helt säkert vad du gör
// --- Ladda in hela formuläret i en PHP-variabel

$form_val = $_GET['form_val'];
//echo $form_val;

$form_val_required_attribute = "";
$form_val_required_star = "";
$form_val_adult_required_attribute = "";
$form_val_adult_required_visibility = "";

if (0==$form_val || 1==$form_val)	{
	$form_val_required_attribute = "required";
	$form_val_required_star = "*";
	$form_val_adult_required_visibility = "display:none;";
}
else {
	$form_val_adult_required_attribute = "required";
}

$year_of_birth_tmp = "";
for($i = date('Y') ; $i > date('Y')-100; $i--)	{
	if ($_POST['year_of_birth'] == $i)	{
		$year_of_birth_tmp.= "<option selected value='$i'>$i</option>";
	}
	else {
		$year_of_birth_tmp.= "<option value='$i'>$i</option>";
	}
}
$year_of_birth = $year_of_birth_tmp;

$month_of_birth_tmp = "";
for($i = 1 ; $i <= 12; $i++)	{
	$month_of_birth_tmp.= "<option ";
	if ($_POST['month_of_birth'] == $i)	{
		$month_of_birth_tmp.= "selected ";
	}

	if ($i < 10)	{
		$month_of_birth_tmp.= "value='0$i'>0$i";
	}
	else {
		$month_of_birth_tmp.= "value='$i'>$i";
	}
	$month_of_birth_tmp.= "</option>";	
}
$month_of_birth = $month_of_birth_tmp;

$day_of_birth_tmp = "";
for($i = 1 ; $i <= 31; $i++)	{
	$day_of_birth_tmp.= "<option ";
	if ($_POST['day_of_birth'] == $i)	{
		$day_of_birth_tmp.= "selected ";
	}

	if ($i < 10)	{
		$day_of_birth_tmp.= "value='0$i'>0$i";
	}
	else {
		$day_of_birth_tmp.= "value='$i'>$i";
	}
	$day_of_birth_tmp.= "</option>";	
}
$day_of_birth = $day_of_birth_tmp;

$formular = "
<form id='scoutnet-form' method='post' action='#formular'>


	<fieldset class='intresseform'>
		<legend>Blivande medlem</legend>

		<ul>
			<li>
				<label for='profile[first_name]'>Förnamn:*</label>
				<input type='text' size='25' required name='profile[first_name]' id='profile[first_name]' value='".$_POST['profile']['first_name']."' placeholder='Förnamn'>
			</li>

			<li>
				<label for='profile[last_name]'>Efternamn:*</label>
				<input type='text' size='25' required name='profile[last_name]' value='".$_POST['profile']['last_name']."' placeholder='Efternamn'>
			</li>

			<li>
				<label for='ssno'>Personnummer:*</label>
				<div>
					<select name='year_of_birth'>
						".
							$year_of_birth
						."
					</select>
					<select name='month_of_birth'>
						".
							$month_of_birth
						."
					</select>
					<select name='day_of_birth'>    
						".
							$day_of_birth
						."
					</select>
					<input type='text' pattern='[0-9]{4}' size='8' required name='birth_number' value='".$_POST['birth_number']."' placeholder='XXXX'>
				</div>
			</li>
			
			<li style='" . $form_val_adult_required_visibility ."'>
				<label for='profile[email]'>E-post:*</label>
				<input type='email' size='30' ". $form_val_adult_required_attribute ." name='profile[email]' id='profile[email]' value='".$_POST['profile']['email']."' placeholder='E-postadress'>
			</li>

			<li style='" . $form_val_adult_required_visibility ."'>
				<label for='contact_list[contacts][contact_1][details]'>Mobiltelefon:*</label>
				<input type='tel' pattern='[\d\s-]*' size='15' ". $form_val_adult_required_attribute ." name='contact_list[contacts][contact_1][details]' id='contact_list[contacts][contact_1][details]' value='".$_POST['contact_list']['contacts']['contact_1']['details']."' placeholder='070-1234567'>
			</li>

			<li>
				<label for='address_list[addresses][address_1][address_line1]'>Gatuadress:*</label>
				<input type='text' size='25' required name='address_list[addresses][address_1][address_line1]' id='address_list[addresses][address_1][address_line1]' value='".$_POST['address_list']['addresses']['address_1']['address_line1']."' placeholder='Scoutvägen 1907'>
			</li>

			<li>	
				<label for='address_list[addresses][address_1][address_line1]'>Postnummer:*</label>
				<input type='number' pattern='[0-9]{5}' size='10' required name='address_list[addresses][address_1][zip_code]' id=name='address_list[addresses][address_1][zip_code]' value='".$_POST['address_list']['addresses']['address_1']['zip_code']."' placeholder='12345'>
			</li>

			<li>
				<label for='address_list[addresses][address_1][zip_name]'>Postort:*</label>
				<input type='text' size='25' required name='address_list[addresses][address_1][zip_name]' id='address_list[addresses][address_1][zip_name]' value='".$_POST['address_list']['addresses']['address_1']['zip_name']."'  placeholder='Hässelby'>
			</li>
		</ul>
	</fieldset>

	</br>

	<fieldset class='intresseform'>
		<legend>Anhörig #1</legend>
		<ul>
			<li>
				<label for='contact_list[contacts][contact_14][details]'>Namn:". $form_val_required_star ."</label>
				<input type='text' size='30' ". $form_val_required_attribute ." name='contact_list[contacts][contact_14][details]' id='contact_list[contacts][contact_14][details]' value='".$_POST['contact_list']['contacts']['contact_14']['details']."' placeholder='Förnamn Efternamn'>
			</li>

			<li>
				<label for='contact_list[contacts][contact_33][details]'>E-post:". $form_val_required_star ."</label>
				<input type='email' size='30' ". $form_val_required_attribute ." name='contact_list[contacts][contact_33][details]' id='contact_list[contacts][contact_33][details]' value='".$_POST['contact_list']['contacts']['contact_33']['details']."' placeholder='E-postadress'>
			</li>

			<li>
				<label for='contact_list[contacts][contact_38][details]'>Mobiltelefon:</label>
				<input type='tel' pattern='[\d\s-]*' size='15' name='contact_list[contacts][contact_38][details]' id='contact_list[contacts][contact_38][details]' value='".$_POST['contact_list']['contacts']['contact_38']['details']."' placeholder='070-1234567'>
			</li>

			<li>
				<label for='contact_list[contacts][contact_43][details]'>Hemtelefon:</label>
				<input type='tel' pattern='[\d\s-]*' size='15' name='contact_list[contacts][contact_43][details]' id='contact_list[contacts][contact_43][details]' value='".$_POST['contact_list']['contacts']['contact_43']['details']."' placeholder='08-1234567'><br/>
			</li>
		</ul>
	</fieldset>

	</br>

	<fieldset class='intresseform'>
		<legend>Anhörig #2</legend>
		<ul>
			<li>
				<label for='contact_list[contacts][contact_16][details]'>Namn:</label>
				<input type='text' size='30' name='contact_list[contacts][contact_16][details]' id='contact_list[contacts][contact_16][details]' value='".$_POST['contact_list']['contacts']['contact_16']['details']."' placeholder='Förnamn Efternamn'><br/>
			</li>

			<li>
				<label for='contact_list[contacts][contact_34][details]'>E-post:</label>
				<input type='email' size='30' name='contact_list[contacts][contact_34][details]' id='contact_list[contacts][contact_34][details]' value='".$_POST['contact_list']['contacts']['contact_34']['details']."' placeholder='E-postadress'>
			</li>

			<li>
				<label for='contact_list[contacts][contact_39][details]'>Mobiltelefon:</label>
				<input type='tel' pattern='[\d\s-]*' size='15' name='contact_list[contacts][contact_39][details]' id='contact_list[contacts][contact_39][details]' value='".$_POST['contact_list']['contacts']['contact_39']['details']."' placeholder='070-1234567'>
			</li>

			<li>
				<label for='contact_list[contacts][contact_44][details]'>Hemtelefon:</label>
				<input type='tel' pattern='[\d\s-]*' size='15' name='contact_list[contacts][contact_44][details]' id='contact_list[contacts][contact_44][details]' value='".$_POST['contact_list']['contacts']['contact_44']['details']."' placeholder='08-1234567'>
			</li>
		</ul>
	</fieldset>

	</br>

	<fieldset class='intresseform'>
		<legend>Övrigt</legend>
		<ul>
			<li>
				<label for='avdelning'>Vilken/Vilka avdelning är av intresse?*
					<ul>
						<li>Ej börjat skolan</li>
						<li>Skolår 1: söndag (Bävrarna)</li>
						<li>Skolår 2-3: måndag (Insekterna)</li>
						<li>Skolår 2-3: tisdag (Gnagarna)</li>
						<li>Skolår 4-5: måndag (Asarna)</li>
						<li>Skolår 4-5: tisdag (Skogsbrynet)</li>
						<li>Skolår 6-8: onsdag (Stigfinnarna)</li>
						<li>Skolår 9- gymnasiet åk3: söndag (Mulle)</li>
						<li>Jag önskar börja som ledare</li>
					</ul>
				</label>
				<input type='text' size='30' required name='avdelning' id='avdelning' value='".$_POST['avdelning']."'  placeholder='Bävrarna'>		
			</li>

			<br/>
			<li style='" . $form_val_adult_required_visibility ."'>
				<label for='barn_namn'>Namn på ev barn på samma avdelning:</label>
				<br/>
				<input type='text' size='30' name='barn_namn' id='barn_namn' value='".$_POST['barn_namn']."'  placeholder='Ebbe'>
			</li>

			<br/>
			<li>
				<label for='ledarintresse'>Förälder ställer gärna upp som ledare:*</label>
				<br/>
				<input type='radio' name='ledarintresse' id='ledarintresse' value='1' required>Ja. Ledares barn har förtur i kön!</input>
				<br/>
				<input type='radio' name='ledarintresse' value=''>Nej</input>
			</li>

			<br/>
			<li>
				<label for='hjalpatill'>Annat föräldrar kan hjälpa till med:</label>
				<br/>
				<input type='checkbox' name='hjalpatill_2' id='hjalpatill' value='Hjälpa på hajker'>Kan hjälpa till på hajker och läger med t.ex. matlagning</input><br/>

				<input type='checkbox' name='hjalpatill_4' id='hjalpatill' value='Hjälpa som hantverkare'>Kan hjälpa till med enklare hantverkssysslor</input><br/>

				<input type='checkbox' name='hjalpatill_8' id='hjalpatill' value='Ordna rabatter'>Kan ordna rabatter i för scouting relevanta butiker</input><br/>

				<input type='checkbox' name='hjalpatill_16' id='hjalpatill' value='Hjälpa på annat sätt'>Kan hjälpa till på annat sätt (använd textfält nedan)</input><br/>
			</li>

			<li>
				<label for='profile[note]'>Övrigt</label>
				<textarea cols='30' rows='4' name='profile[note]' placeholder='Något övrigt som bör läggas in i medlemsregistret?'>".$_POST['profile']['note']."</textarea>
			</li>
		</ul>
	</fieldset>

	</br>

	<fieldset class='intresseform'>

		<legend>Skicka in</legend>
		<p>Genom att skicka in denna intresseanmälan hamnar uppgifterna i kårens väntelista.
		<br>Man kommer bli medlem först efter man börja i scouterna.
		<br>Har ni valt att börja snarast så kommer ni inom en snar framtid få mer information om detta.
		<br>Är scouten för ung eller om ni valt att vänta till nästa terminsstart så kommer ni få mer information när det blir aktuellt att börja.</p>
		<input type='submit' value='Skicka!'>

	</fieldset>

</form>";


// --- Om någon postat formuläret körs nedanstående kod för att ta emot och skicka vidare till Scoutnet
if ($_POST)
{ 
	$new_post_array = $_POST; //läs in postad data
	// print_r($_POST);

	// vi lägger till en massa fält som behövs och lägger värden till dessa

	$new_post_array['membership']['status']=1;	//1=Väntar på godkännade, 2 Sätt i väntelista, 4 Godkänn
	$new_post_array['address_list']['addresses']['address_1']['address_type'] = 0;	//0=Hemadress
	$new_post_array['address_list']['addresses']['address_1']['country_code'] = 752;
	$new_post_array['profile']['newsletter'] = 1;	//Vill ha nyhetsbrev, 0=Nej, 1=Ja
	$new_post_array['profile']['product_subscription_8'] = 1;	//Vill ha medlemstidning
	$new_post_array['profile']['preferred_culture'] = 'sv';		//Sätter språk till svenska
	$new_post_array['contact_list']['contacts']['contact_1']['contact_type_id'] = 1;
	$new_post_array['contact_list']['contacts']['contact_14']['contact_type_id'] = 14;
	$new_post_array['contact_list']['contacts']['contact_33']['contact_type_id'] = 33;
	$new_post_array['contact_list']['contacts']['contact_38']['contact_type_id'] = 38;

	//vi behandlar formulärets värden och moddar lite innan vi skickar till Scoutnet
	if (0==$form_val || 1==$form_val)	{
		$new_post_array['profile']['email'] = $new_post_array['contact_list']['contacts']['contact_33']['details']; //sätt primär epost från förälder1
	}	

	//Fixa telefonnummer
	$nummer_mobil = "";
	$nummer_mamma_mobil = "";
	$nummer_mamma_hem = "";
	$nummer_pappa_mobil = "";
	$nummer_pappa_hem = "";

	if ( ! empty($new_post_array['contact_list']['contacts']['contact_1']['details']))	{ //Mobil
		$nummer_mobil = $new_post_array['contact_list']['contacts']['contact_1']['details'];
		$nummer_mobil = fixphonenumber($nummer_mobil);
		$new_post_array['contact_list']['contacts']['contact_1']['details'] = $nummer_mobil; 
	}

	if ( ! empty($new_post_array['contact_list']['contacts']['contact_38']['details']))	{ //Mamma mobil
		$nummer_mamma_mobil = $new_post_array['contact_list']['contacts']['contact_38']['details'];
		$nummer_mamma_mobil = fixphonenumber($nummer_mamma_mobil);
		if ($nummer_mamma_mobil == $nummer_mobil)	{
			$nummer_mamma_mobil = "";
		}
		$new_post_array['contact_list']['contacts']['contact_38']['details'] = $nummer_mamma_mobil;	
	}

	if ( ! empty($new_post_array['contact_list']['contacts']['contact_43']['details']))	{ //Mamma hem
		$nummer_mamma_hem = $new_post_array['contact_list']['contacts']['contact_43']['details'];
		$nummer_mamma_hem = fixphonenumber($nummer_mamma_hem);
		if ( ! empty($nummer_mamma_hem))	{
			if (($nummer_mamma_hem == $nummer_mamma_mobil) || ($nummer_mamma_hem == $nummer_mobil))	{
				$nummer_mamma_hem = "";
			}
		}
		$new_post_array['contact_list']['contacts']['contact_43']['details'] = $nummer_mamma_hem;
	}

	if ( ! empty($new_post_array['contact_list']['contacts']['contact_39']['details']))	{ //Pappa mobil
		$nummer_pappa_mobil = $new_post_array['contact_list']['contacts']['contact_39']['details'];
		$nummer_pappa_mobil = fixphonenumber($nummer_pappa_mobil);
		if ( ! empty($nummer_pappa_mobil))	{
			if (($nummer_pappa_mobil == $nummer_mamma_hem) || ($nummer_pappa_mobil == $nummer_mamma_mobil) || ($nummer_pappa_mobil == $nummer_mobil))	{
				$nummer_pappa_mobil = "";
			}
		}
		$new_post_array['contact_list']['contacts']['contact_39']['details'] = $nummer_pappa_mobil;
	}

	if ( ! empty($new_post_array['contact_list']['contacts']['contact_44']['details']))	{ //Pappa hem
		$nummer_pappa_hem = $new_post_array['contact_list']['contacts']['contact_44']['details'];
		$nummer_pappa_hem = fixphonenumber($nummer_pappa_hem);
		if ( ! empty($nummer_pappa_hem))	{
			if (($nummer_pappa_hem == $nummer_pappa_mobil) || ($nummer_pappa_hem == $nummer_mamma_hem) || ($nummer_pappa_hem == $nummer_mamma_mobil) || ($nummer_pappa_hem == $nummer_mobil))	{
				$nummer_pappa_hem = "";
			}
		}		
		$new_post_array['contact_list']['contacts']['contact_44']['details'] = $nummer_pappa_hem;
	}	

	
	//$im_value = 0; //reset IM field value

	if (! empty($new_post_array['ledarintresse'])) {
		$new_post_array['profile']['note'] .= " Ledarintresse. ";

		$new_post_array['contact_list']['contacts']['contact_60']['details'] = '1';
        //        $im_value = $im_value + 1;
	}

	if (! empty($new_post_array['hjalpatill_2'])) {
		$new_post_array['profile']['note'] .= " ".$new_post_array['hjalpatill_2']." ";
        //        $im_value = $im_value + 2;
	}

	if (! empty($new_post_array['hjalpatill_4'])) {
		$new_post_array['profile']['note'] .= " ".$new_post_array['hjalpatill_4']." ";
        //      $im_value = $im_value + 4;
	}

	if (! empty($new_post_array['hjalpatill_8'])) {
		$new_post_array['profile']['note'] .= " ".$new_post_array['hjalpatill_8']." ";
        //        $im_value = $im_value + 8;
	}

	if (! empty($new_post_array['hjalpatill_16'])) {
		$new_post_array['profile']['note'] .= " ".$new_post_array['hjalpatill_16']." ";
      //          $im_value = $im_value + 16;
	}

	/*
	if ($im_value > 0) {
		$new_post_array['contact_list']['contacts']['contact_7']['contact_type_id']=7;
		$new_post_array['contact_list']['contacts']['contact_7']['details'] = $im_value; // Sätt IM-fältet till en binärkod som motsvarar kryssrutorna i formuläret
	}*/

	if (! empty($new_post_array['contact_list']['contacts']['contact_16']['details'])) { $new_post_array['contact_list']['contacts']['contact_16']['contact_type_id']=16; }

	if (! empty($new_post_array['contact_list']['contacts']['contact_34']['details'])) { $new_post_array['contact_list']['contacts']['contact_34']['contact_type_id']=34; }

	if (! empty($new_post_array['contact_list']['contacts']['contact_39']['details'])) { $new_post_array['contact_list']['contacts']['contact_39']['contact_type_id']=39; } 

	if (! empty($new_post_array['contact_list']['contacts']['contact_44']['details'])) { $new_post_array['contact_list']['contacts']['contact_44']['contact_type_id']=44; }

	if (! empty($new_post_array['contact_list']['contacts']['contact_43']['details'])) { $new_post_array['contact_list']['contacts']['contact_43']['contact_type_id']=43; }

	if (! empty($new_post_array['contact_list']['contacts']['contact_60']['details'])) { $new_post_array['contact_list']['contacts']['contact_60']['contact_type_id']=60; }

	//$ssno = $new_post_array['profile']['ssno']; //läs ut personnumret för att fylla i date_of_birth automatiskt
	$new_post_array['profile']['ssno'] = $new_post_array['year_of_birth']. $new_post_array['month_of_birth']. $new_post_array['day_of_birth']. $new_post_array['birth_number'];
	$ssno = $new_post_array['profile']['ssno'];

	$new_post_array['profile']['date_of_birth'] = substr($ssno, 0, 4)."-".substr($ssno, 4, 2)."-".substr($ssno, 6, 2); //FIXA SÅ DET SER UT SOM DATUM MED BINDESTRÄCK I

	if ( (substr($ssno, 10, 1)==0) OR (substr($ssno, 10, 1)==2) OR (substr($ssno, 10, 1)==4) OR (substr($ssno, 10, 1)==6) OR (substr($ssno, 10, 1)==8) ) { $new_post_array['profile']['sex'] = 2; } //Kvinna

	if ( (substr($ssno, 10, 1)==1) OR (substr($ssno, 10, 1)==3) OR (substr($ssno, 10, 1)==5) OR (substr($ssno, 10, 1)==7) OR (substr($ssno, 10, 1)==9) ) { $new_post_array['profile']['sex'] = 1; } //Man

	/****Ta reda på startår/gren****/
	$birth_year = substr($ssno, 0, 4);
	$birth_year_text = "<p>Födelseår: " . $birth_year . "</p>";
	
	$d = new DateTime();
	//$d->setDate(2021,7,14);	//För testning

	$this_year = $d->format('Y');
	if ($d->format('n') > 6)	{
		$this_year = $this_year + 1;
	}
	$school_year = $this_year - $birth_year - 7;
	$school_year_text = "<p>Skolår: " . $school_year . "</p>";
	
	/******Ändra här om startålder i kåren förändras******/
	//Årskurs man får börja i scoutkåren
	$start_school_year = 0;
	//Ändra också texten i switch-satsen för respektive årskurs i skolan
	/*********************************************/
	
	$can_start_scouting = $this_year - $school_year - 1 + $start_school_year;
	
	$gren = "<p>Gren: " . getGren($school_year, $can_start_scouting) . "</p>";
	
	/*****************************************/
	// Behandling klar. Nu skickar vi moddad POST till SCOUTNET


	$query_string = http_build_query($new_post_array);
	 //echo $url ."?". $query_string; //debug
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_URL, $url ."?". $query_string); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //cmj fix för scoutnet ssl
    curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, FALSE); //cmj fix2 för scoutnet ssl på dev-miljön

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	$brod_text1 = $new_post_array['profile']['first_name'] . " " .$new_post_array['profile']['last_name'];
	$brod_text2 = $birth_year_text . $school_year_text . $gren;

    if ( $status != 201 ) { // Något gick fel
		if (curl_error($curl)<>"") { // det var ett uppkopplingsfel mot Scoutnet

			echo "<div class='cmj_error'><p>Fel Uppstod!. Uppkopplingen mot Scoutnet misslyckades:<br/>";
			print curl_error($curl);
			echo "</p></div>";
		}
		$response_array=json_decode($json_response, true); //läs in svaret i en array.

//Dölj vid release

//		print_r($response_array); // DEBUG

		for ($i = 0; $i<=10; $i++) { // leta först upp om felet är att profilen redan finns i Scoutnet (exempelvis att man står i kö till en annan scoutkår

			if(strpos($response_array['profile'][$i]['msg'], "Personnumret är redan registrerat på medlem") !== FALSE)  {

		    	echo "<div class='cmj_success'><p>".$success_msg . $success_msg_sign."</p></div>";
		    	echo $success_ga;
		    	$fake_success = 1;		

				//Maila medlemsreg samt den som fyllde i formuläret och visa en snygg bekräftelse.

				$mail = new PHPMailer;
				$mail->CharSet = 'UTF-8';
				$mail->From = $from_email;
				$mail->FromName = $from_name;
				$mail->isHTML(true);	
				$mail->Subject = $success_subject . $new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name'];

				/*Om redan börjat*/
				if(1==$form_val || 2==$form_val)	{
					$vuxen_text = "";
					if(2==$form_val)	{
						$vuxen_text = "<p>Denna person är vuxen</p> <p>Namn på barn är: <b>" . $new_post_array['barn_namn'] . "</b></p>";
					}
					$mail->Body = $brod_text1 . " har börjat hos <b>" .$new_post_array['avdelning'] . "</b> och är nu registrerad i Scoutnet" . $vuxen_text . $brod_text2;
					
					$mail->addAddress($medlemsreg_email);						//Maila medlemsreg
				}
				/*************/
				/*Om intresseanmälan****/
				else	{
					$mail->Body = $brod_text1 . " vill börja på avdelning: <b>" .$new_post_array['avdelning']. "</b>" . $brod_text2 . $success_msg . $success_msg_sign;

					$mail->addAddress($new_post_array['profile']['email']); 	//Maila medlem

					$mail->addCC($medlemsreg_email);							//Maila medlemsreg också
				/**************/
				}

				// Nu skickar vi iväg mailet
				if ($mail->send()) {
					if(0==$form_val){
//Om intresseanmälan///				
						echo("<p>Bekräftelse skickad till ".$new_post_array['profile']['email']."</p>");
/////////////////
					}
					echo("<p>Bekräftelse skickad till medlemsregistreraren</p>");

				} else	{
					echo('Kunde inte skicka bekräftelsemail. FEL: '. $mail->ErrorInfo);
				}

				// vi behöver också maila alla ifyllda uppgifter till medlemsreg, eftersom det kan vara bättre än de som redan står i Scoutnet

				$mail = new PHPMailer;
				$mail->CharSet = 'UTF-8';
				$mail->From = $from_email;
				$mail->FromName = $from_name;
				$mail->isHTML(true);

					$medlemsreg_body_svar = $response_array['profile'][$i]['msg']; //svar

					$medlemsreg_body_medlemsnummer = preg_replace('/[^0-9]/', '', $medlemsreg_body_svar);

					/****Personuppgifter*****/
					$medlemsreg_body_Namn = "<p><b>Personuppgifter</b></p> <p>Namn: " .$new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name']."</p>";

					$medlemsreg_body_Ssno = "<p>Personnummer: " .$new_post_array['profile']['ssno']. "</p>";

					$medlemsreg_body_Adress = "<p>Adress: " .$new_post_array['address_list']['addresses']['address_1']['address_line1'].", ".$new_post_array['address_list']['addresses']['address_1']['zip_code']." ". $new_post_array['address_list']['addresses']['address_1']['zip_name']. "</p>";

					$medlemsreg_body_Epost = "<p>E-post: " .$new_post_array['profile']['email']. "</p>";

					$medlemsreg_body_Mobil = "<p>Mobil: " .$new_post_array['contact_list']['contacts']['contact_1']['details']. "</p>";

					$medlemsreg_body_Member = $medlemsreg_body_Namn . $medlemsreg_body_Ssno . $medlemsreg_body_Adress . $medlemsreg_body_Epost . $medlemsreg_body_Mobil;
					/*****************/
					
					/****Målsman#1*****/
					$medlemsreg_body_Malsman_1_Namn = "<p><b>Anhörig #1</b></p> <p>Namn: ". $new_post_array['contact_list']['contacts']['contact_14']['details']."</p>";

					$medlemsreg_body_Malsman_1_Epost = "<p>E-post: ". $new_post_array['contact_list']['contacts']['contact_33']['details']."</p>";

					$medlemsreg_body_Malsman_1_Mobil = "<p>Mobil: ". $new_post_array['contact_list']['contacts']['contact_38']['details']."</p>";

					$medlemsreg_body_Malsman_1_Hemtele = "<p>Hemtele: ". $new_post_array['contact_list']['contacts']['contact_43']['details']."</p>";


					$medlemsreg_body_Malsman_1 = $medlemsreg_body_Malsman_1_Namn . $medlemsreg_body_Malsman_1_Epost . $medlemsreg_body_Malsman_1_Mobil . $medlemsreg_body_Malsman_1_Hemtele;
					/******************/					

					/******Målsman#2********/
					$medlemsreg_body_Malsman_2_Namn = "<p><b>Anhörig #2</b></p> <p>Namn: ". $new_post_array['contact_list']['contacts']['contact_16']['details']."</p>";

					$medlemsreg_body_Malsman_2_Epost = "<p>E-post: ". $new_post_array['contact_list']['contacts']['contact_34']['details']."</p>";

					$medlemsreg_body_Malsman_2_Mobil = "<p>Mobil: ". $new_post_array['contact_list']['contacts']['contact_39']['details']."</p>";

					$medlemsreg_body_Malsman_2_Hemtele = "<p>Hemtele: ". $new_post_array['contact_list']['contacts']['contact_44']['details']."</p>";

					$medlemsreg_body_Malsman_2 = $medlemsreg_body_Malsman_2_Namn . $medlemsreg_body_Malsman_2_Epost . $medlemsreg_body_Malsman_2_Mobil . $medlemsreg_body_Malsman_2_Hemtele;
					/**********************/

					/////Övrigt
					$medlemsreg_body_Ovrigt_Avdelning = "<p><b>Övrigt</b></p> <p>Avdelning: " .$new_post_array['avdelning']."</p>";

					$medlemsreg_body_Ovrigt_Barn_namn = "<p>Namn på barn: ". $new_post_array['barn_namn'] ."</p>";

					$medlemsreg_body_Ovrigt_Annat = $new_post_array['profile']['note'];

					$medlemsreg_body_Ovrigt = $medlemsreg_body_Ovrigt_Avdelning . $medlemsreg_body_Ovrigt_Barn_namn . $medlemsreg_body_Ovrigt_Annat;

					$medlemsreg_body_alla_uppgifter = $medlemsreg_body_Member . $medlemsreg_body_Malsman_1 . $medlemsreg_body_Malsman_2 . $medlemsreg_body_Ovrigt;


				$mail->Subject = "Detaljer anmälan för " .$new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name']. ", ". $medlemsreg_body_medlemsnummer;

				$medlemsreg_body = "<p><font size='7'>". $medlemsreg_body_medlemsnummer ."</font></p><p><b>Anmälan för person som redan har profil i Scoutnet.</b> Denna profil måste importeras manuellt!</p><p>". $response_array['profile'][$i]['msg'] ."</p>" . $medlemsreg_body_alla_uppgifter . "<p>". print_r($new_post_array, true) . "<p></p>";

            	$mail->Body = $medlemsreg_body;

				$mail->addAddress($medlemsreg_email);

				// Nu skickar vi iväg mailet

				if ($mail->send()) {
		 			echo("<p>Personen är redan registrerad i Scoutnet. Uppgifter skickade till medlemsregistreraren för manuell hantering.</p>");
				} else {
					echo('Kunde inte skicka bekräftelsemail. FEL: '. $mail->ErrorInfo);
				}
			}
		}

		if ($fake_success =='') { //Scoutnet returnerade ett felmeddelande
        	echo "<div class='cmj_error'>";
			echo "<p>Oops, något gick fel. Kontrollera uppgifterna och försök igen!</p>";
			echo "<p>".$status . " : " . $json_response ."</p>";

			for ($i = 0; $i<=10; $i++) {

				if (isset($response_array['profile'][$i]['key'])) {echo $response_array['profile'][$i]['key'] ." : " . $response_array['profile'][$i]['msg'] ."<br/>";}

				if (isset($response_array['address_list'][$i]['key'])) {echo $response_array['address_list'][$i]['key'] ." : " . $response_array['address_list'][$i]['msg'] ."<br/>";}
			}
			// TODO: Fixa ovanstående bättre med en ball loop..
			// TODO: Visa vilket fält som krånglade!! 
			echo "<br>";
			echo "</div>";
			echo $formular; // skriv ut formuläret igen,
		}
	
	} else { // Anmälan postades OK

		echo "<div class='cmj_success'>";      
		echo "<p>".$success_msg.$success_msg_sign."</p>";
		// echo "<p>".$status . " : " . $json_response ."</p>";
	    echo "</div>";	
		echo $success_ga;	
		//Maila medlemsreg samt den som fyllde i formuläret och visa en snygg bekräftelse.
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		$mail->From = $from_email;
		$mail->FromName = $from_name;
		$mail->isHTML(true);

		$mail->Subject = $success_subject . $new_post_array['profile']['first_name'] ." ".$new_post_array['profile']['last_name'];		

		if(0==$form_val)	{

		/******Om intresseanmälan************/

			$mail->Body = $brod_text1 . " vill börja på avdelning: <b>" .$new_post_array['avdelning']. "</b>" . $brod_text2 . $success_msg. $success_msg_sign;

			$mail->addAddress($new_post_array['profile']['email']);

			$mail->addCC($medlemsreg_email);
		}

		else	{
		/*******Om redan börjat eller är vuxen*********/
			$vuxen_text = "";
			if(2==$form_val)	{
				$vuxen_text = "<p>Denna person är vuxen</p> <p>Namn på barn är: <b>" . $new_post_array['barn_namn'] . "</b></p>";
			}
			$mail->Body = $brod_text1 . " har börjat hos <b>" .$new_post_array['avdelning'] . "</b> och är nu registrerad i Scoutnet" . $vuxen_text . $brod_text2;
						
			$mail->addAddress($medlemsreg_email);
		/************************/
		}
		// Nu skickar vi iväg mailet
		if ($mail->send())	{
			if(0==$form_val){
		/****Om intresseanmälan*******/	
			echo("<p>Bekräftelse skickad till ".$new_post_array['profile']['email']."</p>");
		/******************** */
			}
			echo("<p>Bekräftelse skickad till medlemsregistreraren</p>");
		} else	{
			echo('Kunde inte skicka bekräftelsemail. FEL: '. $mail->ErrorInfo);
		}
	}

    curl_close($curl);

}	else	{// det är inte en POST (första gången någon kommer till sidan, vi visar formuläret
	echo $formular;
}


/**Givet ett skolår ger aktuell gren**/
function getGren($school_year, $can_start_scouting) {
	
	switch ($school_year) {
		case "-6":
			return "Börja HT " . $can_start_scouting;
		case "-5":
			return "Börja HT " . $can_start_scouting;
		case "-4":
			return "Börja HT " . $can_start_scouting;
		case "-3":
			return "Börja HT " . $can_start_scouting;
		case "-2":
			return "Börja HT " . $can_start_scouting;
		case "-1":
			return "Börja HT " . $can_start_scouting;
		case "0":
			return "Letare 1:a";
		case "1":
			return "Letare 2:a";
		case "2":
			return "Spårare 1:a";
		case "3":
			return "Spårare 2:a";
		case "4":
			return "Upptäckare 1:a";
		case "5":
			return "Upptäckare 2:a";
		case "6":
			return "Äventyrare 1:a";
		case "7":
			return "Äventyrare 2:a";
		case "8":
			return "Äventyrare 3:a";
		case "9":
			return "Utmanare 1:a";
		case "10":
			return "Utmanare 2:a";
		default:
			return "";
	}
}


/*******Lösa funktioner*/
function fixphonenumber($inputnumber)	{

    $number = preg_replace('~\D~', '',$inputnumber);

    $firstnumbers = substr($number, 0, 2);	

    if ($firstnumbers == "07") {

        $number = substr_replace($number,"-",3,0);

        $number = substr_replace($number," ",7,0);

        $number = substr_replace($number," ",10,0);

    } else if ($firstnumbers == "08") {

        $number = substr_replace($number,"-",2,0);

        if (strlen($number) == 11) {

            $number = substr_replace($number," ",6,0);

            $number = substr_replace($number," ",10,0);

        } else if (strlen($number) == 10) {

            $number = implode(" ",str_split($number,6));

            $number = implode(" ",str_split($number,9));

        } else if (strlen($number) == 9) {

            $number = implode(" ",str_split($number,5));

            $number = implode(" ",str_split($number,8));
        }
    }
		return $number;
}
?>