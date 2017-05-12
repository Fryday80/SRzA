<style>
    ul.logout-list>li>ul>li:hover {
        background: linear-gradient(to left, #FAEBd7, #AFA08D);
    }
    li.logout-list{
        list-style: none;
    }
    .active-user-icon{
        height: 18px;
        width: 20px;
        float: right;
    }
</style>
<?php
$logOutList = $dataService->getLogoutData();

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
