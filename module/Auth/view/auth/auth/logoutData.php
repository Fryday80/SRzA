<style>
    ul.logout-list>li>ul>li:hover {
        background: linear-gradient(to left, #FAEBd7, #AFA08D);
    }
    ul.logout-list,
    ul.logout-list *
    {
        list-style: none;
    }
    li.logout-list>span{
    }
    li.logout-list>ul{
        border: solid 1px;
        margin-left: 10px;
    }
    .active-user-icon{
        height: 18px;
        width: 20px;
        float: right;
    }
</style>
<?php
//$logOutList = $myMenuService->getLogoutData();
$logOutList = array(
    0 => array(
        'name'  => 'Benutzer Daten',
        'class' => 'myMenu',
        'list'  => array(
            0 => array(
                'name' => 'Mein Profil Bearbeiten',
                'url'  => '/profile'
            ),
            1 => array(
                'name' => 'Meine Charaktere Bearbeiten',
                'url'  => '/profile#characters'
            ),
        ),
    ),
);
echo createLogoutList($logOutList);


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
<!--        --><?php //echo createLogoutList($logOutList); ?>
    </div>
</div>
