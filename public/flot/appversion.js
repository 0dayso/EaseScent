/*
  AppReport -- version distribution part
*/
$(document).ready(function () {
    $('#topTen').css({
        height: '200px',
        width: '1000px'
    });
    ajaxRequest(); 
    var type = $('#apptype').val();
    var dur = 0;
    var fixSrc = '/index.php?app=AppReport&m=AppVersionDistribu&a=verlist';
    var ifr = $('iframe#extra');
    ifr.attr('src',fixSrc+'&type='+type+'&dur='+dur);   

    //显示提示
    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y + -36,
            left: x + -6,
            border: '1px solid #fdd',
            padding: '4px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;

    $("#topTen").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;

                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);

                showTooltip(item.pageX, item.pageY, Math.round(y) + " " + item.series.label);
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });

    function onReceiveData(versions){
        var showfield = versions['showfield'];
        var versions = versions['ver'];
        // console.log(versions);
        var data = [];
        for( x in versions){
            data[x] = {};
            data[x]['data'] = [];
            for( i in versions[x]){
                data[x].label = 'V'+versions[x][i]['version'];
                data[x]['data'].push([versions[x][i]['date_time']*1000,parseInt(versions[x][i][showfield])]);
            }
        }
        // console.log(data);
        var i = 0;
        $.each(data, function(key, val) {
            val.color = i;
            ++i;
        }); 
        var options = {
                    lines: {
                        show: true
                    },        
                    points: {
                        show: true
                    },
                    grid: {            
                        borderWidth:1,
                        backgroundColor: '#fff',
                        hoverable: true
                    },
                    xaxis: {
                        mode: "time",
                        timeformat: "%m-%d"
                    },
                    yaxis:{
                        min:0
                    }
                };
        $.plot($("#topTen"),data,options);
    }

    /*
     * 各种查询
     *
     */
    $('#apptype').change(function(){
        ajaxRequest();
        var type = $('#apptype').val();
        var dur = 0;
        var fixSrc = '/index.php?app=AppReport&m=AppVersionDistribu&a=verlist';
        var ifr = $('iframe#extra');
        ifr.attr('src',fixSrc+'&type='+type+'&dur='+dur);
    });
    // 7/30/all days
    $('.days').each(function(){
        $(this).on('click',function(){
            $('input[type="hidden"][name="timesection"]').val($(this).attr('day'));
            ajaxRequest();
            var type = $('#apptype').val();
            var dur = $(this).attr('day');
            var fixSrc = '/index.php?app=AppReport&m=AppVersionDistribu&a=verlist';
            var ifr = $('iframe#extra');
            ifr.attr('src',fixSrc+'&type='+type+'&dur='+dur);
        });
    });
    // new-user active-user start-time,needed params : type{ios or android} ; time section {7 days or 30 days or all}
    $('input[type="radio"][name="rel"]').each(function(){
        $(this).on('click',function(){
            $('input[type="hidden"][name="cat"]').val($(this).attr('cat'));
            ajaxRequest();
        });
    });

    // all requests in common
    function ajaxRequest(){
        var type = $('#apptype').val();
        var cat = $('input[type="hidden"][name="cat"]').val();
        var timeSection = $('input[type="hidden"][name="timesection"]').val();
        $.ajax({
            url     :    'index.php?app=AppReport&m=AppVersionDistribu&a=getVersionData',
            type    :    'post',
            data    :    {type:type,cat:cat,timesection:timeSection},
            dataType:    'json',
            success :    function(versions){
                onReceiveData(versions);
            }
        });
    } 

    $('input[name="search"]').click(function(){
        var verno = $('select[name="vlist"]').val();
        var fixSrc = '/index.php?app=AppReport&m=AppVersionDistribu&a=verlist';
        var ifr = $('iframe#extra');
        ifr.attr('src',fixSrc+'&verno='+verno);
    })
});