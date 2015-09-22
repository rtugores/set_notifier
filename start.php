<?php

function set_notifier_init() {
				
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'set_notifier_owner_block_menu');
    elgg_register_page_handler('set_notifier', 'set_notifier_page_handler');

}
elgg_register_event_handler('init','system','set_notifier_init');

/**
 * Add a menu item to an ownerblock
 */
function set_notifier_owner_block_menu($hook, $type, $return, $params) {
    $user = elgg_get_logged_in_user_entity();
    if (elgg_instanceof($params['entity'], 'user') && $user->isAdmin())  {
         $url = "set_notifier/set";
         $item = new ElggMenuItem('set_notifier_set', elgg_echo('set_notifier:set'), $url);
         $return[] = $item;
    }
    return $return;
}

function set_notifier_page_handler($page, $identifier) {

    // select page based on first URL segment after /set_notifier/
    switch ($page[0]) {
        case 'set':
            gatekeeper();

            $num_users = 1;
            $limit = 100;
            $offset = 0;

            while($num_users != 0){
                $users = elgg_get_entities(array(
                    'types' => 'user',
                    'limit' => $limit,
                    'offset' => $offset,
                ));
                if ($users !=0){
                    foreach($users as $user){
                        $prefix = "notification:method:notifier";
                        $user->$prefix = true;
                        $user->collections_notifications_preferences_notifier = '-1';
                        $user->save();
                    }
                }
                $num_users = count ($users);
                $offset += $limit;
            }

            forward(REFERER);
            break;
    
                
        default:
            echo "request for $identifier $page[0]";
            break;
    }
    // return true to let Elgg know that a page was sent to browser
    return true;
}

?>
