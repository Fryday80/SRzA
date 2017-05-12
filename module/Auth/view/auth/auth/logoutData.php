<?php
//@todo get active user data here
$activeUsers = array ("salt", "FryDay");
$userListItems = array();
foreach ($activeUsers as $activeUser) {
    array_push($userListItems, array(
        "name" => $activeUser,
        "url" => "/profile/$activeUser",
    ));
}
//@todo create this somewhere else
$logOutList = array(
  0 => array (
      "name" => "mein Menü",
      "class" => "my-menu",
      "list" => array (
          0=> array(
              "name" => "Mein Profil",
              "url" => "#",
          ),
          1=> array(
              "name" => "Meine Charaktere",
              "url" => "#",
          ),
      ),
  ),
  1 => array (
      "name" => "Active Users",
      "class" => "active-users",
      "list" => $userListItems,
  ),
);

function createLogoutList($logOutList){
    $return = '<ul class="logout-list">';
    foreach ($logOutList as $subList){
        $return .= '<li class="logout-list '. $subList['class'] . '"> <span>' . $subList['name'] . '</span> <ul>';
        foreach ($subList['list'] as $item){
            $return .= '<li><a href="' . $item['url'] . '">'. $item['name'] .'</a> </li>';
        }
        $return .= '</ul></li>';
    }
    $return .= '</ul>';
    return $return;
}
?>
<br/>
<div>
    <div>
        <br/>
    </div>
    <div>
        <?php echo createLogoutList($logOutList); ?>
    </div>
</div>
