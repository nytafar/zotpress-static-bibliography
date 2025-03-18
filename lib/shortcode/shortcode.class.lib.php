<?php

class zotpressLib
{
    private $account = "";
    private $type = false;
    private $filters = false;
    private $minlength = false;
    private $maxresults = false;
    private $maxperpage = false;
    private $tag = false;
    private $maxtags = 100;
    private $style = false;
    private $sortby = false;
    private $order = false;
    private $citeable = false;
    private $collection = false;
    private $downloadable = false;
    private $showtags = false;
    private $showimage = false;
    private $is_admin = false;
    private $urlwrap = false;
    private $toplevel = false;
    private $target = false;
    private $maxpages = 10;
    private $browsebar = true;

    public function __construct()
    {
        // Called automatically when an instance is instantiated
    }

    public function setAccount($account)
    {
        $this->account = $account;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function setType($type)
    {
        if ($type === false || $type == "basic") {
            $type = "dropdown";
        }
        $this->type = $type;
    }

    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function setMinLength($minlength)
    {
        $this->minlength = $minlength;
    }

    public function getMinLength()
    {
        return $this->minlength;
    }

    public function setMaxResults($maxresults)
    {
        $this->maxresults = $maxresults;
    }

    public function getMaxResults()
    {
        return $this->maxresults;
    }

    public function setMaxPerPage($maxperpage)
    {
        $this->maxperpage = $maxperpage;
    }

    public function getMaxPerPage()
    {
        return $this->maxperpage;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    public function setMaxTags($maxtags)
    {
        $this->maxtags = $maxtags;
    }

    public function setCiteable($citeable)
    {
        $this->citeable = $citeable;
    }

    public function setStyle($style)
    {
        $this->style = strtolower($style);
    }

    public function setCollection($collection_id)
    {
        $this->collection = $collection_id;
    }

    public function setSortBy($sortby)
    {
        $this->sortby = strtolower($sortby);
    }

    public function setOrder($order)
    {
        $this->order = strtolower($order);
    }

    public function setDownloadable($download)
    {
        $this->downloadable = $download;
    }

    public function setShowTags($showtags)
    {
        $this->showtags = $showtags == "yes" || $showtags == "true" || $showtags === true;
    }

    public function setShowImage($showimage)
    {
        $this->showimage = $showimage == "yes" || $showimage == "true" || $showimage === true;
    }

    public function setAdmin($setAdmin)
    {
        $this->is_admin = $setAdmin;
    }

    public function setURLWrap($urlwrap)
    {
        $this->urlwrap = $urlwrap;
    }

    public function setTopLevel($toplevel)
    {
        $this->toplevel = $toplevel;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function setMaxPages($maxpages)
    {
        $this->maxpages = $maxpages;
    }

    public function getMaxPages()
    {
        return $this->maxpages;
    }

    public function setBrowseBar($browsebar)
    {
        $this->browsebar = $browsebar == "show" || $browsebar == "active" || $browsebar == "visible" || $browsebar == "yes" || $browsebar == "true" || $browsebar === true || $browsebar == 1;
    }

    public function getLib()
    {
        global $wpdb;

        $content = "";

        // API User ID
        $api_user_id = $this->getAccount()->api_user_id;

        // Collection ID
        $collection_id = $this->collection;

        // Tag ID
        $tag_id = $this->tag;

        // Browse instance ID
        $instance_id = "zotpress-lib-" . md5(
            $api_user_id .
            $collection_id .
            $this->type .
            $tag_id .
            $this->style .
            $this->sortby .
            $this->order .
            $this->citeable .
            $this->downloadable .
            $this->showtags .
            $this->showimage .
            $this->toplevel .
            $this->target .
            $this->urlwrap .
            $this->is_admin .
            $this->minlength .
            $this->maxresults .
            $this->maxperpage .
            $this->maxtags .
            $this->maxpages .
            $this->filters .
            $this->browsebar
        );

        $content .= "<a name=\"" . $instance_id . "\"></a>\n";
        $content .= "<div id=\"" . $instance_id . "\" class=\"zp-Library zp-Browse\">\n";
        $content .= '<span class="ZP_API_USER_ID" style="display: none;">' . $api_user_id . '</span>';
        if ($collection_id) $content .= '<span class="ZP_COLLECTION_ID" style="display: none;">' . $collection_id . '</span>';
        if ($tag_id) $content .= '<span class="ZP_TAG_ID" style="display: none;">' . $tag_id . '</span>';
        $content .= '<span class="ZP_MAXTAGS" style="display: none;">' . $this->maxtags . '</span>';
        $content .= '<span class="ZP_STYLE" style="display: none;">' . $this->style . '</span>';
        $content .= '<span class="ZP_SORTBY" style="display: none;">' . $this->sortby . '</span>';
        $content .= '<span class="ZP_ORDER" style="display: none;">' . $this->order . '</span>';
        $content .= '<span class="ZP_CITEABLE" style="display: none;">' . $this->citeable . '</span>';
        $content .= '<span class="ZP_DOWNLOADABLE" style="display: none;">' . $this->downloadable . '</span>';
        $content .= '<span class="ZP_SHOWTAGS" style="display: none;">' . $this->showtags . '</span>';
        $content .= '<span class="ZP_SHOWIMAGE" style="display: none;">' . $this->showimage . '</span>';
        if ($this->toplevel) $content .= '<span class="ZP_TOPLEVEL" style="display: none;">' . $this->toplevel . '</span>';
        $content .= '<span class="ZP_TARGET" style="display: none;">' . $this->target . '</span>';
        $content .= '<span class="ZP_URLWRAP" style="display: none;">' . $this->urlwrap . '</span>';
        if ($this->is_admin) $content .= '<span class="ZP_ISADMIN" style="display: none;">' . $this->is_admin . '</span>';
        $content .= '<span class="ZP_BROWSEBAR" style="display: none;">' . $this->browsebar . '</span>';

        $maxperpage = 10;
        if ($this->getMaxPerPage() !== false) $maxperpage = (int)$this->getMaxPerPage();
        $content .= '<input type="hidden" class="ZOTPRESS_AC_MAXPERPAGE" name="ZOTPRESS_AC_MAXPERPAGE" value="' . $maxperpage . '">';

        // Deal with Browse Bar by Type
        if ($this->type == "dropdown") {
            if ($this->browsebar) {
                $content .= '<div class="zp-Browse-Bar">';
                $content .= '<div class="zp-Browse-Collections">';
                $content .= "<div class='zp-Browse-Select'>\n";
                $content .= "<select class='zp-Browse-Collections-Select' class='loading'>\n";
                $content .= "<option class='loading' value='loading'>" . __('Loading', 'zotpress') . " ...</option>";
                if ($tag_id) $content .= "<option value='blank'>--" . __('No Collection Selected', 'zotpress') . "--</option>";
                if (!$tag_id && !$collection_id) $content .= "<option value='blank' class='blank'>" . __('Top Level', 'zotpress') . "</option>";
                $content .= "</select>\n";
                $content .= "</div>\n\n";
                $content .= '</div><!-- .zp-Browse-Collections -->';
                $content .= "\n";
                $content .= '<div class="zp-Browse-Tags">';
                $content .= "<div class='zp-Browse-Select'>\n";
                $content .= '<select class="zp-List-Tags" name="zp-List-Tags" class="loading">';
                $content .= "\n<option class='loading' value='loading'>" . __('Loading', 'zotpress') . " ...</option>\n";
                $content .= "</select>\n";
                $content .= "</div>\n\n";
                $content .= '</div><!-- .zp-Browse-Tags -->';
                $content .= "\n";
                $content .= '</div><!-- .zp-Browse-Bar -->';
            }
        } else {
            $content .= '<div class="zp-Browse-Bar">';
            $content .= '<div class="zp-Zotpress-SearchBox">';
            $content .= '<input class="zp-Zotpress-SearchBox-Input" class="help" type="text" placeholder="' . __('Type to search', 'zotpress') . '" />';
            if ($this->filters) {
                $content .= "<div class='zp-SearchBy-Container'>";
                $content .= "<span class=\"zp-SearchBy\">" . __('Search by', 'zotpress') . ":</span>";
                $filters = explode(",", $this->filters);
                foreach ($filters as $id => $filter) {
                    $filter = $filter == "tags" ? "tag" : "item";
                    $content .= '<div class="zpSearchFilterContainer">';
                    $content .= '<input type="radio" name="zpSearchFilters" class="' . $filter . '" value="' . $filter . '"';
                    if ($id == 0 || count($filters) == 1) $content .= ' checked="checked"';
                    $content .= '><label for="' . $filter . '">' . $filter . '</label>';
                    $content .= '</div>';
                    $content .= "\n";
                }
                $content .= "</div>\n\n";
            }
            $minlength = 3;
            if ($this->getMinLength() !== false) $minlength = (int)$this->getMinLength();
            $content .= '<input type="hidden" class="ZOTPRESS_AC_MINLENGTH" name="ZOTPRESS_AC_MINLENGTH" value="' . $minlength . '" />';
            $maxresults = 50;
            if ($this->getMaxResults() !== false) $maxresults = (int)$this->getMaxResults();
            $content .= '<input type="hidden" class="ZOTPRESS_AC_MAXRESULTS" name="ZOTPRESS_AC_MAXRESULTS" value="' . $maxresults . '" />';
            $maxpages = (int)$this->getMaxPages();
            $content .= '<input type="hidden" class="ZOTPRESS_AC_MAXPAGES" name="ZOTPRESS_AC_MAXPAGES" value="' . $maxpages . '" />';
            $downloadable = false;
            if ($this->downloadable) $downloadable = $this->downloadable;
            $citeable = false;
            if ($this->citeable) $citeable = $this->citeable;
            $showimages = false;
            if ($this->showimage) $showimages = $this->showimage;
            $content .= '<input type="hidden" class="ZOTPRESS_AC_DOWNLOAD" name="ZOTPRESS_AC_DOWNLOAD" value="' . $downloadable . '" />';
            $content .= '<input type="hidden" class="ZOTPRESS_AC_CITE" name="ZOTPRESS_AC_CITE" value="' . $citeable . '" />';
            if ($showimages) $content .= '<input type="hidden" class="ZOTPRESS_AC_IMAGES" name="ZOTPRESS_AC_IMAGES" value="true" />';
            $content .= '<input type="hidden" class="ZOTPRESS_PLUGIN_URL" name="ZOTPRESS_PLUGIN_URL" value="' . ZOTPRESS_PLUGIN_URL . '" />';
            $content .= '<input type="hidden" class="ZOTPRESS_USER" name="ZOTPRESS_USER" value="' . $this->getAccount()->api_user_id . '" />';
            $content .= '</div>';
            $content .= "\n";
            $content .= '</div><!-- .zp-Browse-Bar -->';
        }

        $content .= "\n\n";
        $content .= '<div class="zp-List';
        if ($this->type == "dropdown") {
            $content .= ' loading">';
            if ($collection_id) {
                $content .= "<div class='zp-Collection-Title'>";
                $content .= "<span class='name'>";
                $content .= __('Collection items', 'zotpress') . ":";
                $content .= "</span>";
                if (is_admin()) $content .= "<label for='item_key'>" . __('Collection Key', 'zotpress') . ":</label><input type='text' name='item_key' class='item_key' value='" . $collection_id . "'>\n";
                $content .= "</div>\n";
            } elseif ($tag_id) {
                $content .= "<div class='zp-Collection-Title'>" . __('Viewing items tagged', 'zotpress') . " \"<strong>" . str_replace("+", " ", $tag_id) . "</strong>\"</div>\n";
            } elseif ($this->toplevel == "toplevel" || $this->toplevel === false) {
                $content .= "<div class='zp-Collection-Title'>" . __('Top Level Items', 'zotpress') . "</div>\n";
            } else {
                $content .= "<div class='zp-Collection-Title'>" . __('Default Collection Items', 'zotpress') . "</div>\n";
            }
        } else {
            $content .= "\">";
            $content .= '<img class="zpSearchLoading" src="' . ZOTPRESS_PLUGIN_URL . '/images/loading_default.gif" alt="thinking" />';
        }
        $content .= '<div class="zpSearchResultsContainer"></div>';
        $content .= '<div class="zpSearchResultsPagingContainer">';
        $content .= '<div class="zpSearchResultsPagingContainerInner">';
        $content .= '<div class="zpSearchResultsPagingCrop">';
        $content .= '<div class="zpSearchResultsPaging">';
        $content .= '</div></div></div></div>';
        $content .= '</div><!-- .zp-List -->';
        $content .= "\n";
        $content .= '</div><!-- .zp-Browse -->';

        return $content . "\n\n";
    }
}
?>