var SlideList = {
};

SlideList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: SlideList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: {
                mode: "single",
                allowUnsort: false
            },
            selectable: 'multiple',
            columns: [   
                { field: 'location', title: 'Slide Image' },
                { field: 'title', title: 'Title' },
                { field: 'id', title: 'ID' }                
            ],
            change: function( e )
            {
                SlideList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                SlideList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            rowTemplate: kendo.template($("#rowTemplate").html()),
        });

        $( '#grid' ).data( 'kendoGrid' ).table.kendoSortable({
            filter: ">tbody >tr",
            hint: $.noop,
            cursor: "move",
            placeholder: function(element) {
                return element.clone().addClass("k-state-hover").css("opacity", 0.65);
            },
            container: "#grid tbody",
            change: function(e) {
                var skip = $( '#grid' ).data( 'kendoGrid' ).dataSource.skip(),
                    oldIndex = e.oldIndex + skip,
                    newIndex = e.newIndex + skip,
                    data = $( '#grid' ).data( 'kendoGrid' ).dataSource.data(),
                    dataItem = $( '#grid' ).data( 'kendoGrid' ).dataSource.getByUid(e.item.data("uid"));

                $( '#grid' ).data( 'kendoGrid' ).dataSource.remove(dataItem);
                $( '#grid' ).data( 'kendoGrid' ).dataSource.insert(newIndex, dataItem);
                
                // update slides priorities
                dataItems = $( '#grid' ).data( 'kendoGrid' ).dataSource.view();
                var slide_priorities = [];
                for (i=0; i<dataItems.length; i++) {
                    dataItems[i].set('priority', i+1);
                    slide_priority = {
                        "id": dataItems[i]['id'],
                        "priority": dataItems[i]['priority']
                    };
                    slide_priorities.push(slide_priority);                
                }          
                slide_priorities = {
                    slide_priorities: slide_priorities
                };
                SlideList.updatePriorities(slide_priorities);
            }
        });
}

SlideList.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: false,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/staff/getSlides",
                    dataType: 'json',
                    data:
                    {
                        filters: SlideList.getFilters()
                    }
                }
            },
            schema:
            {
                model: SlideList.getModel(),
                data: 'data',
                total: 'total'
            }
        });
}

SlideList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id',
            fields: {
                image: { type: "string" },
                title: { type: "string" },
                priority: { type: "number" },
                id: { type: "number" }
            }            
        });
}

SlideList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchFilter' ).val();
        }
    }

    return filters;
}

SlideList.filterGrid = function()
{
    SlideList.getGrid().dataSource.filter({});
}

SlideList.filters = function()
{
    var filters = [];
    filters.push( { app: 'slides', grid: 'grid', filterName: 'search', filterValue: SlideList.getFilters().search() } );

    return filters;
}

SlideList.editSlide = function()
{
    var uid = ( SlideList.getGrid().select().data() ) ? SlideList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = SlideList.getGrid().dataSource.getByUid( uid );
        _slide_id = selected.id;

        $( '#mainContentDiv' ).load( "/staff/getSlideForm" );
    }
}

SlideList.addListeners = function()
{
    $( 'table' ).dblclick( SlideList.editSlide );
    $( '#searchFilter' ).keyup( SlideList.filterGrid );
    $( '#searchFilter' ).click( SlideList.filterGrid );
    $( '#addButton' ).click( SlideList.addSlide );
    $( '#editButton' ).click( SlideList.editSlide );
    $( '#deleteButton' ).click( SlideList.deleteSlides );
}

SlideList.addSlide = function()
{
    _slide_id = 0;
    $( '#mainContentDiv' ).load( "/staff/getSlideForm" );
}

SlideList.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editButton' ).addClass( 'k-state-disabled' );
    }

    if( selectedRows.length > 0 )
    {
        $( '#deleteButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deleteButton' ).addClass( 'k-state-disabled' );
    }
}

SlideList.deleteSlides = function()
{
    var ids = [];
    var selected = SlideList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( SlideList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/staff/deleteSlides", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            SlideList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected slides?");
}

SlideList.updatePriorities = function(slide_priorities)
{   
    slide_priorities._token = $('[name="_token"]').val();
    $.post("updateSlidePriorities", slide_priorities, function(response){
    });
}

SlideList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    SlideList.initGrid();
    SlideList.addListeners();
});