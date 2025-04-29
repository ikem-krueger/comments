<?php
/*
 |  Komment     The second native FlatFile Comment Plugin 4 Bludit
 |  @file       ./admin/index-users.php
 |  @author     Ikem Krueger <ikem.krueger@gmail.com>
 |  @version    0.1.2 [0.1.0] - Alpha
 |
 |  @website    https://github.com/ikem-krueger/komment
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 SamBrishes, 2025 Ikem Krueger
 */
    if(!defined("BLUDIT")){ die("Go directly to Jail. Do not pass Go. Do not collect 200 Cookies!"); }

    global $KommentUsers;

    // Get Data
    $page = max((isset($_GET["page"])? (int) $_GET["page"]: 1), 1);
    $limit = sn_config("frontend_per_page");
    $total = count($KommentUsers->db);

    // Get Users
    $search = null;
    if(isset($_GET["view"]) && $_GET["view"] === "users"){
        $page = 1;
        $limit = -1;
        $search = isset($_GET["search"])? $_GET["search"]: null;
    }
    $users = $KommentUsers->getList($search, $page, $limit);

    // Link
    $link = DOMAIN_ADMIN . "komment?page=%d&tab=users#users";

?>
<div id="komment-users" class="tab-pane">
    <div class="card shadow-sm" style="margin: 1.5rem 0;">
        <div class="card-body">
            <div class="row">
                <form class="col-sm-12" method="get" action="<?php echo DOMAIN_ADMIN; ?>komment#users">
                    <div class="form-row align-items-center">
                        <div class="col">
                            <input type="text" name="search" value="<?php echo $search; ?>" class="form-control" placeholder="<?php sn_e("Username or eMail Address"); ?>" />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" name="view" value="users"><?php sn_e("Search Users"); ?></button>
                        </div>
                    </div>
                </form>

                <div class="col-sm-6 text-right">
                    <?php if($total > $limit){ ?>
                        <div class="btn-group btn-group-pagination">
                            <?php if($page <= 1){ ?>
                                <span class="btn btn-secondary disabled">&laquo;</span>
                                <span class="btn btn-secondary disabled">&lsaquo;</span>
                            <?php } else { ?>
                                <a href="<?php printf($link, 1); ?>" class="btn btn-secondary">&laquo;</a>
                                <a href="<?php printf($link, $page-1); ?>" class="btn btn-secondary">&lsaquo;</a>
                            <?php } ?>
                            <?php if(($page * $limit) < $total){ ?>
                                <a href="<?php printf($link, $page+1); ?>" class="btn btn-secondary">&rsaquo;</a>
                                <a href="<?php printf($link, ceil($total / $limit)); ?>" class="btn btn-secondary">&raquo;</a>
                            <?php } else { ?>
                                <span class="btn btn-secondary disabled">&rsaquo;</span>
                                <span class="btn btn-secondary disabled">&raquo;</span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php if(!$users || count($users) === 0){ ?>
        <div class="row justify-content-md-center">
            <div class="col">
                <div class="card w-100 shadow-sm bg-light">
                    <div class="card-body text-center p-4"><i><?php sn_e("No Users available"); ?></i></div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <?php $link = DOMAIN_ADMIN . "komment?action=komment&komment=users&uuid=%s&handle=%s&tokenCSRF=" . $security->getTokenCSRF(); ?>
        <table class="table mt-3">
            <?php foreach(array("thead") AS $tag){ ?>
                <<?php echo $tag; ?>>
                    <tr>
                        <th width="38%" class="border-0"><?php sn_e("Username"); ?></th>
                        <th width="15%" class="border-0 d-none d-lg-table-cell"><?php sn_e("eMail"); ?></th>
                        <th width="22%" class="border-0 d-none d-lg-table-cell"><?php sn_e("Comments"); ?></th>
                        <th width="25%" class="border-0 text-center d-sm-table-cell"><?php sn_e("Actions"); ?></th>
                    </tr>
                </<?php echo $tag; ?>>
            <?php } ?>

            <tbody>
                <?php foreach($users AS $uuid => $user){ ?>
                    <tr>
                        <td class="p-3">
                            <?php echo $user["username"]; ?>
                        </td>
                        <td class="p-3">
                            <?php echo $user["email"]; ?>
                        </td>
                        <td class="p-3">
                            <a href="<?php echo DOMAIN_ADMIN; ?>komment?view=user&user=<?php echo $uuid; ?>">
                                <?php echo count(isset($user["comments"])? $user["comments"]: array()); ?>
                                <?php sn_e("Comments"); ?>
                            </a>
                        </td>
                        <td class="text-center align-middle pt-2 pb-2 pl-1 pr-1">
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                    <?php sn_e("Handle"); ?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item text-danger" href="<?php printf($link, $uuid, "delete"); ?>&anonymize=true"><?php sn_e("Delete (Anonymize)"); ?></a>
                                    <a class="dropdown-item text-danger" href="<?php printf($link, $uuid, "delete"); ?>&anonymize=false"><?php sn_e("Delete (Completely)"); ?></a>
                                    <div class="dropdown-divider"></div>

                                    <?php if($user["blocked"]){ ?>
                                        <a class="dropdown-item" href="<?php printf($link, $uuid, "unblock"); ?>"><?php sn_e("Unblock User"); ?></a>
                                    <?php } else { ?>
                                        <a class="dropdown-item" href="<?php printf($link, $uuid, "block"); ?>"><?php sn_e("Block User"); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
