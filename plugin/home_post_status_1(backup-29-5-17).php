<script>
    $(document).ready(function() {
        $.post('lib/imageload.php', {'st': 3, 'usrid':<?php echo $input_by; ?>}, function(defim) {
            var ndcl = jQuery.parseJSON(defim);
            var thumbdef = ndcl.thumb;
            $('img[name=comment_profile_photo]').attr('src', './profile/' + thumbdef),
        });
    });
</script>


<?php
include('class/extraClass.php');
$extra = new SiteExtra();
//if ($obj->filename() == "profiles.php" || $new_user_id != $input_by) {
if (in_array($obj->filename(), array("profiles.php", "profile.php"))) {
    $sqlquery = $obj->FlyQuery("SELECT
                b.id,
                b.group_id,
                b.user_id,
                b.from_user_id,
                b.photo_id,
                count(dc.id) AS `comment`,
                count(dl.id) AS `likes`,
                b.share_id,
                 CASE b.share_id WHEN 0 THEN
                 (SELECT count(a.id) FROM dostums_post as a WHERE a.share_id=b.id)
                 ELSE
                 (SELECT count(a.id) FROM dostums_post as a WHERE a.share_id=b.share_id)
                 END AS share_count,
                b.post,
                b.post_time,
                b.post_status,
                b.status
                FROM dostums_post AS b
                LEFT JOIN dostums_comment as dc on dc.post_id=b.id
                LEFT JOIN dostums_likes as dl ON dl.post_id=b.id
                WHERE b.status <> 0 AND (b.user_id=" . $new_user_id . " OR
                       b.from_user_id=" . $new_user_id . " OR
                       b.to_user_id=" . $new_user_id . ") AND b.group_id='0' AND b.page_id='0'

                GROUP BY b.id
                ORDER BY b.id DESC LIMIT 8");
} else {
    $sqlquery = $obj->FlyQuery("SELECT
                      b.id,
                      b.group_id,
                      b.user_id,
                      b.from_user_id,
                      b.photo_id,
                      count(dc.id) AS `comment`,
                      count(dl.id) AS `likes`,
                      b.share_id,
                      b.post,
                      b.post_time,
                      b.post_status,
                      b.status,

                      IF(b.id = dt.post_id,dt.to_uid,NULL) AS tagID,
                      IF(b.id = dt.post_id,dt.status,0) AS tagStatus,
                      dt.to_uid AS tagedID,

                      CASE b.share_id WHEN 0 THEN
                      (SELECT count(a.id) FROM dostums_post as a WHERE a.share_id=b.id)
                      ELSE
                      (SELECT count(a.id) FROM dostums_post as a WHERE a.share_id=b.share_id)
                      END AS share_count

                      FROM dostums_post AS b
                      LEFT JOIN dostums_comment as dc on dc.post_id=b.id
                      LEFT JOIN dostums_likes as dl ON dl.post_id=b.id

                      LEFT JOIN dostums_tags AS dt ON dt.to_uid = '" . $new_user_id . "'

                      WHERE
                      b.status <> 0
                      AND b.user_id IN (SELECT dostums_friend.uid FROM dostums_friend WHERE
                      dostums_friend.uid = '" . $new_user_id . "' OR
                      dostums_friend.to_uid='" . $new_user_id . "') OR b.user_id='" . $new_user_id . "'

                      GROUP BY b.id
                      ORDER BY b.id DESC limit 8
                              ");
}

$postbreak = 1;

if (!empty($sqlquery)) {

    foreach ($sqlquery as $post):


        $post_id = $post->id;
        $post_status = $post->post_status;
        $tag_id = $post->tagID;
        $tag_status = $post->tagStatus;

        $new_post_head_id = $post->id . "statushead" . time();

        @$chkpermission = $obj->SelectAllByID_multiple("dostums_post_permission_record",
                                                 array("user_id" => $post->user_id, "post_id" => $post->id));

        if ((@$chkpermission[0]->permission_id == 3 && @$post->user_id == $input_by) || !@$chkpermission) {

            ?>
            <div class="panel panel-default  panel-customs-post">
                <div class="dropdown">
                    <span class="dropdown-toggle" type="button" data-toggle="dropdown">
                        <span class=" glyphicon glyphicon-chevron-down "></span>
                    </span>
                    <ul class="dropdown-menu" role="menu">
                        <li role="presentation">
                          <!-- <a role="menuitem" class="dostums-post-delete" id="<?php echo $post->id; ?>" tabindex="-1" href="#"><i class="fa fa-trash"></i> Delete Post</a> -->
                          <a role="menuitem" class="dostums-post-hide" id="<?php echo $post->id; ?>" tabindex="-1" href="#">
                            <i class="fa fa-eye-slash"></i> Hide from timeline</a>
                        </li>
                    </ul>
                </div>

                <div class="panel-heading" id="<?php echo $new_post_head_id; ?>">
                    <script type="text/javascript">
                        $(document).ready(function() {
                            load_post = {'st': 1, 'post_id':<?php echo $post->id; ?>};
                            $.post('lib/imageload.php', load_post, function(datapost) {
                                if (datapost != 0)
                                {
                                    var datacl = jQuery.parseJSON(datapost);
                                    var user_id = datacl.user_id;
                                    var name = datacl.name;
                                    var thumb = datacl.thumb;
                                    var thumbbig = datacl.thumbbig;
                                    var to_user_id = datacl.to_user_id;

                                    if (to_user_id == 0)
                                    {
                                        var sharedhtmlname = "Shared publicly";
                                        var sharedhtml = "Posted";
                                    }
                                    else
                                    {
                                        var to_name = datacl.to_name;
                                        var thumb2 = datacl.thumb2;
                                        var thumbbig2 = datacl.thumbbig2;
                                        var sharedhtmlname = "Shared on <a href='profile.php?user_id=" + to_user_id + "' style='color:#000;'>" + to_name + "</a> Timeline";
                                        var sharedhtml = "Posted";
                                    }
                                  <?php
                                    if ($post_status == 1) {
                                  ?>
                                        var datahtml = "<img class='img-circle pull-left' src='./profile/" + thumb + "' alt='" + thumb + "'><h3><a href='profile.php?user_id=" + user_id + "'>" + name + "</a> Shared a post</h3><h5><span>" + sharedhtml + "</span> - <span><?php echo $extra->duration($post->post_time, date('Y-m-d H:i:s')); ?></span></h5>";
                                  <?php
                                    } else {
                                  ?>
                                        var datahtml = "<img class='img-circle pull-left' src='./profile/" + thumb + "' alt='" + thumb + "'><h3><a href='profile.php?user_id=" + user_id + "'>" + name + "</a> Posted Something</h3><h5><span>" + sharedhtml + "</span> - <span><?php echo $extra->duration($post->post_time, date('Y-m-d H:i:s')); ?></span></h5>";
                                  <?php
                                    }
                                  ?>
                                        $('#<?php echo $new_post_head_id; ?>').html(datahtml);
                                    }
                                    else
                                    {
                                        window.location.refresh();
                                    }
                            });

                        });
                    </script>
                </div>

                <?php include('status/post.php'); ?>

                <div class="panel-bottom">
                    <div class="panel-footer has-share-panel">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php include('status/postactionbar.php'); ?>
                            </div>
                        </div>
                    </div>
                    <?php include('status/comment_list.php'); ?>
                    <?php include('status/comment.php'); ?>
                </div>
            </div>

            <?php

               $postbreak++;

               }

               elseif ((@$chkpermission[0]->permission_id == 2 || @$chkpermission[0]->permission_id == 1) || !@$chkpermission) {
               if($tag_id == $new_user_id && $tag_status == '2'  ){
                 echo '1';
               ?>

            <!-- <div class="panel panel-default  panel-customs-post" style="display:none;"> -->
            <div class="panel panel-default  panel-customs-post">
             <?php
           } else if( $tag_status == '0' &&  $tag_id == null){
             echo '2';
             ?>
            <div class="panel panel-default  panel-customs-post">
             <?php
             }
             ?>
                    <div class="dropdown">
                        <span class="dropdown-toggle" type="button" data-toggle="dropdown">
                            <span class=" glyphicon glyphicon-chevron-down "></span>
                        </span>
                        <ul class="dropdown-menu" role="menu">
                        <?php
                        if ($post->user_id == $input_by) {
                        ?>
                            <li role="presentation"><a role="menuitem"  class="dostums-post-view"  id="<?php echo $post->id; ?>" tabindex="-1" href="home_view.php?view=<?php echo $post->id; ?>"><i class="fa fa-list"></i> View Post </a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" class="dostums-post-edit" id="<?php echo $post->id; ?>" href="home_edit.php?edit=<?php echo $post->id; ?>"><i class="fa fa-edit"></i> Edit Post </a></li>
                            <li role="presentation"><a role="menuitem" class="dostums-post-delete" id="<?php echo $post->id; ?>" tabindex="-1" href="javascript:void(0);"><i class="fa fa-trash"></i> Remove Post </a></li>

                        <?php
                        } else if($post->post_status == 5){
                        ?>
                            <li role="presentation"><a onclick="javascript:removetag(<?php echo $input_by;?>,<?php echo $post_id?>);" role="menuitem" class="" id="<?php echo $post->id; ?>" tabindex="-1" href="javascript:void(0);"><i class="fa fa-trash"></i> Remove Tag </a></li>
                        <?php
                      } else {  //$input_by  $post_id
                        ?>
                            <li role="presentation"><a role="menuitem" class="dostums-post-delete" id="<?php echo $post->id; ?>" tabindex="-1" href="javascript:void(0);"><i class="fa fa-eye-slash"></i> Hide post </a></li>
                        <?php
                        }
                        ?>
                        </ul>
                    </div>

                <div class="panel-heading" id="<?php echo $new_post_head_id; ?>">
                    <script type="text/javascript">
                        $(document).ready(function() {
                            load_post = {'st': 1, 'post_id':<?php echo $post->id; ?>};
                            $.post('lib/imageload.php', load_post, function(datapost) {
                                if (datapost != 0)
                                {
                                    var datacl = jQuery.parseJSON(datapost);
                                    var user_id = datacl.user_id;
                                    var name = datacl.name;
                                    var thumb = datacl.thumb;
                                    var thumbbig = datacl.thumbbig;
                                    var to_user_id = datacl.to_user_id;
                                    var tagname = datacl.tagName;
                                    // alert($post_status);
                                    if (to_user_id == 0)
                                    {
                                        var sharedhtmlname = "Shared publicly";
                                        var sharedhtml = "Posted";
                                    }
                                    else
                                    {
                                        var to_name = datacl.to_name;
                                        var thumb2 = datacl.thumb2;
                                        var thumbbig2 = datacl.thumbbig2;
                                        var sharedhtmlname = "Shared on <a href='profile.php?user_id=" + to_user_id + "' style='color:#000;'>" + to_name + "</a> Timeline";
                                        var sharedhtml = "Posted";
                                    }

                                    <?php
                                      if ($post_status == 1) {
                                    ?>
                                        var datahtml = "<img class='img-circle pull-left' src='./profile/" + thumb + "' alt='" + thumb + "'><h3><a href='profile.php?user_id=" + user_id + "'>" + name + "</a> Shared a post</h3><h5><span>" + sharedhtml + "</span> - <span><?php echo $extra->duration($post->post_time, date('Y-m-d H:i:s')); ?></span></h5>";
                                    <?php
                                      } else if($post_status == 5){
                                    ?>
                                        var datahtml = "<img class='img-circle pull-left' src='./profile/" + thumb + "' alt='" + thumb + "'><h3><a href='profile.php?user_id=" + user_id + "'>" + name + "</a> with "+tagname+"</h3><h5><span>" + sharedhtml + "</span> - <span><?php echo $extra->duration($post->post_time, date('Y-m-d H:i:s')); ?></span></h5>";
                                    <?php
                                      } else {
                                    ?>
                                        var datahtml = "<img class='img-circle pull-left' src='./profile/" + thumb + "' alt='" + thumb + "'><h3><a href='profile.php?user_id=" + user_id + "'>" + name + "</a> Posted Something</h3><h5><span>" + sharedhtml + "</span> - <span><?php echo $extra->duration($post->post_time, date('Y-m-d H:i:s')); ?></span></h5>";
                                    <?php
                                      }
                                    ?>
                                      $('#<?php echo $new_post_head_id; ?>').html(datahtml);
                                      }
                                      else
                                      {
                                          window.location.refresh();
                                      }
                            });

                        });
                    </script>
                </div>

                <?php include('status/post.php'); ?>

                <div class="panel-bottom">
                    <div class="panel-footer has-share-panel">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php include('status/postactionbar.php'); ?>
                            </div>
                        </div>
                    </div>
                    <?php include('status/comment_list.php'); ?>
                    <?php include('status/comment.php'); ?>
                </div>
            </div>


            <?php
            $postbreak++;
            }


    endforeach;
}
?>
