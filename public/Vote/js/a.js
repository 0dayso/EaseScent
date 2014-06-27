var VOTE = {
    Vote : function(str){
        var theme = str.Theme;
        var marks;
        $.each($("*[VoteTheme='"+theme+"'][VoteBtn]"),function(i,n){
            var mark = $(n).attr("VoteBtn");
            if(mark)	marks = marks ? marks+','+mark : mark;
        });
        if(str.getvotes){
            $.getJSON('http://ajax.wine.g/?action=RH1mdz1TeHNqPXV3ZkR9Zndh&callback=?',{'theme':theme,'marks':marks}, function(msg){
                str.getvotes(msg);
            });
        }
        if(str.btnclick){
            $.each($("*[VoteTheme='"+theme+"'][VoteBtn]"),function(i,n){
                $(n).click(function(){
                    var mark = $(n).attr("VoteBtn");
                    $.getJSON('http://ajax.wine.g/?action=RH1mdz1TeHNqPXFzYWZEfWZ3&callback=?',{'theme':theme,'mark':mark}, function(msg){
                        str.btnclick(msg);
                    });
                });
            });
        }
    }
};
