<?php

defined('__MAINAPP__') or die('nope');


//Override the language:
$lang_override = array(
	'ldap:19'=>'Waarde is te kort of te lang',
	'ldap:53'=>'Waarde voldoet niet aan beleid',
	'ldap:68'=>'Dit record bestaat al',
	'ldap:50'=>'Toegang tot waarde geweigerd',
	'error:to_short'=>'Waarde te kort',
	'error:to_long'=>'Waarde te lang',
	'error:invalid_email'=>'Waarde is geen email adres',
	'error:filter'=>'Filter staat geen toegang tot deze waarde toe',
	'error:login_failed'=>'Kan niet inloggen met deze gegevens',
	'field:cn'=>'Weergavenaam',
	'field:password'=>'Wachtwoord',
	'field:password_repeat'=>'Herhaal',
	'field:user:samaccountname'=>'Gebruikersnaam',
	'field:user:displayname'=>'Weergavenaam',
	'field:user:givenname'=>'Voornaam',
	'field:user:initials'=>'Initialen',
	'field:user:sn'=>'Achternaam',
	'field:user:cn'=>'Weergavenaam',
	'field:user:mail'=>'Email',
	'field:user:department'=>'Afdeling',
	'field:user:title'=>'Functietitel',
	'field:user:unicodepwd'=>'Wachtwoord',
	'field:user:useraccountcontrol'=>'Actief',

	'field:group:samaccountname'=>'Groepsnaam',
	'field:group:member'=>'Leden',
	
	'btn:login'=>'Aanmelden',
	'btn:logout'=>'Afmelden',
	'btn:refresh'=>'Ververs',
	'btn:users'=>'Gebruikers',
	'btn:groups'=>'Groepen',
	'btn:rights'=>'Matrix',
	'btn:create'=>'Maak',
	'btn:logged_in_as'=>'Inglogd als',
	'btn:search'=>'Zoeken',
	'btn:audit'=>'Logboek',
	'txt:version'=>'Versie',
	'txt:startscreen'=>'Beginscherm',
	'txt:login'=>'Aanmelden',
	'txt:login_token'=>'2e factor code',
	'txt:login_help'=>'Meld je aan met je gebruikersnaam, wachtwoord en 2e factor code',
	'txt:action'=>'Actie',
	'txt:datetime'=>'Datum & Tijd',
	'txt:sid'=>'SessieID',
	'txt:user'=>'Gebruiker',
	'txt:dn'=>'Objectnaam',
	'txt:attr'=>'Attribuut',
	'txt:value'=>'Waarde',
	'action:login_success'=>'Gebruiker aanmelden',
	'action:modify'=>'Attribuut wijzigen',
	'action:logout_success'=>'Gebruiker afmelden',
	'action:login_failed'=>'Aanmelden gefaald',
	'action:membership_del'=>'Gebruiker uit groep halen',
	'action:membership_add'=>'Gebruiker in groep zetten',
	'action:new_user'=>'Nieuwe gebruiker maken',
	
);


