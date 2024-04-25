<?php $aboutactivetab = 'webtab-52'; ?>

<div id="tabs">
    <ul class="nav nav-tabs" >
        <li class="">
            <a class="pagerlink" data-toggle="tab" href="#"></a>
        </li>
        <li class="<?php if ('webtab-52' === $aboutactivetab) { ?> active <?php }  ?>">
            <a class="pagerlink" data-toggle="tab" href="#webtab-52">Web Page</a>
        </li>
        <li class="<?php if ('mobiletab-1' === $aboutactivetab) { ?> active <?php }  ?>">
            <a class="pagerlink" data-toggle="tab" href="#mobiletab-1">App Page</a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="webtab-52" class="tab-pane <?php if ('webtab-52' === $aboutactivetab) { ?>active<?php } ?>">
            <?php if ('Edit' === $action) {
                $id = $iPageId = '52';
                $sql = 'SELECT * FROM '.$tbl_name." WHERE iPageId = '".$id."'";
                $db_data = $obj->MySQLSelect($sql);

                $vLabel = $id;
                if (count($db_data) > 0) {
                    for ($i = 0; $i < count($db_master); ++$i) {
                        foreach ($db_data as $key => $value) {
                            $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
                            ${$vPageTitle} = $value[$vPageTitle];
                            $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
                            ${$tPageDesc} = $value[$tPageDesc];
                            if ('Yes' === $cubexthemeon && 52 === $iPageId) {
                                $pageSubtitle = $value['pageSubtitle'];
                                $pageSubtitleArr = json_decode($pageSubtitle, true);
                            }
                            $vPageName = $value['vPageName'];
                            $vTitle = $value['vTitle'];
                            $tMetaKeyword = $value['tMetaKeyword'];
                            $tMetaDescription = $value['tMetaDescription'];
                            $vImage = $value['vImage'];
                            $vImage1 = $value['vImage1'];
                            $vImage2 = $value['vImage2'];
                            $iOrderBy = $value['iOrderBy']; // added by SP for pages orderby,active/inactive functionality
                        }
                    }
                }
            } ?>
            <form method="post" action="" name="_page_form" id="_page_form"  enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                <input type="hidden" name="backlink" id="backlink" value="page.php"/>
                <div class="row">
                    <div class="col-md-12">
                        <label>Page/Section</label>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input type="text" class="form-control" name="vPageName"  id="vPageName" value="<?php echo htmlspecialchars($vPageName); ?>" placeholder="Page Name">
                    </div>
                </div>
                <?php $style_v = '';
if (in_array($iPageId, ['29', '30', '53'], true)) {
    $style_v = "style = 'display:none;'";
}
?>

                <?php

$pagedescarrDefault = json_decode($db_data[0]['tPageDesc_'.$default_lang], true);
$FirstdescvalDefault = $pagedescarrDefault['FirstDesc'];
$SecdescvalDefault = $pagedescarrDefault['SecDesc'];
$ThirddescvalDefault = $pagedescarrDefault['ThirdDesc'];

if (count($db_master) > 1) { ?>
                    <div class="row" <?php echo $style_v; ?>>
                        <div class="col-md-12">
                            <label>Page Title <span class="red"> *</span></label>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" class="form-control" name="vPageTitle_Default"  id="vPageTitle_Default" value="<?php echo htmlspecialchars($db_data[0]['vPageTitle_'.$default_lang]); ?>"  readonly="readonly" <?php if ('' === $id) { ?> onclick="editAboutUsWeb('Add')" <?php } ?> data-originalvalue="<?php echo htmlspecialchars($db_data[0]['vPageTitle_'.$default_lang]); ?>">
                        </div>
                        <?php if ('' !== $id) { ?>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editAboutUsWeb('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                <label>Page Sub Description <span class="red"> *</span></label>
                            <?php } else { ?>
                                <label>Page Description <span class="red"> *</span></label>
                            <?php } ?>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <textarea class="form-control ckeditor" rows="10" id="vPageSubTitle_Default" readonly="readonly"><?php echo $pageSubtitleArr['pageSubtitle_'.$default_lang]; ?></textarea>
                        </div>
                        <?php if ('' !== $id) { ?>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'SubDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                            </div>
                        <?php } ?>
                    </div>


                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                        <div class="row" <?php echo $style_v; ?>>
                            <div class="col-md-12">
                                <label> Page First Description </label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <textarea class="form-control ckeditor" rows="10" id="tPageDesc_Default" readonly="readonly"> <?php echo $FirstdescvalDefault; ?></textarea>
                            </div>
                            <?php if ('' !== $id) { ?>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'FirstDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="row" <?php echo $style_v; ?>>
                            <div class="col-md-12">
                                <label> Page Second Description </label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <textarea class="form-control ckeditor" rows="10" id="tPageSecDesc_Default" readonly="readonly"> <?php echo $SecdescvalDefault; ?></textarea>
                            </div>
                            <?php if ('' !== $id) { ?>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'SecondDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="row" <?php echo $style_v; ?>>
                            <div class="col-md-12">
                                <label> Page Third Description </label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <textarea class="form-control ckeditor" rows="10" id="tPageThirdDesc_Default" readonly="readonly"> <?php echo $ThirddescvalDefault; ?></textarea>
                            </div>
                            <?php if ('' !== $id) { ?>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'ThirdDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div  class="modal fade" id="aboutUsWeb_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-lg" >
                            <div class="modal-content nimot-class">
                                <div class="modal-header">
                                    <h4>
                                        <span id="modal_action"></span> About Us - Page Title
                                        <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageTitle_')">x</button>
                                    </h4>
                                </div>

                                <div class="modal-body">
                                    <?php

                    for ($i = 0; $i < $count_all; ++$i) {
                        $vCode = $db_master[$i]['vCode'];
                        $vLTitle = $db_master[$i]['vTitle'];
                        $eDefault = $db_master[$i]['eDefault'];

                        $vPageTitle = 'vPageTitle_'.$vCode;
                        // $tPageDesc = 'tPageDesc_' . $vCode;

                        if ('' === $style_v) {
                            $required = ('Yes' === $eDefault) ? 'required' : '';
                            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                        }

                        /*$vPageSubTitleS = "vPageSubTitle_$vCode";
                        $vPageSubTitle = "vPageSubTitle[$vCode]";
                        $pagedescarr = json_decode($$tPageDesc,true);
                        $Firstdescval = $pagedescarr['FirstDesc'];
                        $Secdescval = $pagedescarr['SecDesc'];
                        $Thirddescval = $pagedescarr['ThirdDesc'];
                        $tPageSecDesc = 'tPageSecDesc_' . $vCode;
                        $tPageThirdDesc = 'tPageThirdDesc_' . $vCode;   */
                        ?>
                                            <div class="row" <?php echo $style_v; ?>>
                                                <div class="col-md-12">
                                                    <label>Page Title (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                </div>
                                                <?php
                            $page_title_class = 'col-md-12';
                        if (count($db_master) > 1) {
                            if ($EN_available) {
                                if ('EN' === $vCode) {
                                    $page_title_class = 'col-md-9';
                                }
                            } else {
                                if ($vCode === $default_lang) {
                                    $page_title_class = 'col-md-9';
                                }
                            }
                        }
                        ?>
                                                <div class="<?php echo $page_title_class; ?>">
                                                    <input type="text" class="form-control" name="<?php echo $vPageTitle; ?>"  id="<?php echo $vPageTitle; ?>" value="<?php echo htmlspecialchars(${$vPageTitle}); ?>" placeholder="<?php echo $vLTitle; ?> Value" <?php echo $required; ?> data-originalvalue="<?php echo htmlspecialchars(${$vPageTitle}); ?>">
                                                    <div class="text-danger" id="<?php echo $vPageTitle.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                </div>

                                                <?php
                        if (count($db_master) > 1) {
                            if ($EN_available) {
                                if ('EN' === $vCode) { ?>
                                                            <div class="col-md-3">
                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageTitle_', 'EN');">Convert To All Language</button>
                                                            </div>
                                                        <?php }
                                } else {
                                    if ($vCode === $default_lang) { ?>
                                                            <div class="col-md-3">
                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
                                                            </div>
                                                        <?php }
                                    }
                        }
                        ?>
                                            </div>

                                            <?php /*<div class="row">
                                                <div class="col-md-12">
                                                    <label>Page Sub Description (<?= $vLTitle; ?>) <?= $required_msg; ?></label>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="<?= $vPageSubTitle; ?>"  id="<?= $vPageSubTitleS; ?>" placeholder="<?= $vPageSubTitleS; ?> Value" <?= $required; ?>><?= $pageSubtitleArr["pageSubtitle_".$vCode]; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row" <?= $style_v ?>>
                                                <div class="col-md-12">
                                                    <label> Page First Description (<?= $vLTitle; ?>) <?= $required_msg; ?></label>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="<?= $tPageDesc; ?>"  id="<?= $tPageDesc; ?>"  placeholder="<?= $tPageDesc; ?> Value" <?= $required; ?>> <?= $Firstdescval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row" <?= $style_v ?>>
                                                <div class="col-md-12">
                                                    <label> Page Second Description (<?= $vLTitle; ?>) <?= $required_msg; ?></label>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="<?= $tPageSecDesc; ?>"  id="<?= $tPageSecDesc; ?>"  placeholder="<?= $tPageSecDesc; ?> Value" <?= $required; ?>> <?= $Secdescval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row" <?= $style_v ?>>
                                                <div class="col-md-12">
                                                    <label> Page Third Description (<?= $vLTitle; ?>) <?= $required_msg; ?></label>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="<?= $tPageThirdDesc; ?>"  id="<?= $tPageThirdDesc; ?>"  placeholder="<?= $tPageThirdDesc; ?> Value" <?= $required; ?>> <?= $Thirddescval; ?></textarea>
                                                </div>
                                                </div>*/ ?>


                                                <?php
                    }
    ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveAboutUsWeb()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageTitle_')">Cancel</button>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>

                            <div  class="modal fade" id="SubDesc_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg" >
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span> About Us - Page Sub Description
                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                            </h4>
                                        </div>

                                        <div class="modal-body">
                                            <?php

    for ($i = 0; $i < $count_all; ++$i) {
        $vCode = $db_master[$i]['vCode'];
        $vLTitle = $db_master[$i]['vTitle'];
        $eDefault = $db_master[$i]['eDefault'];

        if ('' === $style_v) {
            $required = ('Yes' === $eDefault) ? 'required' : '';
            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
        }

        $vPageSubTitleS = "vPageSubTitle_{$vCode}";
        $vPageSubTitle = "vPageSubTitle[{$vCode}]";
        ?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <textarea class="form-control ckeditor" rows="10" name="<?php echo $vPageSubTitle; ?>"  id="<?php echo $vPageSubTitleS; ?>" placeholder="<?php echo $vPageSubTitleS; ?> Value" <?php echo $required; ?>><?php echo $pageSubtitleArr['pageSubtitle_'.$vCode]; ?></textarea>
                                                        <div class="text-danger" id="<?php echo $vPageSubTitleS.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                </div>
                                                <?php
    }
    ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('vPageSubTitle_', 'SubDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>

                            <div  class="modal fade" id="FirstDesc_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg" >
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span> About Us - Page First Description
                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                            </h4>
                                        </div>

                                        <div class="modal-body">
                                            <?php

    for ($i = 0; $i < $count_all; ++$i) {
        $vCode = $db_master[$i]['vCode'];
        $vLTitle = $db_master[$i]['vTitle'];
        $eDefault = $db_master[$i]['eDefault'];

        $tPageDesc = 'tPageDesc_'.$vCode;

        if ('' === $style_v) {
            $required = ('Yes' === $eDefault) ? 'required' : '';
            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
        }

        $pagedescarr = json_decode(${$tPageDesc}, true);
        $Firstdescval = $pagedescarr['FirstDesc'];
        ?>
                                                <div class="row" <?php echo $style_v; ?>>
                                                    <div class="col-md-12">
                                                        <label> Page First Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDesc; ?>"  id="<?php echo $tPageDesc; ?>"  placeholder="<?php echo $tPageDesc; ?> Value" <?php echo $required; ?>> <?php echo $Firstdescval; ?></textarea>
                                                        <div class="text-danger" id="<?php echo $tPageDesc.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                </div>
                                                <?php
    }
    ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tPageDesc_', 'FirstDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>

                            <div  class="modal fade" id="SecondDesc_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg" >
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span> About Us - Page Second Description
                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                            </h4>
                                        </div>

                                        <div class="modal-body">
                                            <?php

    for ($i = 0; $i < $count_all; ++$i) {
        $vCode = $db_master[$i]['vCode'];
        $vLTitle = $db_master[$i]['vTitle'];
        $eDefault = $db_master[$i]['eDefault'];

        $tPageDesc = 'tPageDesc_'.$vCode;

        if ('' === $style_v) {
            $required = ('Yes' === $eDefault) ? 'required' : '';
            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
        }

        $pagedescarr = json_decode(${$tPageDesc}, true);
        $Secdescval = $pagedescarr['SecDesc'];
        $tPageSecDesc = 'tPageSecDesc_'.$vCode;
        ?>
                                                <div class="row" <?php echo $style_v; ?>>
                                                    <div class="col-md-12">
                                                        <label> Page Second Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageSecDesc; ?>"  id="<?php echo $tPageSecDesc; ?>"  placeholder="<?php echo $tPageSecDesc; ?> Value" <?php echo $required; ?>> <?php echo $Secdescval; ?></textarea>
                                                        <div class="text-danger" id="<?php echo $tPageSecDesc.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                </div>
                                                <?php
    }
    ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tPageSecDesc_', 'SecondDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>

                            <div  class="modal fade" id="ThirdDesc_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg" >
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span> About Us - Page Third Description
                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                            </h4>
                                        </div>

                                        <div class="modal-body">
                                            <?php

    for ($i = 0; $i < $count_all; ++$i) {
        $vCode = $db_master[$i]['vCode'];
        $vLTitle = $db_master[$i]['vTitle'];
        $eDefault = $db_master[$i]['eDefault'];

        if ('' === $style_v) {
            $required = ('Yes' === $eDefault) ? 'required' : '';
            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
        }

        $tPageDesc = 'tPageDesc_'.$vCode;
        $pagedescarr = json_decode(${$tPageDesc}, true);
        $Thirddescval = $pagedescarr['ThirdDesc'];
        $tPageThirdDesc = 'tPageThirdDesc_'.$vCode;
        ?>
                                                <div class="row" <?php echo $style_v; ?>>
                                                    <div class="col-md-12">
                                                        <label> Page Third Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageThirdDesc; ?>"  id="<?php echo $tPageThirdDesc; ?>"  placeholder="<?php echo $tPageThirdDesc; ?> Value" <?php echo $required; ?>> <?php echo $Thirddescval; ?></textarea>
                                                        <div class="text-danger" id="<?php echo $tPageThirdDesc.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                </div>
                                                <?php
    }
    ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tPageThirdDesc_', 'ThirdDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>

                        <?php } else { ?>
                            <div class="row" <?php echo $style_v; ?>>
                                <div class="col-md-12">
                                    <label>Page Title <span class="red"> *</span></label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" name="vPageTitle_<?php echo $default_lang; ?>"  id="vPageTitle_<?php echo $default_lang; ?>" value="<?php echo htmlspecialchars($db_data[0]['vPageTitle_'.$default_lang]); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                        <label>Page Sub Description <span class="red"> *</span></label>
                                    <?php } else { ?>
                                        <label>Page Description <span class="red"> *</span></label>
                                    <?php } ?>
                                </div>
                                <div class="col-md-12">
                                    <textarea class="form-control ckeditor" rows="10" id="vPageSubTitle[<?php echo $default_lang; ?>]" name="vPageSubTitle[<?php echo $default_lang; ?>]"><?php echo $pageSubtitleArr['pageSubtitle_'.$default_lang]; ?></textarea>
                                </div>
                            </div>
                            <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                <div class="row" <?php echo $style_v; ?>>
                                    <div class="col-md-12">
                                        <label> Page First Description </label>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea class="form-control ckeditor" rows="10" id="tPageDesc_<?php echo $default_lang; ?>" name="tPageDesc_<?php echo $default_lang; ?>"> <?php echo $FirstdescvalDefault; ?></textarea>
                                    </div>
                                </div>

                                <div class="row" <?php echo $style_v; ?>>
                                    <div class="col-md-12">
                                        <label> Page Second Description </label>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea class="form-control ckeditor" rows="10" id="tPageSecDesc_<?php echo $default_lang; ?>" name="tPageSecDesc_<?php echo $default_lang; ?>"> <?php echo $SecdescvalDefault; ?></textarea>
                                    </div>
                                </div>

                                <div class="row" <?php echo $style_v; ?>>
                                    <div class="col-md-12">
                                        <label> Page Third Description </label>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea class="form-control ckeditor" rows="10" id="tPageThirdDesc_<?php echo $default_lang; ?>" name="tPageThirdDesc_<?php echo $default_lang; ?>"> <?php echo $ThirddescvalDefault; ?></textarea>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>


                        <?php if (!in_array($iPageId, ['23', '24', '25', '26', '27', '48', '49', '50'], true)) { ?>
                            <div class="row" <?php echo $style_v; ?>>
                                <div class="col-md-12">
                                    <label>Meta Title</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" name="vTitle"  id="vTitle" value="<?php echo htmlspecialchars($vTitle); ?>" placeholder="Meta Title">
                                </div>
                            </div>
                            <div class="row" <?php echo $style_v; ?>>
                                <div class="col-md-12">
                                    <label>Meta Keyword</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" name="tMetaKeyword"  id="tMetaKeyword" value="<?php echo htmlspecialchars($tMetaKeyword); ?>" placeholder="Meta Keyword">
                                </div>
                            </div>
                            <div class="row" <?php echo $style_v; ?>>
                                <div class="col-md-12">
                                    <label>Meta Description</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <textarea class="form-control" rows="10" name="tMetaDescription"  id="<?php echo $tMetaDescription; ?>"  placeholder="<?php echo $tMetaDescription; ?> Value" <?php echo $required; ?>> <?php echo $tMetaDescription; ?></textarea>
                                </div>
                            </div>
                            <?php
                        }

if (!in_array($iPageId, ['1', '2', '7', '4', '3', '6', '23', '27', '33'], true)) {
    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageidCubexImage, true)) { ?>
                                <br><br>
                                <?php if (50 !== $iPageId) { ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Image (Left side shown)</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <?php if ('' !== $vImage) { ?>
                                                <a target="_blank" href="<?php echo $images.$vImage; ?>"><img src="<?php echo $images.$vImage; ?>" style="width:200px;height:100px;"></a>
                                            <?php } ?>
                                            <input type="file" name="vImage" id="vImage" />
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Background Image</label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <?php if ('' !== $vImage1) { ?>
                                            <a target="_blank" href="<?php echo $images.$vImage1; ?>"><img src="<?php echo $images.$vImage1; ?>" style="width:200px;height:100px;"></a>
                                        <?php } ?>
                                        <input type="file" name="vImage1" id="vImage1" />
                                    </div>
                                </div>
                            <?php } elseif ('Yes' === $cubexthemeon && 52 === $iPageId) { ?>
                                <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>First Image (Left side shown)</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <?php if ('' !== $vImage) { ?>
                                                <a target="_blank" href="<?php echo $images.$vImage; ?>"><img src="<?php echo $images.$vImage; ?>" style="width:200px;height:100px;"></a>
                                            <?php } ?>
                                            <input type="file" name="vImage" id="vImageaaa2" /><br/>
                                            [Note: Recommended dimension for image is 570 * 640.]
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Second Image (Right side shown)</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <?php if ('' !== $vImage1) { ?>
                                                <a target="_blank" href="<?php echo $images.$vImage1; ?>"><img src="<?php echo $images.$vImage1; ?>" style="width:200px;height:100px;"></a>
                                            <?php } ?>
                                            <input type="file" name="vImage1" id="vImagea1" /><br/>
                                            [Note: Recommended dimension for image is 570 * 640.]
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Third Image (Left side shown)</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <?php if ('' !== $vImage2) { ?>
                                                <a target="_blank" href="<?php echo $images.$vImage2; ?>"><img src="<?php echo $images.$vImage2; ?>" style="width:200px;height:100px;"></a>
                                            <?php } ?>
                                            <input type="file" name="vImage2" id="vImagea2" /><br/>
                                            [Note: Recommended dimension for image is 570 * 640.]
                                        </div>
                                    </div>
                                <?php }?>
                            <?php } else {
                                $style_vimage = '';
                                if (!in_array($iPageId, ['53'], true)) {
                                    $style_vimage = "style = 'display:none;'";
                                }
                                ?>
                                <div class="row" style="<?php echo $style_vimage; ?>">
                                    <div class="col-md-12">
                                        <label>Image</label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <?php if ('' !== $vImage) { ?>
                                            <a target="_blank" href="<?php echo $images.$vImage; ?>"><img src="<?php echo $images.$vImage; ?>" style="width:200px;height:100px;"></a>
                                        <?php } ?>
                                        <input type="file" name="vImage" id="vImage" />
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } if ('48' !== $iPageId && '49' !== $iPageId && '50' !== $iPageId) { ?>
                            <!-- added by SP for pages orderby,active/inactive functionality  -->
                            <div class="row" <?php echo $style_v; ?>>
                                <div class="col-md-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="number" class="form-control" name="iOrderBy" id="iOrderBy" value="<?php echo $iOrderBy; ?>" placeholder="Page displayed according to this number" min="0">
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-12">
                                <?php if (('Edit' === $action && $userObj->hasPermission('edit-pages')) || ('Add' === $action && $userObj->hasPermission('create-pages'))) { ?>
                                    <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Static Page">
                                    <input type="reset" value="Reset" class="btn btn-default">
                                <?php } ?>
                                <!-- <a href="javascript:void(0);" onclick="reset_form('_page_form');" class="btn btn-default">Reset</a> -->
                                <a href="page.php" class="btn btn-default back_link">Cancel</a>
                            </div>
                        </div>


                    </form>
                </div>

                <div id="mobiletab-1" class="tab-pane <?php if ('mobiletab-1' === $aboutactivetab) { ?>active<?php }?>">
                    <?php if ('Edit' === $action) {
                        $id = $iPageId = '1';
                        $sql = 'SELECT * FROM '.$tbl_name." WHERE iPageId = '".$id."'";
                        $db_data = $obj->MySQLSelect($sql);

                        $vLabel = $id;
                        if (count($db_data) > 0) {
                            for ($i = 0; $i < count($db_master); ++$i) {
                                foreach ($db_data as $key => $value) {
                                    $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
                                    ${$vPageTitle} = $value[$vPageTitle];
                                    $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
                                    ${$tPageDesc} = $value[$tPageDesc];
                                    $vPageName = $value['vPageName'];
                                    $vTitle = $value['vTitle'];
                                    $tMetaKeyword = $value['tMetaKeyword'];
                                    $tMetaDescription = $value['tMetaDescription'];
                                    $vImage = $value['vImage'];
                                    $vImage1 = $value['vImage1'];
                                    $vImage2 = $value['vImage2'];
                                    $iOrderBy = $value['iOrderBy']; // added by SP for pages orderby,active/inactive functionality
                                }
                            }
                        }
                    } ?>
            <form method="post" action="" name="_page_form" id="_page_form"  enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                <input type="hidden" name="backlink" id="backlink" value="page.php"/>
                <div class="row">
                    <div class="col-md-12">
                        <label>Page/Section</label>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input type="text" class="form-control" name="vPageName_<?php echo $id; ?>"  id="vPageName_<?php echo $id; ?>" value="<?php echo htmlspecialchars($vPageName); ?>" placeholder="Page Name">
                    </div>
                </div>
                <?php $style_v = '';
if (in_array($iPageId, ['29', '30', '53'], true)) {
    $style_v = "style = 'display:none;'";
}
/*if ($count_all > 0) {
    for ($i = 0; $i < $count_all; $i++) {
        $vCode = $db_master[$i]['vCode'];
        $vLTitle = $db_master[$i]['vTitle'];
        $eDefault = $db_master[$i]['eDefault'];

        $vPageTitle = 'vPageTitle_' . $vCode;
        $tPageDesc = 'tPageDesc_' . $vCode;

        if($style_v=='') {
            $required = ($eDefault == 'Yes') ? 'required' : '';
            $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
        }
        ?>
        <div class="row" <?= $style_v ?>>
            <div class="col-md-12">
                <label>Page Title (<?= $vLTitle; ?>) <?= $required_msg; ?></label>
            </div>
            <div class="col-md-6 col-sm-6">
                <input type="text" class="form-control" name="<?= $vPageTitle.'_'.$id; ?>"  id="<?= $vPageTitle.'_'.$id; ?>" value="<?= htmlspecialchars($$vPageTitle); ?>" placeholder="<?= $vPageTitle; ?> Value" <?= $required; ?>>
            </div>
        </div>
        <!--- Editor -->
        <div class="row" <?= $style_v ?>>
            <div class="col-md-12">
                <label> Page Description (<?= $vLTitle; ?>) <?= $required_msg; ?></label>
            </div>
            <div class="col-md-12">
                <textarea class="form-control ckeditor" rows="10" name="<?= $tPageDesc.'_'.$id; ?>"  id="<?= $tPageDesc.'_'.$id; ?>"  placeholder="<?= $tPageDesc; ?> Value" <?= $required; ?>> <?= $$tPageDesc; ?></textarea>
            </div>
        </div>
        <!--- Editor -->
        <?
    }
}*/ ?>
                    <?php if (count($db_master) > 1) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Page Title <span class="red"> *</span></label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="vPageTitle_1_Default" value="<?php echo $db_data[0]['vPageTitle_'.$default_lang]; ?>" data-originalvalue="<?php echo $db_data[0]['vPageTitle_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editAboutUsApp('Add')" <?php } ?>>
                            </div>
                            <?php if ('' !== $id) { ?>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editAboutUsApp('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label>Page Description <span class="red"> *</span></label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <textarea class="form-control ckeditor" rows="10" id="tPageDesc_1_Default" readonly="readonly"><?php echo $db_data[0]['tPageDesc_'.$default_lang]; ?></textarea>
                            </div>
                            <?php if ('' !== $id) { ?>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescApp('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                </div>
                            <?php } ?>
                        </div>

                        <div  class="modal fade" id="aboutUsApp_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-lg" >
                                <div class="modal-content nimot-class">
                                    <div class="modal-header">
                                        <h4>
                                            <span id="modal_action"></span> Page
                                            <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageTitle_')">x</button>
                                        </h4>
                                    </div>

                                    <div class="modal-body">
                                        <?php

                    for ($i = 0; $i < $count_all; ++$i) {
                        $vCode = $db_master[$i]['vCode'];
                        $vLTitle = $db_master[$i]['vTitle'];
                        $eDefault = $db_master[$i]['eDefault'];

                        $vPageTitle = 'vPageTitle_'.$vCode;
                        $vPageTitleId = 'vPageTitle_1_'.$vCode;
                        $tPageDesc = 'tPageDesc_'.$vCode;
                        $tPageDescId = 'tPageDesc_1_'.$vCode;

                        if ('' === $style_v) {
                            $required = ('Yes' === $eDefault) ? 'required' : '';
                            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                        }
                        ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Page Title (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

                                                </div>
                                                <?php
                            $page_title_class = 'col-md-12';
                        if (count($db_master) > 1) {
                            if ($EN_available) {
                                if ('EN' === $vCode) {
                                    $page_title_class = 'col-md-9';
                                }
                            } else {
                                if ($vCode === $default_lang) {
                                    $page_title_class = 'col-md-9';
                                }
                            }
                        }
                        ?>
                                                <div class="<?php echo $page_title_class; ?>">
                                                    <input type="text" class="form-control" name="<?php echo $vPageTitle.'_'.$id; ?>" id="<?php echo $vPageTitleId; ?>" value="<?php echo htmlspecialchars(${$vPageTitle}); ?>" data-originalvalue="<?php echo htmlspecialchars(${$vPageTitle}); ?>" placeholder="<?php echo $vLTitle; ?> Value" <?php echo $required; ?>>
                                                    <div class="text-danger" id="<?php echo $vPageTitleId.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                </div>

                                                <?php
                        if (count($db_master) > 1) {
                            if ($EN_available) {
                                if ('EN' === $vCode) { ?>
                                                            <div class="col-md-3">
                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageTitle_1_', 'EN');">Convert To All Language</button>
                                                            </div>
                                                        <?php }
                                } else {
                                    if ($vCode === $default_lang) { ?>
                                                            <div class="col-md-3">
                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageTitle_1_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
                                                            </div>
                                                        <?php }
                                    }
                        }
                        ?>
                                            </div>
                                            <?php
                    }
                        ?>
                                    </div>
                                    <div class="modal-footer" style="margin-top: 0">
                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                            <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveAboutUsApp()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageTitle_1_')">Cancel</button>
                                        </div>
                                    </div>

                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>

                        <div  class="modal fade" id="DescApp_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-lg" >
                                <div class="modal-content nimot-class">
                                    <div class="modal-header">
                                        <h4>
                                            <span id="modal_action"></span> Page
                                            <button type="button" class="close" data-dismiss="modal">x</button>
                                        </h4>
                                    </div>

                                    <div class="modal-body">
                                        <?php

                        for ($i = 0; $i < $count_all; ++$i) {
                            $vCode = $db_master[$i]['vCode'];
                            $vLTitle = $db_master[$i]['vTitle'];
                            $eDefault = $db_master[$i]['eDefault'];

                            $tPageDesc = 'tPageDesc_'.$vCode;
                            $tPageDescId = 'tPageDesc_1_'.$vCode;

                            if ('' === $style_v) {
                                $required = ('Yes' === $eDefault) ? 'required' : '';
                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                            }
                            ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Page Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

                                                </div>
                                                <div class="col-md-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDesc.'_'.$id; ?>"  id="<?php echo $tPageDescId; ?>"  placeholder="<?php echo $vLTitle; ?> Value" <?php echo $required; ?>> <?php echo ${$tPageDesc}; ?></textarea>
                                                    <div class="text-danger" id="<?php echo $tPageDescId.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                </div>
                                            </div>
                                            <?php
                        }
                        ?>
                                    </div>
                                    <div class="modal-footer" style="margin-top: 0">
                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                            <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescApp()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>

                                    <div style="clear:both;"></div>
                                </div>
                            </div>

                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Page Title <span class="red"> *</span></label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" class="form-control" name="vPageTitle_<?php echo $default_lang; ?>_1" id="vPageTitle_1_<?php echo $default_lang; ?>" value="<?php echo $db_data[0]['vPageTitle_'.$default_lang]; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label>Page Description <span class="red"> *</span></label>
                            </div>
                            <div class="col-md-12">
                                <textarea class="form-control ckeditor" rows="10" name="tPageDesc_<?php echo $default_lang; ?>_1" id="tPageDesc_<?php echo $default_lang; ?>_1"><?php echo $db_data[0]['tPageDesc_'.$default_lang]; ?></textarea>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (!in_array($iPageId, ['23', '24', '25', '26', '27', '48', '49', '50'], true)) {
                        ?>
                        <div class="row" <?php echo $style_v; ?>>
                            <div class="col-md-12">
                                <label>Meta Title</label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" class="form-control" name="vTitle_<?php echo $id; ?>"  id="vTitle_<?php echo $id; ?>" value="<?php echo htmlspecialchars($vTitle); ?>" placeholder="Meta Title">
                            </div>
                        </div>
                        <div class="row" <?php echo $style_v; ?>>
                            <div class="col-md-12">
                                <label>Meta Keyword</label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" class="form-control" name="tMetaKeyword_<?php echo $id; ?>"  id="tMetaKeyword_<?php echo $id; ?>" value="<?php echo htmlspecialchars($tMetaKeyword); ?>" placeholder="Meta Keyword">
                            </div>
                        </div>
                        <div class="row" <?php echo $style_v; ?>>
                            <div class="col-md-12">
                                <label>Meta Description</label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <textarea class="form-control" rows="10" name="tMetaDescription_<?php echo $id; ?>"  id="<?php echo $tMetaDescription.'_'.$id; ?>"  placeholder="<?php echo $tMetaDescription; ?> Value" <?php echo $required; ?>> <?php echo $tMetaDescription; ?></textarea>
                            </div>
                        </div>
                        <?php
                    }
if ('48' !== $iPageId && '49' !== $iPageId && '50' !== $iPageId) { ?>
                        <!-- added by SP for pages orderby,active/inactive functionality  -->
                        <div class="row" <?php echo $style_v; ?>>
                            <div class="col-md-12">
                                <label>Display Order</label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="number" class="form-control" name="iOrderBy_<?php echo $id; ?>" id="iOrderBy_<?php echo $id; ?>" value="<?php echo $iOrderBy; ?>" placeholder="Page displayed according to this number" min="0">
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-md-12">
                            <?php if (('Edit' === $action && $userObj->hasPermission('edit-pages')) || ('Add' === $action && $userObj->hasPermission('create-pages'))) { ?>
                                <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Static Page">
                                <input type="reset" value="Reset" class="btn btn-default">
                            <?php } ?>
                            <a href="page.php" class="btn btn-default back_link">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>

/*var somethingChanged = false;
$('.tab-pane.active input').change(function() {
    somethingChanged = true;
    if(somethingChanged == true){
        $( "a.pagerlink" ).click(function() {
          somethingChanged = false;
          confirm("Press a button!");
        });
    }
});
alert(somethingChanged);*/
/*var tabValue = $("#tabs .nav.nav-tabs li.active a").attr("href");
alert(tabValue);*/
/*$('a.pagerlink').click(function() {
    var tabValue = $(this).attr('href');
    var slug = tabValue.split('-').pop();
    passVariable(slug);
});
function passVariable(slug){
    // get the current url and append variable
    var url = document.location.href;
    // to prevent looping
    var exists = document.location.href.indexOf('&tabid=');
    var newurl = replaceUrlParam(url,"tabid",slug);
    console.log(newurl);
    window.location = newurl;
    if(exists < 0){
          // redirect passing variable
    }
}
function replaceUrlParam(url, paramName, paramValue)
{
    if (paramValue == null) {
        paramValue = '';
    }
    var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
    if (url.search(pattern)>=0) {
        return url.replace(pattern,'$1' + paramValue + '$2');
    }
    url = url.replace(/[?#]$/,'');
    return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
}
*/
/*
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};*/
</script>