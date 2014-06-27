var showDiv="#sales";
var showHeight = showHeight ? showHeight : '530px';
$(document).ready(function () {
    $('#sales').css({
        height: showHeight,
        width: 'auto'
    });

    var register_user = [[1, 125], [2, 258], [3, 121], [4, 132], [5, 126], [6, 189], [7, 358], [8, 234], [9, 201], [10, 160], [11, 180], [12, 195], [13, 232], [14, 233], [15, 212]];
    var approve_user = [[1, 356], [2, 345], [3, 452], [4, 345], [5, 325], [6, 424], [7, 478], [8, 464], [9, 558], [10, 443], [11, 562], [12, 535], [13, 342], [14, 348], [15, 402]];
    var pay_user = [[1, 36], [2, 35], [3, 52], [4, 34], [5, 25], [6, 44], [7, 78], [8, 64], [9, 58], [10, 43], [11, 52], [12, 135], [13, 142], [14, 48], [15, 22]];

    var plot = $.plot($(showDiv), [
		{ label: "注册会员", data: register_user },
		{ label: "认证会员", data: approve_user },
        { label: "付费会员", data: pay_user }
	], {
		lines: {
			show: true
		},
		points: {
			show: true
		},
		grid: {
			backgroundColor: '#fffaff',
			hoverable: true,
			clickable: true
		},
		legend: {
			show: false
		}
	});

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

    $(showDiv).bind("plothover", function (event, pos, item) {
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
});