!function(a){a.fn.archivesCW=function(b){return b&&"object"!=typeof b||(b=a.extend({},a.fn.archivesCW.defaults,b)),this.each(function(){new $archivesCW(a(this),b)})},$archivesCW=function(b,c){function d(){i==j-1?b.find(".prev-year").addClass("disabled"):b.find(".prev-year").removeClass("disabled"),0==i?b.find(".next-year").addClass("disabled"):b.find(".next-year").removeClass("disabled")}function e(c,e){var g=i;c<g?a.isFunction(e.goNext)?e.goNext(b,g,c):a.fn.archivesCW.defaults.goNext(b,g,c):a.isFunction(e.goPrev)?e.goPrev(b,g,c):a.fn.archivesCW.defaults.goPrev(b,g,c),i=c;var h=b.find(".menu a[rel="+i+"]");b.find('a.title:not([href="#"])').attr("href",h.attr("href")).html(h.html()),d(),f()}function f(){h.find("a.selected, a[rel="+i+"]").toggleClass("selected");var a=h.find("a.selected").parent();h.css("top",-a.index()*parseInt(g.outerHeight()))}var g=b.find(".calendar-navigation"),h=g.find(".menu"),i=parseInt(h.find("a.current").attr("rel")),j=h.find("li").length;j<=1&&g.find(".arrow-down").hide(),f(),d(),g.find(".prev-year").on("click",function(b){b.preventDefault(),a(this).is(".disabled")||e(i+1,c)}),g.find(".next-year").on("click",function(b){b.preventDefault(),a(this).is(".disabled")||e(i-1,c)}),g.find(".arrow-down").on("click",function(){a.isFunction(c.showDropdown)&&c.showDropdown(h)}),g.find('a[href="#"]').on("click",function(){a.isFunction(c.showDropdown)&&c.showDropdown(h)}),h.mouseleave(function(){var b=a(this);a(this).data("timer",setTimeout(function(){a.isFunction(c.hideDropdown)&&c.hideDropdown(b)},300))}).mouseenter(function(){a(this).data("timer")&&clearTimeout(a(this).data("timer"))}),h.find("a").on("click",function(b){if(b.preventDefault(),!a(this).is(".selected")){a(this).removeClass("selected");var d=parseInt(a(this).attr("rel"));e(d,c),a.isFunction(c.hideDropdown)&&c.hideDropdown(h)}})},a.fn.archivesCW.defaults={goNext:function(a,b,c){a.find(".year").css({"margin-left":"-100%",opacity:1}),a.find(".year[rel="+b+"]").css({"margin-left":0,"z-index":2}).animate({opacity:.5},300),a.find(".year[rel="+c+"]").css({"z-index":3}).animate({"margin-left":0})},goPrev:function(a,b,c){a.find(".year:not(.last)").css({"margin-left":"-100%",opacity:1}),a.find(".year[rel="+c+"]").css({"margin-left":0,opacity:.3,"z-index":2}).animate({opacity:1},300),a.find(".year[rel="+b+"]").css({"margin-left":0,"z-index":3}).animate({"margin-left":"-100%"})},showDropdown:function(a){a.show()},hideDropdown:function(a){a.hide()}}}(jQuery);