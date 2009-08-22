<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/map_zone_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//#############################################################################
// INSTANCES
//#############################################################################
function instances()
{
  global $output, $lang_instances,
    $realm_id, $world_db, $mmfpm_db,
    $server_type, $itemperpage;

  $sqlw = new SQL;
  $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  //-------------------SQL Injection Prevention--------------------------------
  // this page has multipage support and field ordering, so we need these
  $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
  if (preg_match('/^[[:digit:]]{1,5}$/', $start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : 'level_min';
  if (preg_match('/^[_[:lower:]]{1,11}$/', $order_by)); else $order_by='level_min';

  $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;

  // for multipage support
  $all_record = $sqlw->result($sqlw->query('SELECT count(*) FROM instance_template'),0);

  // main data that we need for this page, instances
  if ($server_type)
    $result = $sqlw->query('SELECT map, level_min, level_max, maxPlayers as maxplayers, reset_delay
    FROM instance_template JOIN access_requirement ON access_requirement.id = instance_template.access_id
    ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.';');
  else
    $result = $sqlw->query('SELECT map, levelMin as level_min, levelMax as level_max, maxPlayers as maxplayers, reset_delay
    FROM instance_template ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.';');

  //---------------Page Specific Data Starts Here--------------------------
  // we start with a lead of 10 spaces,
  //  because last line of header is an opening tag with 8 spaces
  //  keep html indent in sync, so debuging from browser source would be easy to read
  $output .= '
          <!-- start of instances.php -->
          <center>
            <table class="top_hidden">
              <tr>
                <td width="25%" align="right">';

  // multi page links
  $output .=
                  $lang_instances['total'].' : '.$all_record.'<br /><br />'.
                  generate_pagination('instances.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);

  // column headers, with links for sorting
  $output .= '
                </td>
              </tr>
            </table>
            <table class="lined">
              <tr>
                <th width="40%"><a href="instances.php?order_by=map&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='map' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['map'].'</a></th>
                <th width="15%"><a href="instances.php?order_by=level_min&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='level_min' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['level_min'].'</a></th>
                <th width="15%"><a href="instances.php?order_by=level_max&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='level_max' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['level_max'].'</a></th>
                <th width="15%"><a href="instances.php?order_by=maxplayers&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='maxplayers' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['max_players'].'</a></th>
                <th width="15%"><a href="instances.php?order_by=reset_delay&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='reset_delay' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['reset_delay'].'</a></th>
              </tr>';

  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  while ($instances = $sqlw->fetch_assoc($result))
  {
    $days  = floor(round($instances['reset_delay'] / 3600) / 24);
    $hours = round($instances['reset_delay'] / 3600) - ($days * 24);
    $reset = "";
    if ($days)
      $reset .= $days.' days';
    if ($hours)
      $reset .= $hours.' hours';

    $output .= '
              <tr valign="top">
                <td>'.get_map_name($instances['map'], $sqlm).' ('.$instances['map'].')</td>
                <td>'.$instances['level_min'].'</td>
                <td>'.$instances['level_max'].'</td>
                <td>'.$instances['maxplayers'].'</td>
                <td>'.$reset.'</td>
              </tr>';
  }
  unset($reset);
  unset($hours);
  unset($days);
  unset($instances);
  unset($result);

  $output .= '
              <tr>
                <td colspan="5" class="hidden" align="right" width="25%">';
  // multi page links
  $output .= generate_pagination('instances.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
  unset($start);
  $output .= '
                </td>
              </tr>
              <tr>
                <td colspan="5" class="hidden" align="right">'.$lang_instances['total'].' : '.$all_record.'</td>
              </tr>
            </table>
          </center>
          <!-- end of instances.php -->';

}


//#############################################################################
// MAIN
//#############################################################################

// error variable reserved for future use
//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//unset($err);

$lang_instances = lang_instances();

$output .= '
          <div class="top">
            <h1>'.$lang_instances['instances'].'</h1>
          </div>';

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

instances();

//unset($action);
unset($action_permission);
unset($lang_instances);

require_once 'footer.php';


?>
