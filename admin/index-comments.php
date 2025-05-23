<?php
/*
 |  Komment     The second native FlatFile Comment Plugin 4 Bludit
 |  @file       ./admin/index-comments.php
 |  @author     Ikem Krueger <ikem.krueger@gmail.com>
 |  @version    0.1.2 [0.1.0] - Alpha
 |
 |  @website    https://github.com/ikem-krueger/komment
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2019 SamBrishes, 2025 Ikem Krueger
 */
    if(!defined("BLUDIT")){ die("Go directly to Jail. Do not pass Go. Do not collect 200 Cookies!"); }

    global $pages, $security, $Komment, $KommentIndex, $KommentPlugin, $KommentUsers;

    // Get Data
    $limit = $KommentPlugin->getValue("frontend_per_page");
    if($limit === 0){
        $limit = 15;
    }
    $current = isset($_GET["tab"])? $_GET["tab"]: "pending";

    // Get View
    $view = "index";
    if(isset($_GET["view"]) && in_array($_GET["view"], array("search", "single", "uuid", "user"))){
        $view = $current = $_GET["view"];
        $tabs = array($view);
    } else {
        $tabs = array("pending", "approved", "rejected", "spam");
    }

    // Render Comemnts Tab
    foreach($tabs AS $status){
        if(isset($_GET["tab"]) && $_GET["tab"] === $status){
            $page = max((isset($_GET["page"])? (int) $_GET["page"]: 1), 1);
        } else {
            $page = 1;
        }

        // Get Comments
        if($view === "index"){
            $comments = $KommentIndex->getList($status, $page, $limit);
            $total = $KommentIndex->count($status);
        } else if($view === "search"){
            $comments = $KommentIndex->searchComments(isset($_GET["search"])? $_GET["search"]: "");
            $total = count($comments);
        } else if($view === "single"){
            $comments = $KommentIndex->getListByParent(isset($_GET["single"])? $_GET["single"]: "");
            $total = count($comments);
        } else if($view === "uuid"){
            $comments = $KommentIndex->getListByUUID(isset($_GET["uuid"])? $_GET["uuid"]: "");
            $total = count($comments);
        } else if($view === "user"){
            $comments = $KommentIndex->getListByUser(isset($_GET["user"])? $_GET["user"]: "");
            $total = count($comments);
        }

        // Render Tab Content
        $link = DOMAIN_ADMIN . "komment?page=%d&tab={$status}#{$status}";
        ?>
            <div id="komment-<?php echo $status; ?>" class="tab-pane <?php echo($current === $status)? "active": ""; ?>">
                <div class="card shadow-sm" style="margin: 1.5rem 0;">
                    <div class="card-body">
                        <div class="row">
                            <form class="col-sm-12" method="get" action="<?php echo DOMAIN_ADMIN; ?>komment">
                                <div class="form-row align-items-center">
                                    <div class="col">
                                        <?php $search = isset($_GET["search"])? $_GET["search"]: ""; ?>
                                        <input type="text" name="search" value="<?php echo $search; ?>" class="form-control" placeholder="<?php sn_e("Comment Title or Excerpt"); ?>" />
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary" name="view" value="search"><?php sn_e("Search Comments"); ?></button>
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

                <?php /* No Comments available */ ?>
                <?php if(count($comments) < 1){ ?>
                        <div class="row justify-content-md-center">
                            <div class="col">
                                <div class="card w-100 shadow-sm bg-light">
                                    <div class="card-body text-center p-4"><i><?php sn_e("No Comments available"); ?></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php continue; ?>
                <?php } ?>

                <?php /* Comments Table */ ?>
                <?php $link = DOMAIN_ADMIN . "komment?action=komment&komment=%s&uid=%s&status=%s&tokenCSRF=" . $security->getTokenCSRF(); ?>
                <table class="table mt-3">
                    <?php foreach(array("thead") AS $tag){ ?>
                        <<?php echo $tag; ?>>
                            <tr>
                                <th width="56%" class="border-0"><?php sn_e("Comment"); ?></th>
                                <th width="22%" class="border-0 d-none d-lg-table-cell"><?php sn_e("Author"); ?></th>
                                <th width="22%" class="border-0 text-center d-sm-table-cell"><?php sn_e("Actions"); ?></th>
                            </tr>
                        </<?php echo $tag; ?>>
                    <?php } ?>
                    <tbody>
                        <?php foreach($comments AS $uid){ ?>
                            <?php
                                $data = $KommentIndex->getComment($uid, $status);
                                if(!(isset($data["page_uuid"]) && is_string($data["page_uuid"]))){
                                    continue;
                                }
                                $user = $KommentUsers->getByString($data["author"]);
                            ?>
                            <tr>
                                <td class="pt-3 pb-3 pl-3 pr-3">
                                    <?php
                                        if($KommentPlugin->getValue("comment_title") !== "disabled" && !empty($data["title"])){
                                            echo '<a href="' . DOMAIN_ADMIN . 'komment/edit/?uid=' . $uid . '">' . $data["title"] . '</a>';

                                        }
                                        echo '<p class="text-muted m-0" style="font-size:12px;">' . (isset($data["excerpt"])? $data["excerpt"]: "") . '</p>';
                                        if(!empty($data["parent_uid"]) && $KommentIndex->exists($data["parent_uid"]) && $view !== "single"){
                                            $reply = DOMAIN_ADMIN . "komment?view=single&single={$uid}";
                                            $reply = '<a href="'.$reply.'" title="'.sn__("Show all replies").'">' . $KommentIndex->getComment($data["parent_uid"])["title"] . '</a>';
                                            echo "<div class='text-muted mt-1' style='font-size:12px;'>" . sn__("Reply To") . ": " . $reply . "</div>";
                                        }
                                    ?>
                                </td>
                                <td class="align-middle pt-2 pb-2 pl-3 pr-3">
                                    <span class="d-inline-block"><?php echo $user["username"]; ?></span>
                                    <small class='d-block'><?php echo $user["email"]; ?></small>
                                </td>
                                <td class="text-center align-middle pt-2 pb-2 pl-1 pr-1">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                            <?php sn_e("Change"); ?>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item text-primary" href="<?php echo DOMAIN_ADMIN . "komment/edit/?uid=" . $uid; ?>"><?php sn_e("Edit Comment"); ?></a>
                                            <a class="dropdown-item text-danger" href="<?php printf($link, "delete", $uid, "delete"); ?>"><?php sn_e("Delete Comment"); ?></a>
                                            <div class="dropdown-divider"></div>

                                            <?php if($status !== "approved"){ ?>
                                                <a class="dropdown-item" href="<?php printf($link, "moderate", $uid, "approved"); ?>"><?php sn_e("Approve Comment"); ?></a>
                                            <?php } ?>
                                            <?php if($status !== "rejected"){ ?>
                                                <a class="dropdown-item" href="<?php printf($link, "moderate", $uid, "rejected"); ?>"><?php sn_e("Reject Comment"); ?></a>
                                            <?php } ?>
                                            <?php if($status !== "spam"){ ?>
                                                <a class="dropdown-item" href="<?php printf($link, "moderate", $uid, "spam"); ?>"><?php sn_e("Mark as Spam"); ?></a>
                                            <?php } ?>
                                            <?php if($status !== "pending"){ ?>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="<?php printf($link, "moderate", $uid, "pending"); ?>"><?php sn_e("Back to Pending"); ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <?php $page = new Page($pages->getByUUID($data["page_uuid"])); ?>
                                    <a href="<?php echo $page->permalink(); ?>#comment-<?php echo $uid; ?>" class="btn btn-outline-primary btn-sm" target="_blank"><?php sn_e("View"); ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php
    }
