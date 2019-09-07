var PostList = {
};

PostList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: PostList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'id', title: 'Post ID' },
                { field: 'category', title: 'Category' },
                { field: 'title', title: 'Title' },
                { field: 'alias', title: 'Alias' },
                { field: 'excerpt', title: 'Excerpt' }
            ],
            change: function( e )
            {
                PostList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                PostList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

PostList.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/staff/getPosts",
                    dataType: 'json',
                    data:
                    {
                        filters: PostList.getFilters()
                    }
                }
            },
            schema:
            {
                model: PostList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

PostList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

PostList.getFilters = function()
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

PostList.filterGrid = function()
{
    PostList.getGrid().dataSource.filter({});
}

PostList.filters = function()
{
    var filters = [];

    filters.push( { app: 'posts', grid: 'grid', filterName: 'search', filterValue: PostList.getFilters().search() } );

    return filters;
}

PostList.editPost = function()
{
    var uid = ( PostList.getGrid().select().data() ) ? PostList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = PostList.getGrid().dataSource.getByUid( uid );
        _post_id = selected.id;

        $( '#mainContentDiv' ).load( "/staff/getPostForm" );
    }
}

PostList.addListeners = function()
{
    $( 'table' ).dblclick( PostList.editPost );
    $( '#searchFilter' ).keyup( PostList.filterGrid );
    $( '#searchFilter' ).click( PostList.filterGrid );
    $( '#addButton' ).click( PostList.addPost );
    $( '#editButton' ).click( PostList.editPost );
    $( '#deleteButton' ).click( PostList.deletePosts );
}

PostList.addPost = function()
{
    _post_id = 0;
    $( '#mainContentDiv' ).load( "/staff/getPostForm" );
}

PostList.setSelected = function( selectedRows )
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

PostList.deletePosts = function()
{
    var ids = [];
    var selected = PostList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( PostList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/staff/deletePosts", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            PostList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected posts?");
}

PostList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    PostList.initGrid();
    PostList.addListeners();
});