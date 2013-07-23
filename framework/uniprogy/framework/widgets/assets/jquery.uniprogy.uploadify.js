(function($) {
    $.fn.uUploadify = function(options) {
        var field = $(this);
        var arrImages = [];
        var arrBins=[];
        var defaultOptions = {
            'onComplete': function(e, q, f, r, d) {
                var re = $.parseJSON(r);                
                //console.log(typeof(re.image));
                //console.log(re);
                arrImages.push($.parseJSON(re.image));                                
                arrBins.push(re.bin);
                
                //var response=$.parseJSON(r);
                //abc.push(response.worklet);
                //process($.parseJSON(r));
                field.uploadifySettings("scriptData", {
                    "bin": binField().val()
                    });
                return true;
            },
            'onSelect': function(e, q, f) {
                field.uploadifySettings("scriptData", {
                    "bin": binField().val()
                    });
                return true;
            },
            'onError': function(a, b, c, d) {
                alert(d.info);
            },
            'onAllComplete': function(e, d) {                
                var response = {
                    worklet: {
                        content: {
                            appendReplace: '<script type="text/javascript"> /*<![CDATA[*/ $.uniprogy.picture.post.load(["'+arrImages.join('","')+'"],"'+arrBins.join(',')+'"); /*]]>*/ </script>'
                            }
                        }
                };                
        process(response);
                
        field.uploadifySettings("scriptData", {
            "bin": binField().val()
            });
        return true;                               
    }

};
options = $.extend(defaultOptions, options);
    field.uploadify(options);

    var process = function(data)
    {
        if (data.errors)
            return field.closest('form').uForm().errorSummary(data.errors);
        if (data.bin)
            binField().val(data.bin);
        if (data.content)
            binField().uUploadField().pushContent(data.content);
        if (data.close)
            $.uniprogy.dialogClose();
        if (data.worklet)
            field.closest('.worklet').uWorklet().process(data.worklet);
    };
     
    var binField = function()
    {
        return $('#' + options.binField);
    };
    return this;
}
})(jQuery);
