/**
 * Created by cph2 on 7/7/17.
 */
$("a[rel=IMG_78_38_31Group]").fancybox({
    helpers : {
        thumbs : {
            width: 50,
            height: 50,
            autoStart : true,
            nextEffect  : 'fade',
            prevEffect  : 'fade',
            padding     : 0,
            margin      : [15, 15, 40, 15],
            beforeShow   : addLinks,
            beforeClose : removeLinks
        }
    }
});


function addLinks() {
    var list = $("#links");

    if (!list.length) {
        list = $('<ul id="links">');

        for (var i = 0; i < this.group.length; i++) {
            $('<li data-index="' + i + '"><label></label></li>').click(function() { $("a[rel=IMG_78_38_31Group]").fancybox.jumpto( $(this).data('index'));}).appendTo( list );
        }

        list.appendTo( 'body' );
    }

    list.find('li').removeClass('active').eq( this.index ).addClass('active');
}

function removeLinks() {
    $("#links").remove();
}
