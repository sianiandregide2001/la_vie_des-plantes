jQuery(document).ready(function($) {

    // show dialog when user clicks info icon
    $( '.epsi-info-icon' ).on( 'click' , function() {

        var has_image = false;
        var img = '';
        var title = $( this ).parent().find('.epsi-label').text();
        title = ( title ? 'OPTION: ' + title  : '' );

        var msg = $( this ).find( 'p' ).html();
        if ( msg )
        {
            var arrayOfStrings = msg.split('@');
            msg = arrayOfStrings[0] ? arrayOfStrings[0] : 'Help text coming soon.';
            if ( arrayOfStrings[1] ) {
                has_image = true;
                img = '<img src="' + arrayOfStrings[1] + '">';
            }
        } else {
            msg = 'Help text coming soon.';
        }

        $('#epsi-dialog-info-icon-msg').html( '<p>' + msg + '</p><br/>' + img);
        $("#epsi-dialog-info-icon").dialog('option', { title: title, height: (has_image ? 850 : 200), width: (has_image ? 1000 : 400)} )
                                    .dialog("open");
    });
    
    //Tabs
    (function(){

        var tabContainer = $('#epsi-tabs');
        var navTabsLi    = $('.nav-tabs li');
        var tabPanel     = $('.tab-panel');

        // Tab Functionality
        tabContainer.find( navTabsLi ).each(function(){

            $(this).on('click', function (){

                tabContainer.find( navTabsLi ).removeClass('active');

                $(this).addClass('active');

                tabContainer.find( tabPanel).removeClass('active');
                changePanels ( $(this).index() );

            });
        });

        function changePanels( Index ){
            var number = Index + 1;
            $('.panel-container .tab-panel:nth-child('+number+')').addClass('active');
        }

    })();

    $('#epsi-dialog-info-icon').dialog({
        resizable: false,
        modal: true,
        autoOpen: false,
        buttons: {
            Ok: function ()
            {
                $(this).dialog("close");
            }
        },
        open: function(event, ui) { $('.ui-widget-overlay').bind('click', function(){ $("#epsi-dialog-info-icon").dialog('close'); }); }
    }).hide();

});