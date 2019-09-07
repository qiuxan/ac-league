var MenuList = {
};

MenuList.initTree = function() {
    $( "div.menuWrap").css( 'height', $( window ).height() - 130 );
    $( "#menuTree" ).kendoTreeView({
        dataSource: MenuList.menuTreeDataSource(),
        dataTextField: "name",
        dragAndDrop: true,
        select: MenuList.menuSelected,
        drop: MenuList.menuDrop
    });
}

MenuList.menuTree = function()
{
    return $( "#menuTree" ).data( 'kendoTreeView' );
}

MenuList.menuTreeDataSource = function()
{
    return new kendo.data.HierarchicalDataSource(
        {
            transport:
            {
                read:
                {
                    url: '/staff/getMenus',
                    dataType: "json"
                }
            },
            schema:
            {
                model:
                {
                    id: "id",
                    hasChildren: "hasChildren"
                }
            }
        });
}

MenuList.newMenu = function()
{
    _menu_id = 0;
    _parent_id = 0;
    MenuList.menuTree().select($());
    MenuList.menuSelectionChange( null );
    MenuForm.loadMenuViewModel();
}

MenuList.addMenu = function()
{
    _menu_id = 0;
    MenuList.showMenu();
}

MenuList.editMenu = function()
{
    var selectedNode = MenuList.menuTree().select();

    if( selectedNode.length > 0 )
    {
        var selected = selectedNode ? MenuList.menuTree().dataItem( selectedNode ) : null;
        _menu_id = selected.id;
        MenuList.showMenu();
    }
}

MenuList.showMenu = function()
{
    MenuForm.loadMenuViewModel();
}

MenuList.updateTree = function( menu_id )
{
    var selectedNode = MenuList.menuTree().select();

    if( selectedNode.length == 0 )
    {
        selectedNode = null;
    }

    var selected = selectedNode ? MenuList.menuTree().dataItem( selectedNode ) : null;
    if( selected && selected.id == menu_id ) {
        MenuList.menuTree().text( selectedNode, MenuForm.menuViewModel.get( 'name' ) );
    } else {
        var menu = {
            name: MenuForm.menuViewModel.get( 'name' ),
            id: MenuForm.menuViewModel.get( 'id' ),
            parent_id: selected ? selected.id : 0,
            hasChildren: false
        }
        MenuList.menuTree().append( menu, selectedNode );
    }
}


MenuList.menuSelected = function( e )
{
    var menu = MenuList.menuTree().dataItem( e.node );
    _menu_id = menu.id;
    _parent_id = menu.id;
    MenuForm.menuViewModel.set( 'parent_id', _parent_id );
    MenuList.menuSelectionChange( menu );
}

MenuList.menuSelectionChange = function( menu )
{
    if( menu )
    {
        $( '#editButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editButton' ).addClass( 'k-state-disabled' );
    }

    if( menu )
    {
        $( '#deleteButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deleteButton' ).addClass( 'k-state-disabled' );
    }
}


MenuList.menuDrop = function( e )
{
    setTimeout( function() {
        $.post( "/staff/saveMenuTree", { 'tree' : MenuList.menusForSave(null), _token: $('[name="_token"]').val() }, function( saved )
        {
        });
    }, 100 );
};

MenuList.menusForSave = function( nodes )
{
    var menus = [];
    var nodes = nodes != null ? nodes : MenuList.menuTree().dataSource.view();
    for( var i = 0; i < nodes.length; i++ )
    {
        var node = nodes[i];
        menus.push(
            {
                id: node['id'],
                name: node['name'],
                children: node['hasChildren'] ? MenuList.menusForSave( node['items'] ) : null,
                hasChildren: node['hasChildren']
            });
    }

    return menus;
}

MenuList.deleteMenu = function()
{
    var selectedNode = MenuList.menuTree().select();

    if( selectedNode.length > 0 ) {
        Utils.confirm().yesCallBack(function () {
            var selected = MenuList.menuTree().dataItem(selectedNode);
            $.post("/staff/deleteMenu", {menu_id: selected.id, _token: $('[name="_token"]').val()}, function (response) {
                response = JSON.parse(response);
                if(response.result == 1)
                {
                    MenuList.menuTree().remove(selectedNode);
                }
            });
        }).show('Confirm Delete', "Are you sure you want to delete the selected menu and it's sub menus?");
    }
}

MenuList.addListeners = function()
{
    $( '#addButton' ).click( MenuList.addMenu );
    $( '#editButton' ).click( MenuList.editMenu );
    $( '#deleteButton' ).click( MenuList.deleteMenu );

}

$( document ).ready( function()
{
    MenuList.initTree();
    MenuList.addListeners();
});