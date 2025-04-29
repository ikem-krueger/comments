<?php
/*
 |  Komment     The second native FlatFile Comment Plugin 4 Bludit
 |  @file       ./admin/index.php
 |  @author     Ikem Krueger <ikem.krueger@gmail.com>
 |  @version    0.1.2 [0.1.0] - Alpha
 |
 |  @website    https://github.com/ikem-krueger/komment
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 SamBrishes, 2025 Ikem Krueger
 */
    if(!defined("BLUDIT")){ die("Go directly to Jail. Do not pass Go. Do not collect 200 Cookies!"); }

    global $L, $Komment;

    // Pending Counter
    $count = count($Komment->getIndex("pending"));
    $count = ($count > 99)? "99+": $count;
    $spam  = count($Komment->getIndex("spam"));

    // Tab Strings
    $strings = array(
        "pending"       => sn__("Pending"),
        "approved"      => sn__("Approved"),
        "rejected"      => sn__("Rejected"),
        "spam"          => sn__("Spam"),
        "search"        => sn__("Search Comments"),
        "single"        => sn__("Single Comment"),
        "uuid"          => sn__("Page Comments"),
        "user"          => sn__("User Comments")
    );

    // Current Tab
    $view = "index";
    if(isset($_GET["view"]) && in_array($_GET["view"], array("search", "single", "uuid", "user"))){
        $view = $current = $_GET["view"];
        $tabs = array($view);
    } else {
        $current = isset($_GET["tab"])? $_GET["tab"]: "pending";
        $tabs = array("pending", "approved", "rejected", "spam");
    }
?>
<h2 class="mt-0 mb-3">
    <span class="fa fa-comment-o" style="font-size: 0.7em;"></span> <?php sn_e("Comments"); ?>
</h2>

<ul class="nav nav-tabs" data-handle="tabs">
    <?php foreach($tabs AS $tab){ ?>
        <?php $class = "nav-link nav-{$tab}" . ($current === $tab? " active": ""); ?>
        <li class="nav-item">
            <a id="<?php echo $tab; ?>-tab" href="#komment-<?php echo $tab; ?>" class="<?php echo $class; ?>" data-toggle="tab">
                <?php
                    echo $strings[$tab];
                    if($tab === "pending" && !empty($count)){
                        ?> <span class="badge badge-primary"><?php echo $count; ?></span><?php
                    }
                    if($tab === "spam" && !empty($spam)){
                        ?> <span class="badge badge-danger"><?php echo $spam; ?></span><?php
                    }
                ?>
            </a>
        </li>
    <?php } ?>

    <li class="nav-item flex-grow-1"></li>

    <li class="nav-item mr-2">
        <a id="users-tab" href="#komment-users" class="nav-link nav-config" data-toggle="tab">
            <span class="oi oi-people"></span> <?php sn_e("Users"); ?>
        </a>
    </li>
</ul>

<div class="tab-content">
    <?php
        include "index-comments.php";
        include "index-users.php";
        include "index-config.php";
    ?>
</div>
