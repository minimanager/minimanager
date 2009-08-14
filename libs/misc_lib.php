<?php


//#############################################################################
//get country code and country name by IP
// given IP, returns array('code','country')
// 'code' is country code, 'country' is country name.

function misc_get_country_by_ip($ip, &$sqlm)
{
  $country = $sqlm->fetch_assoc($sqlm->query(
    'SELECT c.code, c.country FROM ip2nationCountries c, ip2nation i
      WHERE i.ip < INET_ATON("'.$ip.'") AND c.code = i.country
        ORDER BY i.ip DESC LIMIT 0,1;'));

  return $country;
}


//#############################################################################
//get country code and country name by IP
// given account ID, returns array('code','country')
// 'code' is country code, 'country' is country name.

function misc_get_country_by_account($account, &$sqlr, &$sqlm)
{
  $ip = $sqlr->fetch_assoc($sqlr->query(
    'SELECT last_ip FROM account WHERE id='.$account.';'));

  return misc_get_country_by_ip($ip['last_ip'], $sqlm);
}


?>
