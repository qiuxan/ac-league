var Utils = {};
var DateUtils = {};
var MapUtils = {};
var Grid = {};
var StringUtils = {};
var PageUtils;
var Notifier;
var Alert;
var Confirm;

Utils.date = function()
{
    return DateUtils;
}

Utils.maps = function()
{
    return MapUtils;
}

Utils.notifier = function()
{
    return new Notifier();
}

Utils.alert = function()
{
    return new Alert();
}

Utils.confirm = function()
{
    return new Confirm();
}

Utils.page = function()
{
    return new PageUtils();
}

Utils.grid = function()
{
    return Grid;
}

Utils.string = function()
{
    return StringUtils;
}

Utils.getSelectedAutoCompleteId = function( elementId, prop, idField )
{
    if( !$( "#" + elementId ).data( "kendoAutoComplete" ) )
    {
        return 0;
    }

    var val = $( "#" + elementId ).data( "kendoAutoComplete" ).value();
    var data = $( "#" + elementId ).data( "kendoAutoComplete" )['dataSource'].data();
    var id = 0;
    for( var i = 0; i < data.length; i++ )
    {
        if( data[i][prop] == val )
        {
            id = parseInt( data[i][idField] );
            break;
        }
    }

    return id;
};

DateUtils.mysqlDateToDate = function( mysqlDateString )
{
    var year = mysqlDateString.substring( 0, 4 );
    var month = mysqlDateString.substring( 5, 7 ) - 1;
    var date = mysqlDateString.substring( 8, 10 );

    return new Date( year, month, date );
}

DateUtils.mysqlDateTimeToDate = function( mysqlDateTimeString )
{
    var year = mysqlDateTimeString.substring( 0, 4 );
    var month = mysqlDateTimeString.substring( 5, 7 ) - 1;
    var date = mysqlDateTimeString.substring( 8, 10 );
    var hour = mysqlDateTimeString.substring( 11, 13 );
    var minute = mysqlDateTimeString.substring( 14, 16 );
    var second = mysqlDateTimeString.substring( 17, 19 );

    return new Date( year, month, date, hour, minute, second );
}

DateUtils.mysqlGMTDateTimeToDate = function( mysqlDateTimeString )
{
    return moment( mysqlDateTimeString + 'Z', 'YYYY-MM-DD HH:mm:ssZ' );
}

DateUtils.formatMySqlGMTDateTime = function( mysqlDateTimeString, format )
{
    if( !mysqlDateTimeString || mysqlDateTimeString == '0000-00-00 00:00:00' || mysqlDateTimeString == '' ) {
        return '';
    }
    var strFormat = format ? format : 'ddd Do MMM h:mmA';
    return moment( mysqlDateTimeString + 'Z', 'YYYY-MM-DD HH:mm:ssZ' ).format( strFormat )
}

DateUtils.formatMySqlGMTTime = function( mysqlTimeString, format )
{
    if( !mysqlTimeString || mysqlTimeString == '' ) {
        return '';
    }
    var strFormat = format ? format : 'h:mm a';
    return moment( Utils.date().todayMySQL() + ' ' + mysqlTimeString + 'Z', 'YYYY-MM-DD HH:mm:ssZ' ).format( strFormat )
}

DateUtils.mySQLGMTLToLocalTime = function( gmtTimeString )
{
    return moment( Utils.date().todayMySQL() + ' ' + gmtTimeString + 'Z', 'YYYY-MM-DD HH:mm:ssZ').format( 'h:mm a' )
}

DateUtils.localDateToMySQLGMTTime = function( date )
{
    return moment( date ).utc().format( 'HH:mm:ss' )
}

DateUtils.localDateToMySQLGMTDateTime = function( date )
{
    return moment( date ).utc().format( 'YYYY-MM-DD HH:mm:ss' )
}

DateUtils.nowMySQL = function()
{
    var today = new Date();
    return DateUtils.dateToMySQLDateTime( today );
}

DateUtils.todayMySQL = function()
{
    var today = new Date();
    return today.getFullYear() + '-' + DateUtils.dateLeadingZero( today.getMonth() + 1 ) + '-' + DateUtils.dateLeadingZero( today.getDate() );
}

DateUtils.dateToMySQLDateTime = function( date )
{
    if( date ) {
        return date.getFullYear() + '-' + DateUtils.dateLeadingZero(date.getMonth() + 1) + '-' + DateUtils.dateLeadingZero(date.getDate()) + ' ' + DateUtils.dateLeadingZero(date.getHours()) + ':' + DateUtils.dateLeadingZero(date.getMinutes()) + ':' + DateUtils.dateLeadingZero(date.getSeconds());
    }

    return '';
}

DateUtils.dateToMySQLDate = function( date )
{
    if( date ) {
        return date.getFullYear() + '-' + DateUtils.dateLeadingZero( date.getMonth() + 1 ) + '-' + DateUtils.dateLeadingZero( date.getDate() );
    }

    return '';
}

DateUtils.dateLeadingZero = function( date )
{
    if( date < 10 )
    {
        return '0' + date;
    }

    return date;
}

DateUtils.formatAMPM = function( date )
{
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    return hours + ':' + minutes + ' ' + ampm;
}

DateUtils.dateDiff = function( dateA, dateB )
{
    var millisecondsInDay = 1000 * 60 * 60 * 24;

    var utcA = Date.UTC(dateA.getFullYear(), dateA.getMonth(), dateA.getDate());
    var utcB = Date.UTC(dateB.getFullYear(), dateB.getMonth(), dateB.getDate());

    return Math.floor((utcB - utcA) / millisecondsInDay);
}

MapUtils.mapDataToAddressObject = function( data )
{
    var address;
    address = {
        addressLine1: '',
        city: '',
        state: '',
        postcode: '',
        country: '',
        numberNotFound: false
    };

    var streetNumberAvailable = false;

    for( var i = 0; i < data['address_components'].length; i++ )
    {
        var addressComponent = data['address_components'][i];
        if( addressComponent['types'][0] == 'subpremise' )
        {
            address['addressLine1'] = addressComponent['long_name'] + '/';
        }
        if( addressComponent['types'][0] == 'street_number' )
        {
            streetNumberAvailable = true;
            address['addressLine1'] += addressComponent['long_name'];
        }
        else if( addressComponent['types'][0] == 'route' )
        {
            address['addressLine1'] += ' ' + addressComponent['long_name'];
        }
        else if( addressComponent['types'][0] == 'locality' )
        {
            address['city'] = addressComponent['long_name'];
        }
        else if( addressComponent['types'][0] == 'administrative_area_level_1' )
        {
            address['state'] = addressComponent['short_name'];
        }
        else if( addressComponent['types'][0] == 'postal_code' )
        {
            address['postcode'] = addressComponent['long_name'];
        }
        else if( addressComponent['types'][0] == 'country' )
        {
            address['country'] = addressComponent['long_name'];
        }
    }

    if( !streetNumberAvailable )
    {
        address['addressLine1'] = '';
        address['numberNotFound'] = true;
    }

    return address;
}

StringUtils.humanFileSize = function(bytes) {
    var thresh = 1000;
    if(bytes < thresh) return bytes + ' B';
    var units = ['kB','MB','GB','TB','PB','EB','ZB','YB'];
    var u = -1;
    do {
        bytes /= thresh;
        ++u;
    } while(bytes >= thresh);
    var fixed = units[u] == 'kB' ? 0 : 1;
    return bytes.toFixed(fixed)+' '+units[u];
}

StringUtils.nl2br = function( str ) {
    var breakTag = '<br />';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

StringUtils.leadingZeros = function( number, strLength ) {
    while(number.length < strLength){
        number = "0" + number;
    }

    return number;
}

StringUtils.ucFirst = function( str ) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

Notifier = function()
{
    this.statusElement = null;
    var self = this;

    this.status = function( statusElement )
    {
        if( statusElement != undefined )
        {
            self.statusElement = statusElement;
        }

        return self.statusElement
            .text( '' )
            .removeClass()
            .css( 'display', '' );
    }

    this.newSpan = function()
    {
        return $( document.createElement( 'span' ) );
    }

    this.notifyProgress = function( message )
    {
        self.status().html( this.newSpan().text( message).addClass( 'progress' ) );
    }

    this.notifyComplete = function( message )
    {
        var span = this.newSpan().text( message ).addClass( 'valid' );
        self.status().html( span );
        span.fadeOut( 2000 );
    }

    this.notifyError = function( message )
    {
        var span = this.newSpan().text( message ).addClass( 'invalid' );
        self.status().html( span );
        span.delay( 5000 ).fadeOut( 2000 );
    }

    this.clear = function()
    {
        self.status().text( '' );
    }
}

Grid.filterValue = function( app, grid, filterName, onValue )
{
    $.getJSON( '/json/framework/grid/getFilter', { app: app, grid: grid, filterName: filterName }, function( filter )
    {
        if( onValue != undefined )
        {
            onValue( filter['filterValue'] );
        }
    });
}

Grid.gridFilters = function( app, grid, onValues )
{
    $.getJSON( '/json/framework/grid/getFilters', { app: app, grid: grid }, function( filters )
    {
        if( onValues != undefined )
        {
            onValues( filters );
        }
    });
}

Grid.saveFilters = function( filters, onSave )
{
    $.getJSON( '/json/framework/grid/saveFilters', { filters: filters }, function()
    {
        if( onSave != undefined )
        {
            onSave();
        }
    });
}

Grid.gridColumns = function( app, grid, onValues )
{
    $.getJSON( '/json/framework/grid/getColumns', { app: app, grid: grid }, function( columns )
    {
        if( onValues != undefined )
        {
            onValues( columns );
        }
    });
}

Grid.saveColumn = function( column, onSave )
{
    $.getJSON( '/json/framework/grid/saveColumn', { column: column }, function()
    {
        if( onSave != undefined )
        {
            onSave();
        }
    });
}

Alert = function()
{
    this.alertDiv = $( document.createElement( 'div' ) );
    this._okCallBack = null;
    var self = this;

    this.show = function( title, message )
    {
        self.alertDiv
            .html( self.message( message ) )
            .append( self.okButton() )
            .kendoWindow({
                title: title,
                close: self.close,
                modal: true
            });

        self.kendoWindow().center();
        self.kendoWindow().open();
    }

    this.message = function( message )
    {
        return $( document.createElement( 'div' ) )
            .css( 'padding', '20px' )
            .html( message );
    }

    this.okButton = function()
    {
        return $( document.createElement( 'div' ) )
            .css( 'text-align', 'center' )
            .html
            (
                $( document.createElement( 'button' ) )
                    .addClass( 'k-button' )
                    .html( 'OK' )
                    .click( self.callBack )
            );
    }

    this.close = function()
    {
        self.kendoWindow().destroy();
        self.alertDiv.remove();
    }

    this.okCallBack = function( callBack )
    {
        self._okCallBack = callBack;
        return self;
    }

    this.kendoWindow = function()
    {
        return self.alertDiv.data( 'kendoWindow' );
    }

    this.callBack = function()
    {
        if( self._okCallBack )
        {
            self._okCallBack();
        }

        self.close();
    }
}

Confirm = function()
{
    this.confirmDiv = $( document.createElement( 'div' ) );
    this._yesCallBack = null;
    this._noCallBack = null;
    this._yesIsDefault = false;
    this._noIsDefault = false;
    var self = this;

    this.show = function( title, message )
    {
        var uid = new Date().getTime() + 'confirm';
        self.confirmDiv
            .html( self.message( message ) )
            .append( self.buttons( uid ) )
            .kendoWindow({
                title: title,
                close: self.callBackNo,
                modal: true,
                open: function() {
                    self.focusOnInput( uid );
                }
            }).focusout( function() {
            self.focusOnInput( uid );
        });

        self.kendoWindow().center();
        self.kendoWindow().open();
    }

    this.focusOnInput = function( uid ) {
        if( !$( '#' + uid ).is( ':focus' ) ) {
            $( '#' + uid ).focus();
            setTimeout( function() {
                self.focusOnInput( uid )
            }, 50 );
        }
    }

    this.message = function( message )
    {
        return $( document.createElement( 'div' ) )
            .css( 'padding', '20px' )
            .html( message );
    }

    this.buttons = function( uid )
    {
        return $( document.createElement( 'div' ) )
            .css( 'text-align', 'center' )
            .append
            (
                $( document.createElement( 'button' ) )
                    .addClass( 'k-button' )
                    .css( 'margin-right', '10px')
                    .css( 'min-width', '100px')
                    .html( 'No' )
                    .click( self.callBackNo )
            )
            .append
            (
                $( document.createElement( 'button' ) )
                    .addClass( 'k-button' )
                    .css( 'margin-right', '10px')
                    .css( 'min-width', '100px')
                    .html( 'Yes' )
                    .click( self.callBackYes )
            ).append( self.hiddenInput( uid ) );
    }

    this.hiddenInput = function( uid )
    {
        return $( document.createElement( 'input' ) )
            .prop( 'id', uid )
            .prop( 'type', 'text' )
            .css( 'border', 'none' )
            .css( 'display', 'none' )
            .css( 'width', '0px' )
            .keyup(function(event) {
                if (event.keyCode == 13) {
                    if (self._yesIsDefault) {
                        self.callBackYes();
                    } else if (self._noIsDefault) {
                        self.callBackNo();
                    }
                }
            });
    }

    this.close = function()
    {
        self.kendoWindow().destroy();
        self.confirmDiv.remove();
    }

    this.kendoWindow = function()
    {
        return self.confirmDiv.data( 'kendoWindow' );
    }

    this.yesCallBack = function( callBack )
    {
        self._yesCallBack = callBack;
        return self;
    }

    this.noCallBack = function( callBack )
    {
        self._noCallBack = callBack;
        return self;
    }

    this.yesIsDefault = function()
    {
        self._yesIsDefault = true;
        return self;
    }

    this.noIsDefault = function()
    {
        self._noIsDefault = true;
        return self;
    }

    this.callBackYes = function()
    {
        if( self._yesCallBack )
        {
            self._yesCallBack();
        }

        self.close();
    }

    this.callBackNo = function()
    {
        if( self._noCallBack )
        {
            self._noCallBack();
        }

        self.close();
    }
}

PageUtils = function() {
    var self = this;

    this.showLoading = function(message) {
        var loadingDiv = $(document.createElement('div'))
            .prop('id', 'page-utils-loading')
            .append(
                $(document.createElement('div'))
                    .prop('id', 'page-utils-loading-inner')
                    .html(message)
            );

        $('body').append(loadingDiv);
    }

    this.hideLoading = function() {
        $('#page-utils-loading').remove();
    }

    this.insertAtCaret = function(id, text){
        var textArea = document.getElementById(id);
        if (!textArea) { return; }

        var scrollPos = textArea.scrollTop;
        var strPos = 0;
        var br = ((textArea.selectionStart || textArea.selectionStart == '0') ?
            "ff" : (document.selection ? "ie" : false ) );
        if (br == "ie") {
            textArea.focus();
            var range = document.selection.createRange();
            range.moveStart ('character', -textArea.value.length);
            strPos = range.text.length;
        } else if (br == "ff") {
            strPos = textArea.selectionStart;
        }

        var front = (textArea.value).substring(0, strPos);
        var back = (textArea.value).substring(strPos, textArea.value.length);
        textArea.value = front + text + back;
        strPos = strPos + text.length;
        if (br == "ie") {
            textArea.focus();
            var ieRange = document.selection.createRange();
            ieRange.moveStart ('character', -textArea.value.length);
            ieRange.moveStart ('character', strPos);
            ieRange.moveEnd ('character', 0);
            ieRange.select();
        } else if (br == "ff") {
            textArea.selectionStart = strPos;
            textArea.selectionEnd = strPos;
            textArea.focus();
        }

        textArea.scrollTop = scrollPos;
    }
}