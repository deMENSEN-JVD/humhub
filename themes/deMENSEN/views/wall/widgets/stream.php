<?php
/**
 * This view shows the streaming wall by WallStreamWidget.
 *
 * @property String $reloadUrl is the url to load more entries
 * @property String $startUrl is the url to load the first entries
 * @property String $singleEntryUrl is the url to load a single entry
 *
 * @package humhub.modules_core.wall
 * @since 0.5
 */
?>
<?php if ($this->showFilters) { ?>
    <ul class="nav nav-tabs wallFilterPanel" id="filter" style="display: none;">
        <li class=" dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo Yii::t('WallModule.widgets_views_stream', 'Filter'); ?> <b
                    class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="#" class="wallFilter" id="filter_entry_userinvoled"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Where I´m involved'); ?></a>
                </li>
                <li><a href="#" class="wallFilter" id="filter_entry_mine"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Created by me'); ?></a></li>

                <!-- post module related -->
                <li><a href="#" class="wallFilter" id="filter_entry_files"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Content with attached files'); ?>
                    </a></li>
                <li><a href="#" class="wallFilter" id="filter_posts_links"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Posts with links'); ?></a>
                </li>
                <li><a href="#" class="wallFilter" id="filter_model_posts"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Posts only'); ?></a></li>
                <!-- /post module related -->

                <li class="divider"></li>

                <li><a href="#" class="wallFilter" id="filter_entry_archived"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Include archived posts'); ?>
                    </a></li>
                <li><a href="#" class="wallFilter" id="filter_visibility_public"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Only public posts'); ?></a>
                </li>
                <li><a href="#" class="wallFilter" id="filter_visibility_private"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Only private posts'); ?></a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo Yii::t('WallModule.widgets_views_stream', 'Sorting'); ?>
                <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="#" class="wallSorting" id="sorting_c"><i
                            class="fa fa-check-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Creation time'); ?></a></li>
                <li><a href="#" class="wallSorting" id="sorting_u"><i
                            class="fa fa-square-o"></i> <?php echo Yii::t('WallModule.widgets_views_stream', 'Last update'); ?></a></li>
            </ul>
        </li>
    </ul>
<?php } ?>

<div id="wallStream">

    <!-- DIV for a normal wall stream -->
    <div class="s2_stream" style="display:none">

        <div class="s2_streamContent"></div>
        <div class="loader streamLoader">
            <div class="sk-spinner sk-spinner-three-bounce">
                <div class="sk-bounce1"></div>
                <div class="sk-bounce2"></div>
                <div class="sk-bounce3"></div>
            </div>
        </div>

        <div class="emptyStreamMessage">

            <div class="placeholder <?php echo $this->messageStreamEmptyCss; ?>">
                <div class="panel">
                    <div class="panel-body">
                        <?php echo $this->messageStreamEmpty; ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="emptyFilterStreamMessage">
            <div class="placeholder <?php echo $this->messageStreamEmptyWithFiltersCss; ?>">
                <div class="panel">
                    <div class="panel-body">
                        <?php echo $this->messageStreamEmptyWithFilters; ?>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- DIV for an single wall entry -->
    <div class="s2_single" style="display: none;">
        <div class="back_button_holder">
            <a href="#"
               class="singleBackLink btn btn-primary"><?php echo Yii::t('WallModule.widgets_views_stream', 'Back to stream'); ?></a><br><br>
        </div>
        <div class="p_border"></div>

        <div class="s2_singleContent"></div>
        <div class="loader streamLoaderSingle"></div>
        <div class="test"></div>
    </div>
</div>

<!-- show "Load More" button on mobile devices -->
<div class="col-md-12 text-center visible-xs visible-sm">
    <button id="btn-load-more" class="btn btn-primary btn-lg ">Load more</button>
    <br/><br/>
</div>

<script type="text/javascript">
/**
 * Return parseHtml result and delete dublicated entries from container
 * @param {object} json JSON object
 * @param {object} container Container with entries
 * @returns {string} HTML string
 */
function parseEntriesHtml(json, container) {
    function removeDublicates(entryIds, container) {
        for (var i = 0, count = entryIds.length; i < count; i++) {
            if ($(container).find('#wallEntry_' + entryIds[i]).length) {
                $("#wallEntry_" + entryIds[i]).remove();
            }
        }
    }
    if (typeof container !== 'undefined') {
        removeDublicates(json.entryIds, container);
    }
    return parseHtml(json.output);
}

// Add new posts to stream
var spacesTotalItems = null;

$(document).on("newFrontEndInfo", function(event) {
    var spaces = event.info.workspaces;
    if (!spacesTotalItems) {
        // Init spacesTotalItems
        spacesTotalItems = {};
        for (var i in spaces) {
            var guid = spaces[i].guid;
            spacesTotalItems[guid] = spaces[i].totalItems;
        }
        return;
    }
    var currentGuid = "<?php if (is_object($this->contentContainer)) { echo $this->contentContainer->guid; } ?>";
    var isDashboard = (streamUrl.indexOf("dashboard") > -1);
    if (currentGuid == "" && !isDashboard) {
        return;
    }
    var itemsToLoad = 0;
    for (var i in spaces) {
        var guid = spaces[i].guid;
        var newTotalItems = spaces[i].totalItems;
        var oldTotalItems = spacesTotalItems[guid];
        if (!oldTotalItems) {
            // New space
            spacesTotalItems[guid] = newTotalItems;
            continue;
        }
        if (isDashboard) {
            itemsToLoad += newTotalItems - oldTotalItems;
            spacesTotalItems[guid] = newTotalItems;
            continue;
        }
        if (currentGuid == guid && oldTotalItems < newTotalItems) {
            itemsToLoad = newTotalItems - oldTotalItems;
            spacesTotalItems[guid] = newTotalItems;
            break;
        }
    }
    if (itemsToLoad > 0) {
        // There are new items for this wall, let's load them
        var url = streamUrl;
        url = url.replace('-filter-', '');
        url = url.replace('-sort-', '');
        url = url.replace('-from-', '');
        url = url.replace('-limit-', itemsToLoad);
        jQuery.getJSON(url, function (json) {
            currentStream.loadedEntryCount += json.counter;
            streamDiv = $(currentStream.baseDiv).find(".s2_streamContent");
            $(parseEntriesHtml(json, streamDiv)).prependTo($(streamDiv)).fadeIn('fast');
            currentStream.onNewEntries();
            $(currentStream.baseDiv).find(".emptyStreamMessage").hide();
        });
    }
});
</script>

