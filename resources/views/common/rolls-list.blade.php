<div class="x_panel">
    <div class="x_title">
        <h2>Roll List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        {!! csrf_field() !!}
        <script type="text/x-kendo-template" id="toolbarTemplate">
            <div class="pull-left">
                <a class="k-button k-state-disabled" id="editButton">
                    <span class="k-icon k-edit">&nbsp;</span>
                    <span>Open</span>
                </a>
                <a class="k-button k-state-disabled" id="deleteButton">
                    <span class="k-icon k-i-close">&nbsp;</span>
                    <span>Delete</span>
                </a>
                <a class="k-button" id="importButton">
                    <span class="k-icon k-i-arrow-60-up">&nbsp;</span>
                    <span>Import</span>
                </a>
                <a class="k-button" id="importFromURLsButton">
                    <span class="k-icon k-i-arrow-60-up">&nbsp;</span>
                    <span>Import From URLs</span>
                </a>
            </div>
            <div class="pull-right">
                <span class="k-textbox k-space-left">
                    <a class="k-icon k-i-search">&nbsp;</a>
                    <input type="text" id="searchFilter" placeholder="search"/>
                </span>
            </div>
            <div class="clearfix"></div>
        </script>
        <div>
            <div id="grid"></div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>