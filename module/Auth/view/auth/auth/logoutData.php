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
    .logout-list li>a
    {
        padding-left: 5px;
    }
</style>
<?php
$return = '<ul class="logout-list">';
foreach ($logOutList as $subList){
    $return .= '<li class="logout-list '. $subList['class'] . '"> <span>' . $subList['name'] . '</span> <ul>';
    foreach ($subList['list'] as $item){
        $return .= '<li><a href="' . $item['url'] . '">'. $item['name'] .'</a> </li>';
    }
    $return .= '</ul></li>';
}
$return .= '</ul>';
echo $return;
?>
