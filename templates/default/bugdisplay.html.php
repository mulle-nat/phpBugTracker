<script type="text/javascript">
    <!--
    warningString = '<?php echo translate("You must login to modify this bug"); ?>';
    warnedAlready = false;

    versions = new Array();
    closedversions = new Array();
    components = new Array();
    versions['All'] = new Array(new Array('','All'));
    closedversions['All'] = new Array(new Array('','All'));
    components['All'] = new Array(new Array('','All'));
<?php build_project_js(true); ?>

    function updateMenus(f) {
        sel = f.project_id[f.project_id.selectedIndex].text;
        f.version_id.length = versions[sel].length;
        for (var x = 0; x < versions[sel].length; x++) {
            f.version_id.options[x].value = versions[sel][x][0];
            f.version_id.options[x].text = versions[sel][x][1];
            f.version_id.selectedIndex = 0;
        }

        f.to_be_closed_in_version_id.length = closedversions[sel].length;
        for (var x = 0; x < closedversions[sel].length; x++) {
            f.to_be_closed_in_version_id.options[x].value = closedversions[sel][x][0];
            f.to_be_closed_in_version_id.options[x].text = closedversions[sel][x][1];
            f.to_be_closed_in_version_id.selectedIndex = 0;
        }

        f.closed_in_version_id.length = closedversions[sel].length;
        for (var x = 0; x < closedversions[sel].length; x++) {
            f.closed_in_version_id.options[x].value = closedversions[sel][x][0];
            f.closed_in_version_id.options[x].text = closedversions[sel][x][1];
            f.closed_in_version_id.selectedIndex = 0;
        }

        f.component_id.length = components[sel].length;
        for (var x = 0; x < components[sel].length; x++) {
            f.component_id.options[x].value = components[sel][x][0];
            f.component_id.options[x].text = components[sel][x][1];
            f.component_id.selectedIndex = 0;
        }
    }

    function popupAtt(id) {
        window.open('attachment.php?use_js=1&bugid='+id, 'ewin', 'dependent=yes,width=350,height=200,scrollbars=1');
        return false;
    }
    //-->
</script>
<?php
$is_user = (isset($_SESSION['uid']) && !empty($_SESSION['uid']));
$is_admin = ($is_user && isset($perm) && $perm->have_perm_proj($project_id));
$may_edit = (isset($perm) && $perm->have_perm('EditBug', $project_id));
$may_manage = ($may_edit && $perm->have_perm('ManageBug', $project_id));
$may_change_project = ($may_edit && $perm->have_perm('EditProject', $project_id));
$may_change_component = ($may_edit && $perm->have_perm('EditComponent', $project_id));
$may_change_assignment = ($may_edit && $perm->have_perm('EditAssignment', $project_id));
$may_change_status = ($may_edit && $perm->have_perm('EditStatus', $project_id));
$may_close = ($may_edit && $perm->have_perm('CloseBug', $project_id));
$may_change_resolution = ($may_edit && $perm->have_perm('EditResolution', $project_id));
$may_change_priority = ($may_edit && $perm->have_perm('EditPriority', $project_id));
$may_change_severity = ($may_edit && $perm->have_perm('EditSeverity', $project_id));
$may_add_comment = (isset($perm) && $perm->have_perm('CommentBug', $project_id));
?>
<form action="bug.php" method="post">
    <input type="hidden" name="op" value="update">
    <input type="hidden" name="bugid" value="<?php echo $bug_id ?>">
    <input type="hidden" name="projectid" value="<?php echo $project_id ?>">
    <input type="hidden" name="last_modified_date" value="<?php echo $last_modified_date ?>">
    <input type="hidden" name="pos" value="<?php echo isset($_REQUEST['pos']) ? $_REQUEST['pos'] : ''; ?>">
    <table border="0" width="100%">
        <tr>
            <td>Bug <b>#<?php echo $bug_id ?></b> - <?php echo htmlspecialchars($title) ?></td>
            <td align="right">
                <b><a href="query.php"><?php echo translate("Return to bug list"); ?></a></b>
                <?php if (!empty($prevbug)) { ?>
                    <b>&nbsp;|&nbsp;&nbsp;<a href="bug.php?op=show&bugid=<?php echo $prevbug ?>&pos=<?php echo $prevpos ?>"><?php echo translate("Previous bug"); ?></a></b>
                <?php } else { ?>
                    <b>&nbsp;|&nbsp;&nbsp;<strike><?php echo translate("Previous bug"); ?></strike></b>
                <?php } ?>
                <?php if (!empty($nextbug)) { ?>
                    <input type="hidden" name="nextbug" value="<?php echo $nextbug ?>">
                    <input type="hidden" name="nextpos" value="<?php echo $nextpos ?>">
                    <b>&nbsp;|&nbsp;&nbsp;<a href="bug.php?op=show&bugid=<?php echo $nextbug ?>&pos=<?php echo $nextpos ?>"><?php echo translate("Next bug"); ?></a></b>
                <?php } else { ?>
                    <b>&nbsp;|&nbsp;&nbsp;<strike><?php echo translate("Next bug"); ?></strike></b>
                <?php } ?>
            </td>
        </tr>
        <?php if (!empty($error['status']))
            echo "<tr><td class=\"error\">{$error['status']}</td></tr>"; ?>
    </table>
    <?php if (!empty($error['add_dep']))
        echo "<div class=\"error\">{$error['add_dep']}</div>"; ?>
    <?php if (!empty($error['vote']))
        echo "<div class=\"error\">{$error['vote']}</div>"; ?>

    <!-- comments (the meat of the bug report) -->
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tr><td><!-- <?php echo translate("Comments"); ?>: --></td></tr>
        <tr class="alt">
            <td><?php echo translate("Posted by"); ?>: <?php echo maskemail($reporter); ?> 
                <?php echo translate("Date"); ?>: <?php echo date(TIME_FORMAT . ' ' . DATE_FORMAT, $created_date); ?></td>
        </tr><tr>
            <td><?php echo format_comments($description); ?> <br><br></td>
        </tr>
        <?php for ($i = 0, $ccount = count($comments); $i < $ccount; $i++) { ?>
            <tr class="alt">
                <td><?php echo translate("Posted by"); ?>: <?php echo maskemail($comments[$i]['login']); ?> 
                    <?php echo translate("Date"); ?>: <?php echo date(TIME_FORMAT . ' ' . DATE_FORMAT, $comments[$i]['created_date']); ?></td>
            </tr><tr>
                <td><?php echo format_comments($comments[$i]['comment_text']); ?> <br><br></td>
            </tr>
        <?php } ?>
    </table>

    <!-- metadata -->
    <hr/>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
            <td><?php echo translate("Reporter"); ?>:</td>
            <?php if ($is_admin) { ?>
                <td><select name="created_by"><?php build_select('reporter', $created_by) ?></select></td>
            <?php } else { ?>
                <td><b><?php echo maskemail($reporter); ?></b></td>
            <?php } ?>
            <td><?php echo translate("Created"); ?>:</td>
            <td><b><?php echo date(DATE_FORMAT, $created_date) ?></b></td>
        </tr><tr>
            <td><?php echo translate("Project"); ?>:</td>
            <?php if ($may_change_project) { ?>
                <td><select name="project_id" onChange="updateMenus(this.form)"><?php build_select('project', $project_id) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('project', $project_id); ?></td>
            <?php } ?>
            <td><?php echo translate("Priority"); ?>:</td>
            <?php if ($may_change_priority or $may_manage) { ?>
                <td><select name="priority"><?php build_select('priority', $priority) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('priority', $priority); ?></td>
            <?php } ?>
        </tr><tr>
            <td><?php echo translate("Component"); ?>:</td>
            <?php if ($may_change_component) { ?>
                <td><select name="component_id"><?php build_select('component', $component_id, $project_id) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('component', $component_id); ?></td>
            <?php } ?>
            <td><?php echo translate("Severity"); ?>:</td>
            <?php if ($may_change_severity or $may_manage) { ?>
                <td><select name="severity_id"><?php build_select('severity', $severity_id) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('severity', $severity_id); ?></td>
            <?php } ?>
        </tr><tr>
            <td><?php echo translate("Version"); ?>:</td>
            <?php if ($may_edit) { ?>
                <td><select name="version_id"><?php build_select('version', $version_id, $project_id) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('version', $version_id); ?></td>
            <?php } ?>
            <td><?php echo translate("Operating System"); ?>:</td>
            <?php if ($may_edit) { ?>
                <td><select name="os_id"><?php build_select('os', $os_id) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('os', $os_id); ?></td>
            <?php } ?>
        </tr><tr>
            <td><?php echo translate("To be closed in version"); ?></td>
            <?php if ($may_close or $may_manage) { ?>
                <td><select name="to_be_closed_in_version_id">
                        <option value="0"><?php echo translate("Choose one"); ?></option>
                        <?php build_select('version', $to_be_closed_in_version_id, $project_id) ?>
                    </select></td>
            <?php } else { ?>
                <td><?php echo lookup('version', $to_be_closed_in_version_id, $project_id); ?></td>
            <?php } ?>
            <td><?php echo translate("Database"); ?>:</td>
            <?php if ($may_edit) { ?>
                <td><select name="database_id"><?php build_select('database', $database_id) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('database', $database_id); ?></td>
            <?php } ?>
        </tr><tr>
            <td><?php echo translate("Closed in version"); ?></td>
            <?php if ($may_close or $may_manage) { ?>
                <td><select name="closed_in_version_id">
                        <option value="0" selected><?php echo translate("Choose one"); ?></option>
                        <?php build_select('version', $closed_in_version_id, $project_id); ?>
                    </select></td>
            <?php } else { ?>
                <td><?php echo lookup('version', $closed_in_version_id, $project_id); ?></td>
            <?php } ?>
            <td><?php echo translate("Site"); ?></td>
            <?php if ($may_edit) { ?>
                <td><select name="site_id"><?php build_select('site', $site_id); ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('site', $site_id); ?></td>
            <?php } ?>
        </tr><tr>
            <td><?php echo translate("Summary"); ?>:</td>
            <?php if ($may_edit) { ?>
                <td><input type="text" size="40" maxlength="100" name="title" value="<?php echo htmlspecialchars($title) ?>"></td>
            <?php } else { ?>
                <td><?php echo htmlspecialchars($title); ?></td>
            <?php } ?>
            <td><?php echo translate("Status"); ?>:</td>
            <?php if ($may_change_status or $may_manage) { ?>
                <td><select name="status_id"><?php build_select('status', $status_id, $project_id, true); ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('status', $status_id); ?></td>
            <?php } ?>
        </tr><tr>
            <td><?php
            if ($url)
                echo "<a href=\"$url\" rel=\"nofollow\">" . translate("URL") . "</a>"; else
                echo translate("URL");
            ?>:</td>
            <?php if ($may_edit) { ?>
                <td><input type="text" size="35" maxlength="255" name="url" value="<?php echo $url ?>"></td>
            <?php } else { ?>
                <td><?php echo $url; ?></td>
            <?php } ?>
            <td><?php echo translate("Resolution"); ?>:</td>
            <?php if ($may_close or $may_change_resolution or $may_manage) { ?>
                <td><select name="resolution_id"><option value="0"><?php echo translate("None"); ?></option><?php build_select('resolution', $resolution_id) ?></select></td>
            <?php } else { ?>
                <td><?php echo lookup('resolution', $resolution_id); ?></td>
            <?php } ?>
        </tr><tr>
            <td><?php echo translate("Assigned to"); ?>:</td>
            <?php if ($may_change_assignment or $may_manage) { ?>
                <td><select name="assigned_to"><option value="0"><?php echo translate("None"); ?></option><?php build_select('owner', $assigned_to) ?></select></td>
            <?php } else { ?>
                <td>
                    <?php echo $assigned_to ? lookup('assigned_to', $assigned_to) : ""; ?>
                    <input type="hidden" name="assigned_to" value="<?php echo $assigned_to; ?>">
                </td>
            <?php } ?>
            <?php if ($may_change_assignment or $may_manage or $may_edit) { ?>
                <td><?php echo translate("Add CC"); ?>:</td>
                <td><select name="add_cc"><option value="0" selected>Choose one</option>
                        <?php build_select('reporter', 'none') ?></select></td> 
                <!-- <td><input type="text" name="add_cc"></td> -->
            <?php } ?>
        </tr><tr>
            <td colspan="2" valign="top">
                <br>
                <?php if (!empty($error['add_dep']))
                    echo "<div class=\"error\">{$error['add_dep']}</div>"; ?>
                <?php if (!empty($bug_dependencies)) { ?>
                    <?php echo translate("Depends on bugs"); ?>: 
                    <?php
                    for ($i = 0, $count = count($bug_dependencies); $i < $count; $i++) {
                        printf('<a href="bug.php?op=show&bugid=%d" class="%s">#%d</a>%s', $bug_dependencies[$i]['bug_id'], ($bug_dependencies[$i]['bug_open'] ? 'open_bug_number' : 'closed_bug_number'), $bug_dependencies[$i]['bug_id'], ($i < $count - 1 ? ', ' : ''));
                    }
                    ?>
                    <br>
                <?php } ?>
                <?php if (!empty($bug_blocks)) { ?>
                    <?php echo translate("Blocks bugs"); ?>:  
                    <?php
                    for ($i = 0, $count = count($bug_blocks); $i < $count; $i++) {
                        printf('<a href="bug.php?op=show&bugid=%d" class="%s">#%d</a>%s', $bug_blocks[$i]['bug_id'], ($bug_blocks[$i]['bug_open'] ? 'open_bug_number' : 'closed_bug_number'), $bug_blocks[$i]['bug_id'], ($i < $count - 1 ? ', ' : ''));
                    }
                    ?>
                    <br>
                <?php } ?>
                <?php if (!empty($bug_duplicates)) { ?>
                    <?php echo translate("Duplicates"); ?>: 
                    <?php
                    for ($i = 0, $count = count($bug_duplicates); $i < $count; $i++) {
                        if (isset($bug_duplicates[$i])) {
                            printf('<a href="bug.php?op=show&bugid=%d" class="%s">#%d</a>%s', $bug_duplicates[$i]['bug_id'], ($bug_duplicates[$i]['bug_open'] ? 'open_bug_number' : 'closed_bug_number'), $bug_duplicates[$i]['bug_id'], ($i < $count - 1 ? ', ' : ''));
                        }
                    }
                    ?>
                    <br>
                <?php } ?>
                <?php if ($may_manage or $is_owner or $is_assignee) { ?>
                    <br>
                    <?php echo translate("Add dependency"); ?>: <input type="text" name="add_dependency" size="5">&nbsp;
                    <?php echo translate("Remove dependency"); ?>: <input type="text" name="del_dependency" size="5"><br>
                    <?php echo translate("Add duplicate"); ?>: <input type="text" name="add_duplicate" size="5">&nbsp;
                    <?php echo translate("Remove duplicate"); ?>: <input type="text" name="del_duplicate" size="5"><br><br>
                <?php } else { ?>
                    <input type="hidden" name="add_dependency" value="">
                    <input type="hidden" name="del_dependency" value="">
                <?php } ?>
            </td>
            <td colspan="2" valign="top">
                <?php if ($may_change_assignment or $may_manage or $may_edit) { ?>
                    <?php echo translate("Remove selected CCs"); ?>:<br>
                    <select name="remove_cc[]" size="5" style="width: 15em" multiple><?php build_select('bug_cc', $bug_id, $project_id) ?></select>
                <?php } ?>
            </td>
        </tr>
    </table>
    <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <?php if ($may_add_comment) { ?>
            <tr class="noprint">
                <td valign="top"><br><?php echo translate("Additional comments"); ?>:<br>
                    <textarea name="comments" rows="6" cols="79"><?php echo isset($_POST['comments']) ? $_POST['comments'] : ''; ?></textarea>
                    <br><br>
                    <div align="right">
                        <?php if ($may_change_assignment or $may_manage or $may_edit or
                                $may_add_comment or $may_close or $may_change_resolution) { ?>
                            <?php echo translate("Supress notification email"); ?> <input type="checkbox" name="suppress_email" value="1">
                            <?php if (defined('DIGICRAFT_TRACKER')) { // AVK fix start  ?>
                                <input type="submit" value="Submit & return">
                            <?php } else {  // AVK fix end   ?>
                                <?php if (empty($nextbug)) { ?>
                                    <input type="submit" value="Submit">
                                <?php } else { ?>
                                    <input type="submit" value="Submit & View Next">
                                <?php } ?>
                            <?php } // AVK fix (one line) ?>
                        <?php } else
                            echo translate("You must login to modify this bug"); ?>
                    </div></td>
            </tr>
        <?php } ?>
        <tr>
            <td><table border="0" cellpadding="0" width="100%">
                    <tr>
                        <td colspan="2"><?php echo translate("Attachments"); ?>:</td>
                        <td colspan="3" align="right">
                            <?php if ($may_edit or $may_add_comment) { ?>
                                <a href="attachment.php?bugid=<?php echo $bug_id; ?>" onClick="return popupAtt(<?php echo $bug_id; ?>)"><?php echo translate("Create new attachment"); ?></a>
                            <?php } ?>
                        </td>
                    </tr><tr>
                        <td colspan="5" height="2" bgcolor="#ffffff"><img src="images/spacer.gif" alt="" height="2" width="2" /></td>
                    </tr><tr>
                    <td bgcolor="#cccccc" align="center"><b><?php echo translate("Name"); ?></b></td>
                    <td width="60" bgcolor="#cccccc" align="center"><b><?php echo translate("Size"); ?></b></td>
                    <td width="150" bgcolor="#cccccc" align="center"><b><?php echo translate("Type"); ?></b></td>
                    <td width="80" bgcolor="#cccccc" align="center"><b><?php echo translate("Created"); ?></b></td>
                    <td width="80" bgcolor="#cccccc" align="center">&nbsp;</td>
                </tr><tr>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                </tr>
                <?php if ($attcount = count($attachments)) { ?>
                    <?php for ($i = 0; $i < $attcount; $i++) { ?>
                <tr title="<?php echo htmlspecialchars($attachments[$i]['description']); ?>"<?php if ($i % 2 != 0)
                    echo ' class="alt" bgcolor="#dddddd"' ?>>
                            <td><?php echo htmlspecialchars($attachments[$i]['file_name']); ?></td>
                            <td align="right">
                                <?php
                                echo $attachments[$i]['file_size'] > 1024 ? number_format((round($attachments[$i]['file_size']) / 1024 * 100) / 100) . 'k' : number_format($attachments[$i]['file_size']) . 'b';
                                ?>
                            </td>
                            <td align="center"><?php echo $attachments[$i]['mime_type']; ?></td>
                            <td align="center"><?php echo date(DATE_FORMAT, $attachments[$i]['created_date']); ?></td>
                            <td align="center"><a href='attachment.php?attachid=<?php echo $attachments[$i]['attachment_id']; ?>'>View</a>
                                <?php if ($is_admin or $may_manage) { ?>
                                    | <a href='attachment.php?del=<?php echo $attachments[$i]['attachment_id']; ?>' onClick="return confirm('<?php echo translate("Are you sure you want to delete this attachment?"); ?>');"><?php echo translate("Delete"); ?></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" align="center"><?php echo translate("No attachments found for this bug"); ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                    <td bgcolor="#000000" height="1"><img src="images/spacer.gif" alt="" height="1" width="1" /></td>
                </tr><tr>
                    <td colspan="5" height="2" bgcolor="#ffffff"><img src="images/spacer.gif" alt="" height="2" width="2" /></td>
                </tr>
        </table></td>
</tr>
</table>
</form>
<div align="center" class="bugdisplaylinks">
    <?php if (isset($_SESSION['uid']) && !empty($_SESSION['uid'])) { ?>
        <?php if (!$already_bookmarked) { ?>
            <b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?op=addbookmark&amp;bugid=<?php echo $bug_id . $posinfo; ?>"><?php echo translate("Bookmark this bug"); ?></a></b> |
        <?php } else { ?>
            <b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?op=delbookmark&amp;bugid=<?php echo $bug_id . $posinfo; ?>"><?php echo translate("Remove bookmark for this bug"); ?></a></b> |
        <?php } ?>
        <?php if (!empty($error['vote']))
            echo "<div class=\"error\">{$error['vote']}</div>" ?>
        <b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?op=vote&amp;bugid=<?php echo $bug_id . $posinfo; ?>" onClick="if (<?php echo $already_voted; ?>) { alert ('<?php echo translate("You have already voted for this bug"); ?>'); return false; }"><?php echo translate("Vote for this bug"); ?></a></b> |
    <?php } ?>
    <b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?op=viewvotes&amp;bugid=<?php echo $bug_id . $posinfo; ?>"><?php echo translate("View votes"); ?> (<?php echo $num_votes; ?>)</a></b>
    | <b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?op=history&amp;bugid=<?php echo $bug_id . $posinfo; ?>"><?php echo translate("View bug history"); ?></a></b>
    | <b><a href="<?php echo $_SERVER['PHP_SELF']; ?>?op=print&amp;bugid=<?php echo $bug_id . $posinfo; ?>"><?php echo translate("Printable View"); ?></a></b>
    <?php if ($is_admin or $may_manage) { ?>
        | <b><a href="editComment.php?bugid=<?php echo $bug_id ?>"><?php echo translate("Edit Comment") ?></a></b>
    <?php } ?>
    <?php if ($is_admin) { ?>
        <!--
        | <b><a href="bug.php?op=del&bugid=<?php echo $bug_id; ?>"><?php echo translate("Delete bug"); ?></a></b>
        -->
    <?php } ?>
</div>
