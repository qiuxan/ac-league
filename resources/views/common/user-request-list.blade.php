<div class="x_panel">
    <div class="x_title">
        <h2>User Request List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        {!! csrf_field() !!}
        <script type="text/x-kendo-template" id="toolbarTemplate">
            <div class="pull-right">
                <span class="k-textbox k-space-left">
                    <a class="k-icon k-i-search">&nbsp;</a>
                    <input type="text" id="searchFilter" placeholder="search"/>
                </span>
            </div>
            <div class="clearfix"></div>
        </script>
        <div>
            <div id="grid" style="height:1000px"></div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>